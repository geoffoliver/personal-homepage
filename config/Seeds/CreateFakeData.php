<?php
use Migrations\AbstractSeed;
use Cake\Utility\Text;

/**
 * CreateFakePosts seed.
 */
class CreateFakeData extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $userId = Text::uuid();
        $now = date('Y-m-d H:i:s');

        $users = [[
            'id' => $userId,
            'name' => 'Geoff Oliver',
            'email' => 'the@geoffoliver.org',
            'password' => sha1('password'),
            'created' => $now,
            'modified' => $now
        ]];

        $this->table('users')->insert($users)->save();

        $posts = [];
        $medias = [];
        $comments = [];

        for ($i = 0; $i < 100; $i++) {
            $postId = Text::uuid();
            $post = [
                'id' => $postId,
                'url_alias' => $faker->slug,
                'name' => $faker->words(5, true),
                'content' => $faker->paragraphs(4, true),
                'public' => true,
                'user_id' => $userId,
                'created' => $now,
                'modified' => $now
            ];

            if ($i % 2) {
                $mediaId = Text::uuid();
                $medias[]= [
                    'id' => $mediaId,
                    'post_id' => $postId,
                    'mime' => 'image/jpeg',
                    'thumbnail' => '2019/07/13/dog-thumbnail.jpg',
                    'local_filename' => '2019/07/13/dog.jpg',
                    'original_filename' => 'a-really-cool-dog.jpg',
                    'user_id' => $userId,
                    'created' => $now,
                    'modified' => $now
                ];

                $maxComments = rand(1, 20);
                for ($n = 0; $n < $maxComments; $n++) {
                  $comments[]= [
                    'id' => Text::uuid(),
                    'model_id' => $mediaId,
                    'comment' => $faker->paragraphs(rand(1, 4), true),
                    'approved' => true,
                    'public' => true,
                    'posted_by' => $faker->email,
                    'display_name' => $faker->name,
                    'created' => $now,
                    'modified' => $now
                  ];
                }
            }

            $maxComments = rand(1, 20);
            for ($n = 0; $n < $maxComments; $n++) {
              $comments[]= [
                'id' => Text::uuid(),
                'model_id' => $postId,
                'comment' => $faker->paragraphs(rand(1, 4), true),
                'approved' => true,
                'public' => true,
                'posted_by' => $faker->email,
                'display_name' => $faker->name,
                'created' => $now,
                'modified' => $now
              ];
            }

            $posts[]= $post;
        }

        $this->table('posts')->insert($posts)->save();
        $this->table('medias')->insert($medias)->save();
        $this->table('comments')->insert($comments)->save();
    }
}
