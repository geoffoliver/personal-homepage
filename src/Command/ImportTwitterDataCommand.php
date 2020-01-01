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
    private $zip;
    private $mediaZip;
    private $photosAlbum;
    private $videosAlbum;

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
            'help' => __('The path to your zipped Twitter data.'),
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

        // get the actual path to the zip (in case the user enters something like ./tmp/twitter-data.zip)
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

        // make sure the import file exists
        if (!ImportUtils::checkFile($path, $io)) {
            return;
        }

        $this->zip = new \ZipArchive();
        if (!$this->zip->open($path)) {
            $io->error__('Unable to open Twitter data zip file');
            return;
        }

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
        $tweetFileContent = $this->zip->getFromName('tweet.js');

        if (!$tweetFileContent) {
            $io->error(__('Tweet file is empty'));
            return;
        }

        $albums = [
            [
                'property' => 'photosAlbum',
                'name' => __('Twitter Photos'),
                'type' => 'photos'
            ],
            [
                'property' => 'videosAlbum',
                'name' => __('Twitter Videos'),
                'type' => 'videos'
            ],
        ];

        foreach ($albums as $album) {
            // find/create an album for twitter media
            $alb = $this->Albums->find()
                ->where([
                    'Albums.name' => $album['name'],
                    'Albums.type' => $album['type']
                ])
                ->first();

            if (!$alb) {
                $alb = $this->Albums->newEntity([
                    'name' => $album['name'],
                    'type' => $album['type'],
                    'user_id' => $this->user->id,
                ]);

                if (!$this->Albums->save($alb)) {
                    $io->error(__('Unable to create album for twitter ' . $album['type']));
                    return;
                }
            }

            $this->{$album['property']} = $alb;
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
            $screenName = '';
            if (property_exists($tweet, 'in_reply_to_screen_name')) {
                // the tweet has the screen name of the user we replied to, use it.
                $screenName = $tweet->in_reply_to_screen_name;
                $urlStr = $screenName;
            } else {
                // find an @ mention at the very beginning of the tweet
                $screenName = __('Unknown');
                $matches = [];
                if (preg_match('/^@([^\s]+).+$/', $content, $matches)) {
                    $screenName = $matches[1];
                }
                $urlStr = $tweet->in_reply_to_user_id_str;
            }

            $title = __('Replied to a tweet from @{0}', $screenName);
            // set the source to the tweet that was replied to
            $source = "https://twitter.com/{$urlStr}/status/{$tweet->in_reply_to_status_id}";
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
                            $source = "https://twitter.com/{$uMention->screen_name}/status/{$tweet->id_str}";
                            break;
                        }
                    }
                }
            }
        }

        // replace user mentions (@...) with links to the person's twitter page
        if ($tweet->entities->user_mentions) {
            foreach ($tweet->entities->user_mentions as $uMention) {
                $content = preg_replace(
                    "/@({$uMention->screen_name})(.*)/",
                    '[@$1](https://twitter.com/$1)$2',
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
            is_array($tweet->extended_entities->media) &&
            $tweet->extended_entities->media
        ) {
            foreach ($tweet->extended_entities->media as $media) {
                // what kind of media are we dealing with?
                if ($iMedia = $this->importMedia($media, $tweet, $io)) {
                    $medias[]= $iMedia;
                }
            }
        }

        // get a list of media IDs if there's any media
        $mediaIds = null;
        if ($medias) {
            $mediaIds = ['_ids' => []];
            foreach ($medias as $m) {
                $mediaIds['_ids'][]= $m->id;
            }
        }

        $entity = [
            'created' => $created,
            'modified' => $created,
            'import_source' => 'twitter',
            'name' => $this->fixText($title),
            'content' => $this->fixText($content),
            'user_id' => $this->user->id,
            'source' => $source,
            'medias' => $mediaIds,
        ];

        $post = $this->Posts->newEntity($entity);

        if ($errors = $post->getErrors()) {
            $io->error(print_r($errors, true));
            return;
        }

        if ($this->Posts->save($post)) {
            $this->stats['posts']['success']++;
            $io->success(__('Tweet saved!'));
            if ($medias) {
                $photos = [];
                $videos = [];
                foreach ($medias as $media) {
                    if (strpos($media->mime, 'image/') === 0) {
                        $photos[]= $media;
                    } elseif (strpos($media->mime, 'video/') === 0) {
                        $videos[]= $media;
                    }
                }

                if ($photos) {
                    if ($this->Albums->Medias->link($this->photosAlbum, $photos)) {
                        $io->success(__('Photos linked to album'));
                    }
                }

                if ($videos) {
                    if ($this->Albums->Medias->link($this->videosAlbum, $videos)) {
                        $io->success(__('Videos linked to album'));
                    }
                }
            }
        } else {
            dump($post);
            $this->stats['posts']['fail']++;
            $io->error(__('Unable to save tweet'));
        }
    }

    private function importMedia($media, $tweet, $io)
    {
        // tell the user what's going on
        $io->out(__('Importing media...'));

        // figure out what type of media we're dealing with
        $type = $media->type;

        // this is where the filename will go
        $mediaFile = null;

        if ($type === "photo") {
            // photos are easy, just the tweet id concated with a hyphen
            // and the actual filename
            $mediaFile = $tweet->id . '-' . basename($media->media_url);
        } else if (
            ($type === "video" || $type === "animated_gif") &&
            property_exists($media, 'video_info') &&
            $media->video_info &&
            property_exists($media->video_info, 'variants') &&
            is_array($media->video_info->variants) &&
            $media->video_info->variants
        ) {
            // videos are tricky because they've got multiple resolutions and a
            // playlist. we just want the highest resolution video we can get.
            // also, sometimes the bitrate can be 0 (wtf?!) thus, the -1 here.
            $bitrate = -1;

            foreach ($media->video_info->variants as $variant) {
                if (!property_exists($variant, 'bitrate')) {
                    // this is the playlist, skip it
                    continue;
                }
                // convert the bitrate to a number so we can compare it..
                $br = (int)$variant->bitrate;
                if ($br > $bitrate) {
                    // bigger bitrate on the video, so use it
                    $mediaFile = $tweet->id . '-' . basename($variant->url);
                    $br = $bitrate;
                }
            }
        } else {
            $io->error(__('Unhandled media type "{0}"', $type));
        }

        if (!$mediaFile) {
            $io->error(__('Unable to locate file for media type {0}', $type));
            return;
        }

        // if there's a querystring on the filename, remove it
        if (strpos($mediaFile, '?') !== false) {
            list($mediaFile, ) = explode('?', $mediaFile);
        }

        // if we've already imported this media item, just use that
        $existing = $this->Medias->find()
            ->where([
                'original_filename' => $mediaFile,
                'import_source' => 'twitter'
            ])
            ->first();

        if ($existing) {
            $io->out(__('Media {0} exists in database', $existing->name));
            return $existing;
        }

        // try to get the file out of the zip
        $extracted = $this->zip->getFromName('tweet_media' . DS . $mediaFile);

        if (!$extracted) {
            $io->error(__('Unable to find file named "{0}" in media archive', $mediaFile));
            return;
        }

        // this is where the file will live for a moment
        $tmpName = TMP . 'import' . DS . $mediaFile;

        if (!file_put_contents($tmpName, $extracted)) {
            $io->error(__('Unable to create temporary file from "{0}"', $mediaFile));
            return;
        }

        // create a data structure that the `uploadAndCreate` can work with
        $create = [
            'tmp_name' => $tmpName,
            'name' => $mediaFile,
        ];

        // some extra bits of data for the media
        $created = date('Y-m-d H:i:s', strtotime($tweet->created_at));

        $addlData = [
            'created' => $created,
            'modified' => $created,
            'user_id' => $this->user->id,
            'import_source' => 'twitter'
        ];

        // finally, try to do something with the media
        $io->out(__('Attempting to move and save media'));
        if ($media = $this->Medias->uploadAndCreate($create, false, $addlData)) {
            $io->success(__('Media saved'));
            $this->stats['media']['success']++;
            // delete the temporary file
            unlink($tmpName);
            // yay!!!
            return $media;
        } else {
            // delete the temporary file
            unlink($tmpName);
            $this->stats['media']['fail']++;
        }

        return null;
    }

    private function fixText($str)
    {
        // do the "regular" UTF fixes
        $str = ImportUtils::fixText($str);

        // escape quotes
        $str = str_replace('"', '\"', $str);

        // replace actual new lines with \n's
        $str = str_replace("\n", '\n', $str);

        // this is how we have to handle some UTF codes from Twitter
        $str = json_decode('"' . $str . '"');

        return $str;
    }
}
