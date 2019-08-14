<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Utility\Hash;
use Cake\Utility\Text;


class MediasController extends AppController
{
    public $paginate = [
        'Posts' => [
            'limit' => 10000,
            'conditions' => [
                'public' => true,
            ],
            'contain' => [
                'Medias',
            ],
        ],
    ];

    public function initialize()
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['heroBackground', 'profilePhoto']);
    }

    public function upload()
    {
        $this->request->allowMethod(['post']);

        $success = false;
        $media = null;

        $user = $this->request->getAttribute('identity');

        $file = $this->request->getData('file');

        if (!$file) {
            throw new \Exception(__('Missing upload'));
        }

        $tmp = Hash::get($file, 'tmp_name');

        if (!$tmp) {
            throw new \Exception(__('Invalid file'));
        }

        if (!is_uploaded_file($tmp)) {
            throw new \Exception(__('Invalid upload'));
        }

        $filename = Hash::get($file, 'name');

        $uploadsRoot = WWW_ROOT . 'media';
        $uploadFolder = date('Y') . DS . date('m') . DS . date('d');
        $uploadDest = new Folder($uploadsRoot . DS . $uploadFolder, true);
        $uploadFilename = Text::uuid();

        if ($filename && strpos($filename, '.') !== false) {
            $parts = explode('.', $filename);
            $ext = strtolower($parts[count($parts) - 1]);
            $uploadFilename.= ".{$ext}";
        }

        $uploadFileDest = $uploadDest->path . DS . $uploadFilename;

        if (move_uploaded_file($tmp, $uploadFileDest)) {
            $mediaFile = new File($uploadFileDest);

            $thumbnailFilename = $this->generateThumbnail($mediaFile);
            if ($thumbnailFilename) {
                $thumbnailFilename = $uploadFolder . DS . $thumbnailFilename;
            }

            $squareThumbnailFilename = $this->generateThumbnail($mediaFile, true);
            if ($squareThumbnailFilename) {
                $squareThumbnailFilename = $uploadFolder . DS . $squareThumbnailFilename;
            }

            $mime = $mediaFile->mime();

            $newMedia = $this->Medias->newEntity([
                'mime' => $mime,
                'name' => $this->request->getData('name'),
                'description' => $this->request->getData('description'),
                'size' => $mediaFile->size(),
                'thumbnail' => $thumbnailFilename,
                'square_thumbnail' => $squareThumbnailFilename,
                'local_filename' => $uploadFolder . DS . $uploadFilename,
                'original_filename' => $filename,
                'user_id' => $user->id
            ]);

            if ($newMedia->getErrors()) {
                $errors = $newMedia->getErrors();
                $err = [];
                foreach ($errors as $field => $ers) {
                    foreach ($ers as $e) {
                        $err[]= "{$field}: {$e}";
                    }
                }

                throw new \Exception(implode(". ", $err));
            }

            if ($this->Medias->save($newMedia)) {
                $success = true;
                $media = $newMedia;
            }
        }

        $this->set([
            'success' => $success,
            'data' => [
                'media' => $media,
            ],
            '_serialize' => [
                'success',
                'data',
            ],
        ]);
    }

    public function heroBackground()
    {
        $this->loadModel('Settings');
        $hero = $this->Settings->find()->where(['Settings.name' => 'site.hero-background'])->first();
        $file = WWW_ROOT . 'img' . DS . 'default-hero-background.jpg';

        if ($hero) {
            $media = $this->Medias->find()->where(['Medias.id' => $hero->value])->first();

            if ($media) {
                $mFile = WWW_ROOT . 'media' . DS . $media->value;
                if (file_exists($mFile) && is_readable($mFile)) {
                    $file = $mFile;
                }
            }
        }

        return $this->response->withFile($file);
    }

    public function profilePhoto()
    {
        $this->loadModel('Settings');
        $profile = $this->Settings->find()->where(['Settings.name' => 'site.profile-photo'])->first();
        $file = WWW_ROOT . 'img' . DS . 'default-profile-photo.jpg';

        if ($profile) {
            $media = $this->Medias->find()->where(['Medias.id' => $profile->value])->first();

            if ($media) {
                $mFile = WWW_ROOT . 'media' . DS . $media->value;
                if (file_exists($mFile) && is_readable($mFile)) {
                    $file = $mFile;
                }
            }
        }

        return $this->response->withFile($file);
    }

    private function generateThumbnail($file, $square = false)
    {
        $thumbPath = $file->path . '-thumbnail';

        if ($square) {
            $thumbPath.= '-square';
        }

        $width = 500;
        $height = 500;
        $mime = $file->mime();

        if (strpos($mime, 'image') === 0) {
            if ($ext = $file->ext()) {
                $thumbPath.= '.' . $ext;
            }

            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }

            if (!$file->copy($thumbPath)) {
                return false;
            }

            $thumb = new \Imagick($thumbPath);
            if ($square) {
                $thumb->cropThumbnailImage($width, $height);
            } else {
                $thumb->thumbnailImage($width, $height, true);
            }

            if ($thumb->writeImage($thumbPath)) {
                $thumb->destroy();
                return basename($thumbPath);
            }

            $thumb->destroy();

            return false;
        }

        if (strpos($mime, 'video') === 0) {
            $ffmpeg = \FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($file->path);
            $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(5));

            try {
                // turn the video filename into a jpg filename
                $imageThumb = $thumbPath . '.jpg';

                $frame->save($imageThumb);
                $thumb = new \Imagick($imageThumb);

                if ($square) {
                    $thumb->cropThumbnailImage($width, $height, true);
                } else {
                    $thumb->thumbnailImage($width, $height, true);
                }

                if ($thumb->writeImage($imageThumb)) {
                    $thumb->destroy();
                    return basename($imageThumb);
                }
            } catch (\Exception $ex) {
                return false;
            }
        }

        return false;
    }


}
