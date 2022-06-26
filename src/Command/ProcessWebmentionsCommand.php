<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Cache\Cache;

use App\Model\Entity\Webmention;

/**
 * ProcessWebmentions command.
 */
class ProcessWebmentionsCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return null|void|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->Webmentions = $this->fetchTable('Webmentions');
        $this->Comments = $this->fetchTable('Comments');
        $this->findPendingWebmentions();
    }

    protected function findPendingWebmentions()
    {
        $mentions = $this->Webmentions->find()
            ->where([
                'status' => 'pending'
            ])
            ->all();

        if (!$mentions || count($mentions) === 0) {
            return;
        }

        foreach ($mentions as $mention) {
            $this->processWebmention($mention);
        }
    }

    private function processWebmention(Webmention $mention): void
    {
        $html = Cache::read($mention->source, 'webmentions');

        if (!$html) {
            // also yanked from https://github.com/janboddez/iw-utils/blob/main/app/Jobs/ProcessWebmentions.php
            $context = stream_context_create(['http' => [
                'follow_location' => true,
                'ignore_errors' => true, // Don't choke on HTTP (4xx, 5xx) errors.
                'timeout' => 15,
            ]]);

            $html = @file_get_contents($mention->source, false, $context);
            Cache::write($mention->source, 'webmentions');
        }

        if (strpos($html, $mention->target) === false) {
            $mention->set('status', 'invalid');
            $this->Webmentions->save($mention);
            return;
        }

        $exists = false;

        if ($mention->type === 'post') {
            $posts = $this->fetchTable('Posts');
            $exists = $posts->findById($mention->type_id);
        } else if ($mention->type === 'media') {
            $medias = $this->fetchTable('Medias');
            $exists = $medias->findById($mention->type_id);
        }

        if (!$exists) {
            $mention->set('status', 'inavlid');
            $this->Webmentions->save($mention);
            return;
        }

        // $host = parse_url($mention->source, PHP_URL_HOST);

        $commentData = [
            'model_id' => $mention->type_id,
            'comment' => 'I mentioned your post on my website!',
            'approved' => false,
            'public' => true,
            'type' => 'mention',
            'posted_by' => $mention->source,
            'display_name' => 'Anonymous',
            'url' => $mention->source,
        ];

        $this->parseMicroformats($commentData, $html, $mention);

        $existing = $this->Comments->find()
            ->where([
                'model_id' => $mention->type_id,
                'posted_by' => $mention->source,
            ])
            ->first();

        if ($existing) {
            $mention->set('status', 'updated');
            $saveComment = $this->Comments->patchEntity($existing, $commentData);
        } else {
            $mention->set('status', 'created');
            $saveComment = $this->Comments->newEntity($commentData);
        }

        $this->Comments->save($saveComment);

        $this->Webmentions->save($mention);
    }

    // everything below here taken from https://github.com/janboddez/iw-utils/blob/main/app/Jobs/ProcessWebmentions.php
    // and tweaked to work with CakePHP
    protected function parseMicroformats(array &$data, string $html, Webmention $webmention): void
    {
        $mf = \Mf2\parse($html, $webmention->source);

        if (empty($mf['items'][0]['type'][0])) {
            // No relevant microformats found. Leave `$comment` untouched.
            return;
        }

        if ($mf['items'][0]['type'][0] === 'h-entry') {
            // Topmost item is an h-entry. Let's try to parse it.
            $this->parseHentry($data, $mf['items'][0], $webmention);
            return;
        } elseif ($mf['items'][0]['type'][0] === 'h-feed') {
            // Topmost item is an h-feed.
            if (empty($mf['items'][0]['children'])) {
                return;
            }

            if (! is_array($mf['items'][0]['children'])) {
                return;
            }

            // Loop through its children, and parse (only) the first h-entry we
            // encounter.
            foreach ($mf['items'][0]['children'] as $child) {
                if (empty($child['type'][0])) {
                    continue;
                }

                if ($child['type'][0] !== 'h-entry') {
                    continue;
                }

                $this->parseHentry($data, $child, $webmention);
                return;
            }
        }
    }

    protected function parseHentry(array &$data, array $hentry, object $webmention): void
    {
        // Update author name.
        if (! empty($hentry['properties']['author'][0]['properties']['name'][0])) {
            $data['display_name'] = $hentry['properties']['author'][0]['properties']['name'][0];
        }

        // Update author URL.
        if (! empty($hentry['properties']['author'][0]['properties']['url'][0])) {
            $data['url'] = $hentry['properties']['author'][0]['properties']['url'][0];
        }

        // Update comment datetime.
        if (! empty($hentry['properties']['published'][0])) {
            $data['created'] = date('Y-m-d H:i:s', strtotime($hentry['properties']['published'][0]));
        }

        $postType = 'mention';

        if (! empty($hentry['properties']['in-reply-to']) && in_array($webmention->target, (array) $hentry['properties']['in-reply-to'], true)) {
            $postType = 'reply';
        }

        if (! empty($hentry['properties']['repost-of']) && in_array($webmention->target, (array) $hentry['properties']['repost-of'], true)) {
            $postType = 'repost';
        }

        if (! empty($hentry['properties']['bookmark-of']) && in_array($webmention->target, (array) $hentry['properties']['bookmark-of'], true)) {
            $postType = 'bookmark';
        }

        if (! empty($hentry['properties']['like-of']) && in_array($webmention->target, (array) $hentry['properties']['like-of'], true)) {
            $postType = 'like';
        }

        // Temporarily store unaltered content.
        $comment = $data['comment'];

        // Overwrite default content based on post type.
        switch ($postType) {
            case 'bookmark':
                $comment = '&hellip; bookmarked this!';
                break;

            case 'like':
                $comment = '&hellip; liked this!';
                break;

            case 'repost':
                $comment = '&hellip; reposted this!';
                break;

            case 'mention':
            case 'reply':
            default:
                if (! empty($hentry['properties']['content'][0]['value']) && mb_strlen($hentry['properties']['content'][0]['value'], 'UTF-8') <= config('max_length', 500)
                 && ! empty($hentry['properties']['content'][0]['html'])) {
                    // If the mention is short enough, store it in its entirety.
                    $comment = strip_tags($hentry['properties']['content'][0]['html']);
                } else {
                    // Fetch the bit of text surrounding the link to our page.
                    $context = $this->fetchContext($hentry['properties']['content'][0]['html'], $webmention->target);

                    if (! empty($context)) {
                        // Found context, now store it.
                        $comment = $context;
                    } elseif (! empty($hentry['properties']['content'][0]['html'])) {
                        // Simply store an excerpt of the webmention source.
                        $comment = \Cake\Utility\Text::truncate(strip_tags($hentry['properties']['content'][0]['html']), 500);
                    }
                }
        }

        $data['comment'] = $comment;
        $data['type'] = $postType;
    }

    /**
     * Looks for a link to `$target`, and returns some of the text surrounding
     * it.
     *
     * Lifted pretty much straight from WordPress.
     */
    protected function fetchContext(string $html, string $target): string
    {
        // Work around bug in `strip_tags()`.
        $html = str_replace('<!DOC', '<DOC', $html);
        $html = preg_replace('/[\r\n\t ]+/', ' ', $html);
        $html = preg_replace('/<\/*(h1|h2|h3|h4|h5|h6|p|th|td|li|dt|dd|pre|caption|input|textarea|button|body)[^>]*>/', "\n\n", $html);

        // Remove all script and style tags, including their content.
        $html = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $html);
        // Just keep the tag we need.
        $html = strip_tags($html, '<a>');

        $p = explode("\n\n", $html);

        $preg_target = preg_quote($target, '|');
        $excerpt = '';

        foreach ($p as $para) {
            if (strpos($para, $target) !== false) {
                preg_match('|<a[^>]+?' . $preg_target . '[^>]*>([^>]+?)</a>|', $para, $context);

                if (empty($context)) {
                    // The URL isn't in a link context; keep looking.
                    continue;
                }

                // We're going to use this fake tag to mark the context in a
                // bit. The marker is needed in case the link text appears more
                // than once in the paragraph.
                $excerpt = preg_replace('|\</?wpcontext\>|', '', $para);

                // Prevent really long link text.
                if (mb_strlen($context[1]) > 100) {
                    $context[1] = mb_substr($context[1], 0, 100) . '&#8230;';
                }

                $marker = '<wpcontext>' . $context[1] . '</wpcontext>'; // Set up our marker.
                $excerpt = str_replace($context[0], $marker, $excerpt);  // Swap out the link for our marker.
                $excerpt = strip_tags($excerpt, '<wpcontext>');          // Strip all tags but our context marker.
                $excerpt = trim($excerpt);
                $preg_marker = preg_quote($marker, '|');
                $excerpt = preg_replace("|.*?\s(.{0,200}$preg_marker.{0,200})\s.*|s", '$1', $excerpt);
                $excerpt = strip_tags($excerpt);

                break;
            }
        }

        if (empty($context)) {
            // Link to target not found.
            return '';
        }

        return '[&#8230;] ' . h($excerpt) . ' [&#8230;]';
    }
}
