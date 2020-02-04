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
        $userId = Text::uuid();
        $now = date('Y-m-d H:i:s');
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

        // add some settings
        $settings = [
            [
                'id' => Text::uuid(),
                'created' => $now,
                'modified' => $now,
                'name' => 'about-page',
                'value' => ''
            ],
            [
                'id' => Text::uuid(),
                'created' => $now,
                'modified' => $now,
                'name' => 'homepage-about',
                'value' => __('Say something about yourself here!')
            ],
            [
                'id' => Text::uuid(),
                'created' => $now,
                'modified' => $now,
                'name' => 'cover-title',
                'value' => __('Welcome to my homepage')
            ],
            [
                'id' => Text::uuid(),
                'created' => $now,
                'modified' => $now,
                'name' => 'cover-subtitle',
                'value' => __('These are things I\'ve said and done.')
            ],
            [
                'id' => Text::uuid(),
                'created' => $now,
                'modified' => $now,
                'name' => 'timezone',
                'value' => 'America/New_York'
            ],
            [
                'id' => Text::uuid(),
                'created' => $now,
                'modified' => $now,
                'name' => 'site-name',
                'value' => __('My Homepage'),
            ],
            [
                'id' => Text::uuid(),
                'created' => $now,
                'modified' => $now,
                'name' => 'site-title',
                'value' => __('Just another site on the internet.'),
            ],
            [
                'id' => Text::uuid(),
                'created' => $now,
                'modified' => $now,
                'name' => 'time-format',
                'value' => 'F j, Y \a\t g:i a'
            ]
        ];

        $this->table('settings')->insert($settings)->save();
    }
}
