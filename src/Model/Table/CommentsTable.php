<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CommentsTable extends Table
{
  public function initialize(array $config)
  {
    parent::initialize($config);

    $this->setTable('comments');
    $this->setDisplayField('id');
    $this->setPrimaryKey('id');

    $this->addBehavior('Timestamp');

    $this->belongsTo('Posts', [
      'foreignKey' => 'model_id',
      'joinType' => 'INNER',
    ]);

    $this->belongsTo('Medias', [
      'foreignKey' => 'model_id',
      'joinType' => 'INNER',
    ]);

    $this->belongsTo('Albums', [
      'foreignKey' => 'model_id',
      'joinType' => 'INNER',
    ]);

  }

  public function validationDefault(Validator $validator)
  {
    $validator
      ->uuid('id')
      ->allowEmptyString('id', null, 'create');

    $validator
      ->scalar('comment')
      ->requirePresence('comment', 'create')
      ->notEmptyString('comment');

    $validator
      ->boolean('approved')
      ->allowEmptyString('approved');

    $validator
      ->boolean('public')
      ->allowEmptyString('public');

    $validator
      ->scalar('posted_by')
      ->maxLength('posted_by', 255)
      ->requirePresence('posted_by', 'create')
      ->notEmptyString('posted_by');

    return $validator;
  }

  public function buildRules(RulesChecker $rules)
  {
      // TODO: Figure out how to fix this
      // $rules->add($rules->existsIn(['model_id'], 'Models'));

      return $rules;
  }
}
