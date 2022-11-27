<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FeedItems Model
 *
 * @property \App\Model\Table\FollowingsTable&\Cake\ORM\Association\BelongsTo $Followings
 *
 * @method \App\Model\Entity\FeedItem newEmptyEntity()
 * @method \App\Model\Entity\FeedItem newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\FeedItem[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FeedItem get($primaryKey, $options = [])
 * @method \App\Model\Entity\FeedItem findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\FeedItem patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\FeedItem[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\FeedItem|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FeedItem saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FeedItem[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\FeedItem[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\FeedItem[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\FeedItem[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FeedItemsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('feed_items');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Followings', [
            'foreignKey' => 'following_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->uuid('following_id')
            ->requirePresence('following_id', 'create')
            ->notEmptyString('following_id');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('summary')
            ->allowEmptyString('summary');

        $validator
            ->scalar('url')
            ->requirePresence('url', 'create')
            ->notEmptyString('url');

        $validator
            ->allowEmptyString('author');

        $validator
            ->scalar('content')
            ->allowEmptyString('content');

        $validator
            ->scalar('media')
            ->maxLength('media', 255)
            ->allowEmptyString('media');

        $validator
            ->boolean('is_read')
            ->notEmptyString('is_read');

        $validator
            ->allowEmptyString('page_feed');

        $validator
            ->dateTime('date_published')
            ->requirePresence('date_published', 'create')
            ->notEmptyDateTime('date_published');

        $validator
            ->dateTime('date_modified')
            ->requirePresence('date_modified', 'create')
            ->notEmptyDateTime('date_modified');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('following_id', 'Followings'), ['errorField' => 'following_id']);

        return $rules;
    }
}
