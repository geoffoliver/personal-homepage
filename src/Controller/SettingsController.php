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
        $timezones = [];

        $tz = timezone_identifiers_list();
        foreach ($tz as $zone) {
            $timezones[$zone] = str_replace('_', ' ', $zone);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $this->loadModel('Medias');
            $settings = $this->Settings->find()->all()->toArray();

            // the user who is uploading the file
            $user = $this->request->getAttribute('identity');

            $fileData = [
                'user_id' => $user->id
            ];

            foreach ($this->request->getData() as $key => $value) {
                $found = false;

                if (is_array($value)) {
                    if (isset($value['tmp_name'])) {
                        $file = $value;
                        $value = Hash::get($this->settings, $key);

                        if ($file['tmp_name']) {
                            try {
                                if ($media = $this->Medias->uploadAndCreate($file, true, $fileData)) {
                                    $value = $media->id;
                                }
                            } catch (\Exception $ex) {
                                dd($ex);
                            }
                        }
                    } else {
                        $value = json_encode($value);
                    }
                }

                foreach ($settings as $setting) {
                    if ($setting->name === $key) {
                        $this->Settings->patchEntity($setting, ['value' => $value]);
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $settings[]= $this->Settings->newEntity([
                        'name' => $key,
                        'value' => $value
                    ]);
                }
            }

            if ($this->Settings->saveMany($settings)) {
                return $this->redirect(['controller' => 'Settings', 'action' => 'index']);
            }
        }

        $this->set(compact('timezones'));
    }
}
