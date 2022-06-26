<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Webmentions Model
 *
 * @method \App\Model\Entity\Webmention newEmptyEntity()
 * @method \App\Model\Entity\Webmention newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Webmention[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Webmention get($primaryKey, $options = [])
 * @method \App\Model\Entity\Webmention findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Webmention patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Webmention[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Webmention|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Webmention saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Webmention[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Webmention[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Webmention[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Webmention[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class WebmentionsTable extends Table
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

        $this->setTable('webmentions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->scalar('source')
            ->maxLength('source', 255)
            ->requirePresence('source', 'create')
            ->notEmptyString('source');

        $validator
            ->scalar('target')
            ->notEmptyString('target');

        $validator
            ->scalar('type')
            ->notEmptyString('type');

        $validator
            ->scalar('type_id')
            ->notEmptyString('type_id')
            ->uuid('type_id');

        return $validator;
    }
}
