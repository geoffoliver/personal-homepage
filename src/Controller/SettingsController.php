<?php
namespace App\Controller;

use Cake\Utility\Hash;

use App\Controller\AppController;

/**
 * Settings Controller
 *
 * @property \App\Model\Table\SettingsTable $Settings
 *
 * @method \App\Model\Entity\Setting[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SettingsController extends AppController
{
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

        // all done
        $this->set(compact('timezones'));
    }

    private function saveSettings()
    {
        // load the medias model
        $this->loadModel('Medias');

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
            if (is_array($value)) {
                if (isset($value['tmp_name'])) {
                    // this is a file, just try to upload it
                    $file = $value;
                    $value = Hash::get($this->settings, $key);
                    if ($file['tmp_name']) {
                        if ($media = $this->Medias->uploadAndCreate($file, true, $fileData)) {
                            $value = $media->id;
                        }
                    }
                } else {
                    // just encode the data and call it a day
                    $value = json_encode($value);
                }
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
            $this->Flash->error(__('Unable to update settings.'));
        }

        // later, gator
        return $this->redirect(['controller' => 'Settings', 'action' => 'index']);
    }
}
