<?php
use Migrations\AbstractSeed;
use Cake\Utility\Text;
use Authentication\PasswordHasher\DefaultPasswordHasher;

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
        $now = $faker->dateTime()->format('Y-m-d H:i:s');
        $password = (new DefaultPasswordHasher)->hash('password');

        $users = [[
            'id' => $userId,
            'name' => 'Geoff Oliver',
            'email' => 'the@geoffoliver.org',
            'password' => $password,
            'created' => $now,
            'modified' => $now
        ]];

        $this->table('users')->insert($users)->save();

        $posts = [];
        $medias = [];
        $comments = [];

        for ($i = 0; $i < 10; $i++) {
            $postId = Text::uuid();
            $now = $faker->dateTime()->format('Y-m-d H:i:s');
            $post = [
                'id' => $postId,
                'name' => $faker->words(5, true),
                'content' => '<p>' . implode('</p><p>', $faker->paragraphs(4)) . '</p>',
                'public' => true,
                'allow_comments' => true,
                'user_id' => $userId,
                'created' => $now,
                'modified' => $now
            ];

            if ($i % 2) {
                $mediaId = Text::uuid();
                $now = $faker->dateTime()->format('Y-m-d H:i:s');
                $medias[]= [
                    'id' => $mediaId,
                    'post_id' => $postId,
                    'mime' => 'image/jpeg',
                    'square_thumbnail' => '2019/07/13/dog-thumbnail.jpg',
                    'thumbnail' => '2019/07/13/dog-thumbnail.jpg',
                    'local_filename' => '2019/07/13/dog.jpg',
                    'original_filename' => 'a-really-cool-dog.jpg',
                    'user_id' => $userId,
                    'created' => $now,
                    'modified' => $now
                ];

                $maxComments = rand(1, 20);
                for ($n = 0; $n < $maxComments; $n++) {
                  $now = $faker->dateTime()->format('Y-m-d H:i:s');
                  $comments[]= [
                    'id' => Text::uuid(),
                    'model_id' => $mediaId,
                    'comment' => '<p>' . implode('</p><p>', $faker->paragraphs(rand(1, 4))) . '</p>',
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
              $now = $faker->dateTime()->format('Y-m-d H:i:s');
              $comments[]= [
                'id' => Text::uuid(),
                'model_id' => $postId,
                'comment' => '<p>' . implode('</p><p>', $faker->paragraphs(rand(1, 4))) . '</p>',
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
