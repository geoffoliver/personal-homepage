<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * Settings Controller
 *
 * @property \App\Model\Table\SettingsTable $Settings
 *
 * @method \App\Model\Entity\Setting[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SettingsController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();

        // let everybody access a few methods
        $this->Authentication->allowUnauthenticated([
            'siteInfo',
        ]);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // user is saving settings
        if ($this->request->is(['patch', 'post', 'put'])) {
            return $this->saveSettings();
        }

        // get a list of timezones for the user to choose from
        $timezones = [];
        $tz = timezone_identifiers_list();
        foreach ($tz as $zone) {
            $timezones[$zone] = str_replace('_', ' ', $zone);
        }

        $bookmarklet = @file_get_contents(WWW_ROOT . DS . 'js' . DS . 'util' . DS . 'bookmarklet.js');

        // all done
        $this->set(compact('timezones', 'bookmarklet'));
    }

    private function saveSettings()
    {
        // load the medias model
        $this->Medias = $this->fetchTable('Medias');

        // get all the settings
        $settings = $this->Settings->find()->all()->toArray();

        // the user who is uploading the file
        $user = $this->request->getAttribute('identity');

        $fileData = [
            'user_id' => $user->id
        ];

        foreach ($this->request->getData() as $key => $value) {
            $found = false;

            // fields with array values (files and... maybe other stuff?)
            if ($value instanceof \Laminas\Diactoros\UploadedFile) {
                // this is a file, just try to upload it
                if ($value->getSize() > 0) {
                    if ($media = $this->Medias->uploadAndCreate($value, true, $fileData)) {
                        $value = $media->id;
                    } else {
                        $value = Hash::get($this->settings, $key);
                    }
                } else {
                    $value = Hash::get($this->settings, $key);
                }
            }

            if (is_array($value)) {
                // just encode the data and call it a day
                $value = json_encode($value);
            }

            // try to find and patch a matching setting entity... this could
            // definitely be improved.
            foreach ($settings as $setting) {
                if ($setting->name === $key) {
                    $this->Settings->patchEntity($setting, ['value' => $value]);
                    $found = true;
                    break;
                }
            }

            // we couldn't update the setting? ok, just make a new one!
            if (!$found) {
                $settings[]= $this->Settings->newEntity([
                    'name' => $key,
                    'value' => $value
                ]);
            }
        }

        // try to save all the settings
        if ($this->Settings->saveMany($settings)) {
            // yay!
            $this->Flash->success(__('Settings updated.'));
        } else {
            // boooo!!
            $errs = [];
            foreach ($settings as $s) {
                $errors = $s->getErrors();
                if (!$errors) {
                    continue;
                }
                $errs[$s->name] = $errors;
            }
            $this->Flash->error(__('Unable to update settings. Errors: ' . print_r($errs, true)));
        }

        // later, gator
        return $this->redirect(['controller' => 'Settings', 'action' => 'index']);
    }

    public function siteInfo()
    {
        $feed = Router::url([
            '_name' => 'jsonFeed',
            '_ext' => 'json'
        ], true);

        $icon = Router::url([
            '_name' => 'profilePhoto'
        ], true);

        $this->set([
            'name' => Hash::get($this->settings, 'site-name'),
            'description' => Hash::get($this->settings, 'homepage-about'),
            'icon' => $icon,
            'feed' => $feed,
            '_serialize' => [
                'name',
                'description',
                'icon',
                'feed'
            ]
        ]);
    }

}
