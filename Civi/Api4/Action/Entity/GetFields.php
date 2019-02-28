<?php

namespace Civi\Api4\Action\Entity;

use Civi\Api4\Generic\Result;
use Civi\Api4\Generic\Action\DAO\GetFields as GenericGetFields;

/**
 * Get fields for all entities
 */
class GetFields extends GenericGetFields {

  public function _run(Result $result) {
    $action = $this->getAction();
    $includeCustom = $this->getIncludeCustom();
    $entities = \Civi\Api4\Entity::get()->execute();
    foreach ($entities as $entity) {
      $entity = ((array) $entity) + ['fields' => []];
      // Prevent infinite recursion
      if ($entity['name'] != 'Entity') {
        $entity['fields'] = (array) civicrm_api4($entity['name'], 'getFields', ['action' => $action, 'includeCustom' => $includeCustom, 'select' => $this->select]);
      }
      else {
        $entity['fields'] = [
          [
            'name' => 'name',
            'title' => 'Name',
            'data_type' => 'String',
          ],
          [
            'name' => 'description',
            'title' => 'Description',
            'data_type' => 'String',
          ],
          [
            'name' => 'comment',
            'title' => 'Comment',
            'data_type' => 'String',
          ],
        ];
      }
      $result[] = $entity;
    }
  }

  /**
   * @inheritDoc
   */
  public function getParamInfo($param = NULL) {
    $info = parent::getParamInfo($param);
    if (!$param) {
      // This action doesn't actually let you select fields.
      unset($info['fields']);
    }
    return $info;
  }

}
