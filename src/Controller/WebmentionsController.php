<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Webmentions Controller
 *
 */
class WebmentionsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated([
            'add',
        ]);
    }

    /**
     * Add method
     *
     * Adds a new webmention to the database
     */
    public function add()
    {
        $webmention = $this->Webmentions->newEmptyEntity();

        if ($this->request->is('post')) {
            $source = $this->request->getData('source');
            $target = $this->request->getData('target');

            if (!$source) {
                throw new \Cake\Http\Exception\BadRequestException('Missing source');
            }

            if (!$target) {
                throw new \Cake\Http\Exception\BadRequestException('Missing target');
            }

            $protocol = 'http';
            $hostname = env('HTTP_HOST');
            if (env('HTTPS')) {
                $protocol .= 's';
            }

            $domain = $protocol . '://' . $hostname;

            if (parse_url($target, PHP_URL_HOST) !== parse_url($domain, PHP_URL_HOST)) {
                throw new \Cake\Http\Exception\BadRequestException('Bad request');
            }

            // make sure target actually exists
            $parsed = parse_url($target);
            $parts = $parsed['path'] ? explode('/', trim($parsed['path'], '/')) : [];

            \Cake\Error\Debugger::log([$parsed, $parts]);

            if (count($parts) >= 2) {
                $id = $parts[count($parts) - 1];
                $type = $parts[count($parts) - 2];
                $exists = false;

                if ($type === 'post') {
                    $posts = $this->fetchTable('Posts');
                    $exists = $posts->findById($id);
                } else if ($type === 'media') {
                    $medias = $this->fetchTable('Medias');
                    $exists = $medias->findById($id);
                }

                if ($exists) {
                    $webmention = $this->Webmentions->patchEntity($webmention, ['source' => $source, 'target' => $target]);
                    $this->Webmentions->save($webmention);
                }
            }
        }

        $this->set([
            'message' => 'ok',
            '_serialize' => [
                'message',
            ],
        ]);
    }
}
