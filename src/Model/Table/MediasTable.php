<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Filesystem\File;
use Cake\Utility\Text;
use Cake\Datasource\EntityInterface;

/**
 * Medias Model
 *
 * @property \App\Model\Table\PostsTable|\Cake\ORM\Association\BelongsTo $Posts
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Media get($primaryKey, $options = [])
 * @method \App\Model\Entity\Media newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Media[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Media|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Media saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Media patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Media[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Media findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MediasTable extends Table
{

    public $mediaPath = WWW_ROOT . 'media';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('medias');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Posts', [
            'foreignKey' => 'post_id',
        ]);
        $this->belongsTo('Albums', [
            'foreignKey' => 'album_id',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Comments', [
            'foreignKey' => 'model_id',
            'dependent' => true
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
    {
        $validator
            ->uuid('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('mime')
            ->maxLength('mime', 255)
            ->requirePresence('mime', 'create')
            ->notEmptyString('mime');

        $validator
            ->scalar('thumbnail')
            ->maxLength('thumbnail', 255)
            ->requirePresence('thumbnail', 'create')
            ->notEmptyString('thumbnail');

        $validator
            ->scalar('local_filename')
            ->maxLength('local_filename', 255)
            ->requirePresence('local_filename', 'create')
            ->notEmptyFile('local_filename');

        $validator
            ->scalar('original_filename')
            ->maxLength('original_filename', 255)
            ->requirePresence('original_filename', 'create')
            ->notEmptyFile('original_filename');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker
    {
        $rules->add($rules->existsIn(['post_id'], 'Posts'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    public function uploadAndCreate(\Laminas\Diactoros\UploadedFile $file, $shouldBeUploaded = true, $extraData = [])
    {
        if ($shouldBeUploaded && !$file->getSize()) {
            throw new \Exception(__('Invalid file size'));
        }

        $filename = $file->getClientFilename();

        $uploadsRoot = $this->mediaPath;
        $uploadFolder = date('Y') . DS . date('m') . DS . date('d');

        if ($extraData && isset($extraData['created']) && $extraData['created']) {
            $created = strtotime($extraData['created']);
            if (!$created) {
                $created = time();
            }
            $uploadFolder = date('Y', $created) . DS . date('m', $created) . DS . date('d', $created);
        }

        $uploadDest = $uploadsRoot . DS . $uploadFolder;

        if (!file_exists($uploadDest)) {
            mkdir($uploadDest, 0777, true);
        }

        $uploadFilename = Text::uuid();

        if ($filename && strpos($filename, '.') !== false) {
            $parts = explode('.', $filename);
            $ext = strtolower($parts[count($parts) - 1]);
            $uploadFilename .= ".{$ext}";
        }

        $uploadFileDest = $uploadDest . DS . $uploadFilename;

        $moved = false;

        if ($shouldBeUploaded) {
            $file->moveTo($uploadFileDest);
            $moved = true;
        } else {
            die('Implement copying files that should not be uploaded!');
            // $moved = copy($file, $uploadFileDest);
        }

        if ($moved) {
            // $mediaFile = new File($uploadFileDest);
            $mediaFile = new \SplFileInfo($uploadFileDest);

            // try {
                $thumbnailFilename = $this->generateThumbnail($mediaFile);
                if ($thumbnailFilename) {
                    $thumbnailFilename = $uploadFolder . DS . $thumbnailFilename;
                }

                $squareThumbnailFilename = $this->generateThumbnail($mediaFile, true);
                if ($squareThumbnailFilename) {
                    $squareThumbnailFilename = $uploadFolder . DS . $squareThumbnailFilename;
                }

                // $mime = $mediaFile->mime();
                $mime = mime_content_type($uploadFileDest);

                $newMedia = $this->newEntity(array_merge([
                    'mime' => $mime,
                    'size' => $mediaFile->getSize(),
                    'thumbnail' => $thumbnailFilename,
                    'square_thumbnail' => $squareThumbnailFilename,
                    'local_filename' => $uploadFolder . DS . $uploadFilename,
                    'original_filename' => $filename,
                ], $extraData));

                if ($newMedia->getErrors()) {
                    $errors = $newMedia->getErrors();
                    $err = [];
                    foreach ($errors as $field => $ers) {
                        foreach ($ers as $e) {
                            $err[]= "{$field}: " . print_r($e, true);
                        }
                    }

                    throw new \Exception(implode(". ", $err));
                }

                // $mediaFile->close();

                if ($this->save($newMedia)) {
                    return $newMedia;
                }
            // } catch (\Exception $ex) {
            //     // $mediaFile->close();
            // }
        }

        return null;
    }

    private function generateThumbnail(\SplFileInfo $file, $square = false)
    {
        $thumbPath = $file->getPathname() . '-thumbnail';

        if ($square) {
            $thumbPath.= '-square';
        }

        $width = 500;
        $height = 500;
        $mime = mime_content_type($file->getPathname());
        // $mime = $file->mime();

        if (strpos($mime, 'image') === 0) {
            if ($ext = $file->getExtension()) {
                $thumbPath.= '.' . $ext;
            }

            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }

            // if (!$file->copy($thumbPath)) {
            if (!copy($file->getPathname(), $thumbPath)) {
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
                unset($thumb);
                return basename($thumbPath);
            }

            $thumb->destroy();
            unset($thumb);

            return false;
        }

        if (strpos($mime, 'video') === 0) {
            try {
                $ffmpeg = \FFMpeg\FFMpeg::create();
                $video = $ffmpeg->open($file->getPathname());
                $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(0));

                // turn the video filename into a jpg filename
                $imageThumb = $thumbPath . '.jpg';

                $frame->save($imageThumb);
                $thumb = new \Imagick($imageThumb);

                // maybe this will help memory usage? :shrug:
                unset($frame);
                unset($video);

                if ($square) {
                    $thumb->cropThumbnailImage($width, $height, true);
                } else {
                    $thumb->thumbnailImage($width, $height, true);
                }

                if ($thumb->writeImage($imageThumb)) {
                    $thumb->destroy();
                    unset($thumb);
                    return basename($imageThumb);
                }

                $thumb->destroy();
                unset($thumb);
            } catch (\Exception $ex) {
                return false;
            }
        }

        return false;
    }

    public function delete(EntityInterface $entity, $options = []): bool
    {
        $deleted = parent::delete($entity, $options);

        if ($deleted) {
            $delete = [];

            if ($entity->thumbnail) {
                $delete[]= $entity->thumbnail;
            }

            if ($entity->square_thumbnail) {
                $delete[]= $entity->square_thumbnail;
            }

            if ($entity->local_filename) {
                $delete[]= $entity->local_filename;
            }

            foreach ($delete as $del) {
                $del = $this->mediaPath . DS . $del;
                if (file_exists($del) && is_readable($del)) {
                    unlink($del);
                }
            }
        }

        return $deleted;
    }

}
