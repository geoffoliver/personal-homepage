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
        if (!$this->request->is('post')) {
            throw new \Exception('Invalid request');
        }

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
        $partsCount = count($parts);

        if ($partsCount >= 2) {
            $id = $parts[$partsCount - 1];
            $type = $parts[$partsCount - 2];

            if ($type === 'view' && $partsCount >= 3) {
                $type = $parts[$partsCount - 3];
            }

            if ($id && $type) {
                $postOrMedia = false;

                if ($type === 'view-post' || $type === 'post') {
                    $type = 'post';
                    $postOrMedia = $this->fetchTable('Posts')->findById($id);
                } else if ($type === 'view-media' || $type === 'media') {
                    $type = 'media';
                    $postOrMedia = $this->fetchTable('Medias')->findById($id);
                }

                if ($postOrMedia) {
                    $webmention = $this->Webmentions->newEntity([
                        'source' => $source,
                        'target' => $target,
                        'type' => $type,
                        'type_id' => $id,
                    ]);
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
