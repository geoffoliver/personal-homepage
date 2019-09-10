<?php

namespace App\Command;

use App\Lib\ImportUtils;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * ImportTwitterData command.
 */
class ImportTwitterDataCommand extends Command
{
    private $user;
    private $path;
    private $photosDir;
    private $albumsDir;
    private $videosDir;
    private $postsDir;

    private $stats = [
        'media' => [
            'success' => 0,
            'fail' => 0
        ],
        'posts' => [
            'success' => 0,
            'fail' => 0
        ],
    ];

    public function initialize()
    {
        parent::initialize();
        // load up some models
        $this->loadModel('Medias');
        $this->loadModel('Posts');
        $this->loadModel('Albums');
        $this->loadModel('Users');
    }

    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/3.0/en/console-and-shells/commands.html#defining-arguments-and-options
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser)
    {
        $parser = parent::buildOptionParser($parser);

        // we always need a path to the FB data
        $parser->addArgument('path', [
            'help' => __('The path to your _unzipped_ Twitter data. Just unzip the main archive, not any of the zips contained within it.'),
            'required' => true
        ]);

        $parser->addArgument('email', [
            'help' => __('The email address of the user you want to import the data for. If not provided, the first (oldest) user in the database will be used.')
        ]);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return null|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        // tell the user what's going on
        $io->out(__('Importing Twitter data...'));

        // get the actual path (in case the user enters something like ./tmp/twitter-data)
        $path = realpath($args->getArgument('path'));

        $user = $this->Users->find();

        if ($email = $args->getArgument('email')) {
            $user = $user->where([
                'Users.email' => $email
            ]);
        } else {
            $user = $user->order([
                'Users.created' => 'DESC'
            ]);
        }

        $user = $user->first();
        if (!$user) {
            $io->error(__('Unable to locate user'));
            return;
        }

        $this->user = $user;

        // make sure the path exists
        if (!ImportUtils::checkPath($path, $io)) {
            return;
        }

        // setup some directories
        $this->path = $path;
        $this->mediaDir = $this->path . DS . 'tweet_media';

        // give a little output
        $io->out(__('Processing data...'));

        // import posts
        $this->importPosts($io);

        // spit out a little report
        $io->out('|----------------------------------|');
        $io->out('| Item     | Successes  | Failures |');
        $io->out('|----------------------------------|');
        foreach ($this->stats as $type => $stats) {
            $io->out(
                '| ' .
                    str_pad(ucfirst($type), 9, ' ') . '| ' .
                    str_pad(number_format($stats['success']), 10, ' ') . '| ' .
                    str_pad(number_format($stats['fail']), 8, ' ') .
                    ' |'
            );
        }
        $io->out('|----------------------------------|');

        // all done
        $io->success(__('Done!'));
    }

    private function importPosts($io)
    {
        $io->out(__('Importing tweets...'));

        // this is where tweets _should_ live
        $tweetFile = $this->path . DS . 'tweet.js';

        // make sure we can work with the tweet file
        if (!ImportUtils::checkFile($tweetFile, $io)) {
            return;
        }

        // grab the contents of the file that contains tweets
        $tweetFileContent = file_get_contents($tweetFile);

        if (!$tweetFileContent) {
            $io->error(__('Tweet file is empty'));
            return;
        }

        // strip some bits away so we can decode the string into
        // a JSON object that we can do stuff with
        $io->out(__('Converting tweet file to JSON object...'));
        $lines = explode("\n", $tweetFileContent);
        // remove the first and last lines
        array_shift($lines);
        array_pop($lines);
        // replace the first and last lines to make this a valid JSON array
        array_unshift($lines, '[{');
        $lines[]= '}]';
        // turn the lines back into a big ass string
        $tweetFileContent = implode("\n", $lines);

        $io->out(__('Decoding tweet JSON...'));
        $tweetJson = json_decode($tweetFileContent);

        if (!$tweetJson || !is_array($tweetJson)) {
            $io->error(__('Unable to decode tweet json!'));
            return;
        }

        // loop over the tweets and import each one of them
        foreach ($tweetJson as $tweet) {
            $this->importTweet($tweet, $io);
        }

        $io->success(__('Importing tweets complete!'));
    }

    private function importTweet($tweet, $io)
    {
        $io->out(__('Importing tweet {0}', $tweet->id_str));

        // Set a default title. This could change based on whether this tweet
        // is a reply to another tweet, a retweet, or just a regular update.
        $title = __('Posted a tweet');

        // set some default content, which could change depending on the
        // type of tweet being imported.
        $content = $tweet->full_text;

        // set the created (and modified) based on the tweet's created_at
        $created = date('Y-m-d H:i:s', strtotime($tweet->created_at));

        // assume this is just a regular tweet
        $source = null;

        // is this tweet a reply to another tweet?
        $isReply = (
            property_exists($tweet, 'in_reply_to_status_id') &&
            $tweet->in_reply_to_status_id
        );

        // is this tweet a retweet? pretty dumb for now, but it works for
        // probably most of the RTs that i've done so... :shrug:
        $isRetweet = strpos($tweet->full_text, 'RT @') === 0;

        if ($isReply) {
            // if the tweet is a reply, make a nice title, fix up the content a bit,
            // and set the source to the original tweet (the one that was RTd)

            // set the title
            $title = __('Replied to a tweet from @{0}', $tweet->in_reply_to_screen_name);

            // set the source to the tweet that was replied to
            $source = "https://twitter.com/{$tweet->in_reply_to_user_id_str}/status/{$tweet->in_reply_to_status_id}";
        } else if ($isRetweet) {
            // make a nice title using the screen name of the person that was RTd.
            // this is another dumb string match to pull out the name of the user
            // whose tweet was RTd
            $rtMatch = [];
            if (preg_match('/^RT @([^\s:]+).*/', $content, $rtMatch)) {
                $title = __('Retweeted @{0}', $rtMatch[1]);

                // set the source to the tweet that was RTd
                if ($tweet->entities->user_mentions) {
                    foreach ($tweet->entities->user_mentions as $uMention) {
                        // find a user mention that matches the RTd screen name
                        if ($uMention->screen_name === $rtMatch[1]) {
                            $source = "https://twitter.com/{$uMention->id_str}/status/{$tweet->id_str}";
                            break;
                        }
                    }
                }
            }
        }

        die('here');

        // replace user mentions (@...) with links to the person's twitter page
        if ($tweet->entities->user_mentions) {
            foreach ($tweet->entities->user_mentions as $uMention) {
                $content = str_replace(
                    "@{$uMention->screen_name}",
                    "[https://twitter.com/{$uMention->id_str}](@{$uMention->screen_name})",
                    $content
                );
            }
        }

        // figure out if there are any photos/videos we need to import
        $medias = [];
        if (
            property_exists($tweet, 'extended_entities') &&
            $tweet->extended_entities &&
            property_exists($tweet->extended_entities, 'media') &&
            is_array($tweet->extended_entities) &&
            $tweet->extended_entities
        ) {
            foreach ($tweet->extended_entities->media as $media) {
                // what kind of media are we dealing with?
                $type = $media->type;
            }
        }

        $entity = [
            'created' => $created,
            'modified' => $created,
            'import_source' => 'twitter',
            'title' => $title,
            'content' => implode("\n", $content),
            'source' => $source,
            'medias' => $medias
        ];

        $post = $this->Post->newEntity($entity);
    }

    private function importMedia($media, $io)
    {
        // tell the user what's going on
        $io->out(__('Importing media...'));

        // create a data structure that the `uploadAndCreate` can work with
        $create = [
            'tmp_name' => $this->path . DS . $media->uri,
            'name' => basename($media->uri),
        ];

        // if we've already imported this media item, just use that
        $existing = $this->Medias->find()
            ->where([
                'original_filename' => $create['name']
            ])
            ->first();

        if ($existing) {
            $io->out(__('Media {0} exists in database', $create['name']));
            return $existing;
        }

        // some extra bits of data for the media
        $now = time();
        $created = date('Y-m-d H:i:s', isset($media->creation_timestamp) ? $media->creation_timestamp : $now);

        $addlData = [
            'created' => $created,
            'modified' => $created,
            'user_id' => $this->user->id,
            'import_source' => 'facebook'
        ];

        // if there's a title, use it
        if (isset($media->title) && $media->title) {
            $addlData['name'] = ImportUtils::fixText($media->title);
        }

        // if there's a description, use it
        if (isset($media->description) && $media->description) {
            $addlData['description'] = ImportUtils::fixText($media->description);
        }

        // are there comments on the media?
        if (isset($media->comments) && $media->comments) {
            $addlData['comments'] = $this->generateComments($media->comments, $io);
        }

        // finally, try to do something with the media
        $io->out(__('Attempting to move and save media'));
        if ($media = $this->Medias->uploadAndCreate($create, false, $addlData)) {
            $io->success(__('Media saved'));
            $this->stats['media']['success']++;
            // yay!!!
            return $media;
        } else {
            $this->stats['media']['fail']++;
        }

        return null;
    }
}
