<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;
use Cake\Datasource\EntityInterface;
use ArrayObject;
use App\Lib\oEmbed;

/**
 * Posts Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\MediaTable|\Cake\ORM\Association\HasMany $Media
 *
 * @method \App\Model\Entity\Post get($primaryKey, $options = [])
 * @method \App\Model\Entity\Post newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Post[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Post|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Post saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Post patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Post[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Post findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PostsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('posts');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Medias', [
            'foreignKey' => 'post_id',
            'saveStrategy' => 'replace'
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
    public function validationDefault(Validator $validator)
    {
        $validator
            ->uuid('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->boolean('public')
            ->notEmptyString('public');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        $oEmbed = new oEmbed();
        $embeds = [];

        $data['content'] = trim($data['content']);

        if ($data['content'] && !$data['source']) {
            $spacePos = strpos($data['content'], ' ');

            if (
                !$data['source'] &&
                $spacePos === false &&
                preg_match('/^https?:\/\/.*$/', $data['content'])
            ) {
                $data['source'] = $data['content'];
            }
        }

        if (isset($data['source']) && $data['source']) {
            $embedInfo = $oEmbed->getEmbedInfo($data['source']);

            if ($embedInfo && $embedInfo->code) {
                $embeds = [$embedInfo->code];
            }
        }

        // if (!$embeds) {
        //     if ($emb = $oEmbed->getEmbeds($data)) {
        //         $embeds = $emb;
        //     }
        // }

        $data['embeds'] = json_encode($embeds);

        return $data;
    }
}
