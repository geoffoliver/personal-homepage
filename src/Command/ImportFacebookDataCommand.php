<?php

namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

use Cake\Filesystem\Folder;

/**
 * ImportFacebookData command.
 */
class ImportFacebookDataCommand extends Command
{

    private $user;
    private $path;
    private $photosDir;
    private $albumsDir;
    private $videosDir;
    private $postsDir;

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
            'help' => __('The path to your _unzipped_ Facebook data.'),
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
        $io->out(__('Importing Facebook data...'));

        // get the actual path (in case the user enters something like ./tmp/facebook-data)
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
        if (!$this->checkPath($path, $io)) {
            return;
        }

        // setup some directories
        $this->path = $path;
        $this->photosDir = $this->path . DS . 'photos_and_videos';
        $this->albumsDir = $this->photosDir . DS . 'album';
        $this->videosDir = $this->photosDir . DS . 'videos';
        $this->postsDir = $this->path . DS . 'posts';

        // give a little output
        $io->out(__('Processing data...'));

        // import albums
        //$this->importAlbums($path, $io);

        // import videos
        //$this->importVideos($path, $io);

        // import posts
        $this->importPosts($path, $io);

        // all done
        $io->success(__('Done!'));
    }

    private function importAlbums($path, ConsoleIo $io)
    {
        // tell the user what's happening
        $io->out(__('Importing albums'));

        // make sure the albums path is ok
        if (!$this->checkPath($this->albumsDir, $io)) {
            return;
        }

        // fire up a folder and search for JSON files in it
        $albumsFolder = new Folder($this->albumsDir);
        $albums = $albumsFolder->find('^.+\.json$');

        if (!$albums) {
            // no albums? no problem. later!!
            $io->out(__('There are no albums to import'));
            return;
        }

        // loop over the albums and import them
        foreach ($albums as $aFile) {
            $albumFile = $this->albumsDir . DS . $aFile;
            if (!$this->checkFile($albumFile, $io)) {
                continue;
            }

            // pull the album data out of the JSON file
            $album = json_decode(file_get_contents($albumFile));

            $io->out(__('Creating album "{0}"', $album->name));

            if (!$album->photos) {
                $io->out(__('Album "{0}" has no photos, skipping.', $album->name));
                continue;
            }

            // if an album with this name exists already, there's nothing else to do
            $exists = $this->Albums->find()
                ->where([
                    'Albums.name' => $album->name
                ])
                ->first();

            if ($exists) {
                $io->out(__('Album "{0}" exists, moving on.', $exists->name));
                continue;
            }

            // make the album entity
            $created = date('Y-m-d H:i:s', isset($album->last_modified_timestamp) ? $album->last_modified_timestamp : time());
            $entity = [
                'name' => $album->name,
                'user_id' => $this->user->id,
                'description' => $album->description,
                'created' => $created,
                'modified' => $created,
            ];

            // are there comments on the album?
            if ($album->comments) {
                $entity['comments'] = $this->generateComments($album->comments, $io);
            }

            // does the album have a cover photo?
            if ($album->cover_photo) {
                $io->out(__('Generating cover photo...'));
                if ($coverPhoto = $this->importMedia($album->cover_photo, $io)) {
                    $entity['cover_photo'] = $coverPhoto->id;
                }
            }

            // loop over each photo and import it
            $photos = [];
            foreach ($album->photos as $aPhoto) {
                if ($photo = $this->importMedia($aPhoto, $io)) {
                    $photos[]= $photo;
                }
            }

            // try to create the album entity
            $albumObject = $this->Albums->newEntity($entity);

            // if there are any errors, just spit them out and continue on
            if ($errors = $albumObject->getErrors()) {
                $io->error(print_r($errors, true));
                continue;
            }

            // try to save the album
            if ($this->Albums->save($albumObject)) {
                $io->success(__('Album "{0}" created', $albumObject->name));
                // if the album had photos (which it really should, right?) link
                // them to the album
                if ($photos) {
                    if ($this->Albums->Medias->link($albumObject, $photos)) {
                        $io->success(__('Photos linked to album'));
                    } else {
                        $io->error(__('Unable to link photos'));
                    }
                }
            }
        }

        $io->success(__('Albums imported'));
    }

    private function importVideos($path, ConsoleIo $io)
    {
        // tell the user what's going on
        $io->out(__('Importing videos'));

        // make sure the videos dir is ok
        if (!$this->checkPath($this->videosDir, $io)) {
            return;
        }

        // the JSON file that describes a user's videos
        $videosFile = $this->photosDir . DS . 'your_videos.json';

        // make sure the JSON file actually exists and we can access it
        if (!$this->checkFile($videosFile, $io)) {
            return;
        }

        $videos = json_decode(file_get_contents($videosFile));

        // file didn't decode, or didn't decode correctly. oh well.
        if (!$videos) {
            $io->error(__('Unable to decode videos file'));
            return;
        }

        // if there aren't any videos, move on
        if (!isset($videos->videos) || !$videos->videos) {
            $io->out(__('No videos to process, moving on'));
            return;
        }

        // if the album already exists, move on
        $existing = $this->Albums->find()
            ->where([
                'Albums.name' => __('Facebook Videos')
            ])
            ->first();

        if ($existing) {
            $io->out(__('Videos album exists, moving on'));
            return;
        }

        // import the video files
        $vids = [];
        foreach ($videos->videos as $video) {
            if ($video = $this->importMedia($video, $io)) {
                $vids[]= $video;
            }
        }

        // try to create the video album
        $videosAlbum = $this->Albums->newEntity([
            'name' => __('Facebook Videos'),
            'user_id' => $this->user->id,
        ]);

        if ($this->Albums->save($videosAlbum)) {
            $io->success(__('Videos album created'));

            // link the videos to the album
            if ($vids) {
                if ($this->Albums->Medias->link($videosAlbum, $vids)) {
                    $io->success(__('Videos linked to album'));
                }
            }
        }

        $io->success(__('Done importing videos'));
    }

    private function importPosts($path, ConsoleIo $io)
    {
        // tell the user what's happening
        $io->out(__('Importing posts'));

        if (!$this->checkPath($this->postsDir, $io)) {
            return;
        }

        // get a handle on the directory where posts live
        $postDir = new Folder($this->postsDir);
        $postFiles = $postDir->find('^your_posts.*\.json$');

        // find post files
        if (!$postFiles) {
            $io->out(__('There are no post files to process'));
            return;
        }

        // loop over the post files and create the posts!!
        foreach ($postFiles as $pFile) {
            $io->out(__('Processing post file "{0}"', basename($pFile)));

            // the JSON file with the post data
            $postFile = $this->postsDir . DS . $pFile;

            // make sure we can access the post file
            if (!$this->checkFile($postFile, $io)) {
                continue;
            }

            // try to decode the posts
            $posts = json_decode(file_get_contents($postFile));

            // well that sucks... oh well.
            if (!$posts) {
                $io->out(__('No posts in file "{0}"', basename($pFile)));
                continue;
            }

            // loop over the posts and create them
            foreach ($posts as $post) {
                // figure out when the post was created
                $created = date('Y-m-d H:i:s');
                if (isset($post->timestamp) && $post->timestamp) {
                    $created = date('Y-m-d H:i:s', $post->timestamp);
                }

                // grab/generate a title for the post
                $title = isset($post->title) ? $post->title : __('Untitled Post');
                $content = [];

                // are there comments on the post?
                if (isset($post->comments) && $post->comments) {
                    $entity['comments'] = $this->generateComments($post->comments);
                }

                var_dump($post);
                die();

                // this is where post content and other stuff lives
                if (isset($post->data) && $post->data) {
                    foreach ($post->data as $pKey => $pVal) {
                        var_dump(['key' => $pKey, 'val' => $pVal]);
                    }
                }

                die();

                // basically everything is a fucking attachment... this is going to suck
                if (isset($post->attachments) && $post->attachments) {

                }

                // create the post entity
                $entity = [
                    'user_id' => $this->user->id,
                    'created' => $created,
                    'modified' => $created,
                    'title' => $title,
                    'content' => implode("\n", $content)
                ];

                $postEntity = $this->Posts->newEntity($entity);

                if ($errors = $postEntity->getErrors()) {
                    $io->error(print_r($errors, true));
                    continue;
                }

                if ($this->Posts->save($postEntity)) {

                }
            }
        }
    }

    private function generateComments($comments, $io)
    {
        // let the user know what's going on
        $io->out(__('Generating comments...'));

        // this is what we'll hand back
        $commentsArr = [];

        foreach ($comments as $comment) {
            // try to figure out the author name... sometimes it's not there
            $author = null;
            if (isset($comment->author) && $comment->author) {
                $author = $comment->author;
            }

            // this is the comment. easy peasy!
            $commentsArr[]= [
                'posted_by' => 'facebook-data-import@internal',
                'display_name' => $author,
                'comment' => $comment->comment,
                'approved' => true,
                'public' => true,
                'created' => date('Y-m-d H:i:s', $comment->timestamp),
                'modified' => date('Y-m-d H:i:s', $comment->timestamp),
            ];
        }

        return $commentsArr;
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
            'user_id' => $this->user->id
        ];

        // if there's a title, use it
        if (isset($media->title) && $media->title) {
            $addlData['name'] = $media->title;
        }

        // if there's a description, use it
        if (isset($media->description) && $media->description) {
            $addlData['description'] = $media->description;
        }

        // are there comments on the media?
        if (isset($media->comments) && $media->comments) {
            $addlData['comments'] = $this->generateComments($media->comments, $io);
        }

        // finally, try to do something with the media
        $io->out(__('Attempting to move and save media'));
        if ($media = $this->Medias->uploadAndCreate($create, false, $addlData)) {
            $io->success(__('Media saved'));
            // yay!!!
            return $media;
        }

        return null;
    }

    private function checkPath($path, $io)
    {
        if (!is_dir($path)) {
            $io->error(__('Path {0} is not a directory', $path));
            return false;
        }

        return $this->checkFile($path, $io);
    }

    private function checkFile($path, $io)
    {
        if (!file_exists($path)) {
            $io->error(__('Invalid path: {0}', $path));
            return false;
        }

        if (!is_readable($path)) {
            $io->error(__('Path {0} is unreadable', $path));
            return false;
        }

        return true;
    }
}
