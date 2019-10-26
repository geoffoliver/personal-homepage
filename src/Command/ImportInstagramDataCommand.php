<?php
namespace App\Command;

use App\Lib\ImportUtils;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Utility\Inflector;

/**
 * ImportInstagramData command.
 */
class ImportInstagramDataCommand extends Command
{
    private $user;
    private $path;
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
            'help' => __('The path to your _unzipped_ Instagram data.'),
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
        $io->out(__('Importing Instagram data...'));

        // get the actual path (in case the user enters something like ./tmp/instagram-data)
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

        $albums = [
            [
                'property' => 'photosAlbum',
                'name' => __('Instagram Photos'),
                'type' => 'photos'
            ],
            [
                'property' => 'videosAlbum',
                'name' => __('Instagram Videos'),
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
                    $io->error(__('Unable to create album for instagram ' . $album['type']));
                    return;
                }
            }

            $this->{$album['property']} = $alb;
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
        $io->out(__('Importing photos, videos, and stories...'));

        // this is where instagram posts _should_ live
        $mediaFile = $this->path . DS . 'media.json';

        // make sure we can work with the instagram file
        if (!ImportUtils::checkFile($mediaFile, $io)) {
            return;
        }

        // grab the contents of the file that contains instagram posts
        $mediaFileContent = file_get_contents($mediaFile);

        if (!$mediaFileContent) {
            $io->error(__('Instagram media file is empty'));
            return;
        }

        $igPostTypes = json_decode($mediaFileContent);
        if (!$igPostTypes) {
            $io->error(__('Unable to decode instagram media file'));
            return;
        }

        foreach ($igPostTypes as $postType => $posts) {
            foreach ($posts as $post) {
                $this->importPost($io, $postType, $post);
            }
        }
    }

    private function importPost($io, $type, $igPost)
    {
        $io->out(__('Importing instagram post from {0}', $igPost->taken_at));

        $title = __('Posted a {0}', ucfirst(Inflector::singularize($type)));
        $content = $igPost->caption;

        $created = date('Y-m-d H:i:s', strtotime($igPost->taken_at));

        $media = $this->importMedia($io, $title, $type, $igPost);

        if (!$media) {
            return;
        }

        $entity = [
            'created' => $created,
            'modified' => $created,
            'import_source' => 'instagram',
            'name' => ImportUtils::fixText($title),
            'content' => ImportUtils::fixText($content),
            'user_id' => $this->user->id,
            'medias' => ['_ids' => [$media->id]],
        ];

        $post = $this->Posts->newEntity($entity);

        if ($errors = $post->getErrors()) {
            $io->error(print_r($errors, true));
            return;
        }

        if ($this->Posts->save($post)) {
            $this->stats['posts']['success']++;
            $io->success(__('Instagram post saved!'));

            if ($type === 'photos' && $this->Albums->Medias->link($this->photosAlbum, [$media])) {
                $io->success(__('Photo linked to album'));
            } else if ($type === 'videos' && $this->Albums->Medias->link($this->videosAlbum, [$media])) {
                $io->success(__('Video linked to album'));
            }
        } else {
            $this->stats['posts']['fail']++;
        }
    }

    private function importMedia($io, $title, $type, $igPost)
    {
        // tell the user what's going on
        $io->out(__('Importing media...'));

        // grab the filename for the file
        $mediaFile = $this->path . DS . $igPost->path;
        $mediaFilename = basename($mediaFile);

        if (!ImportUtils::checkFile($mediaFile, $io)) {
            return;
        }

        // if we've already imported this media item, just use that
        $existing = $this->Medias->find()
            ->where([
                'original_filename' => $mediaFilename,
                'import_source' => 'instagram'
            ])
            ->first();

        if ($existing) {
            $io->out(__('Media {0} exists in database', $existing->name));
            return $existing;
        }

        // create a data structure that the `uploadAndCreate` can work with
        $create = [
            'tmp_name' => $mediaFile,
            'name' => $mediaFilename,
        ];

        // some extra bits of data for the media
        $created = date('Y-m-d H:i:s', strtotime($igPost->taken_at));

        $addlData = [
            'created' => $created,
            'modified' => $created,
            'user_id' => $this->user->id,
            'import_source' => 'instagram',
            'name' => $title
        ];

        if ($igPost->caption) {
            $addlData['description'] = $igPost->caption;
        }

        // finally, try to do something with the media
        $io->out(__('Attempting to move and save media'));
        if ($media = $this->Medias->uploadAndCreate($create, false, $addlData)) {
            $io->success(__('Media saved'));
            $this->stats['media']['success']++;
            // yay!!!
            return $media;
        } else {
            $io->error(__('Unable to save media'));
            $this->stats['media']['fail']++;
        }

        return null;
    }
}
