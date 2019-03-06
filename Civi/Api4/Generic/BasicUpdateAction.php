<?php

namespace Civi\Api4\Generic;

use Civi\Api4\Generic\Result;

/**
 * Update one or more records with new values.
 *
 * Use the where clause (required) to select them.
 */
class BasicUpdateAction extends AbstractUpdateAction {

  /**
   * @var callable
   *
   * Function(array $item, BasicUpdateAction $thisAction) => array
   */
  private $setter;

  /**
   * BasicUpdateAction constructor.
   *
   * @param string $entityName
   * @param string $actionName
   * @param string|array $select
   *   One or more fields to select from each matching item.
   * @param callable $setter
   *   Function(array $item, BasicUpdateAction $thisAction) => array
   */
  public function __construct($entityName, $actionName, $select = 'id', $setter = NULL) {
    parent::__construct($entityName, $actionName, $select);
    $this->setter = $setter;
  }

  /**
   * We pass the writeRecord function an array representing one item to update.
   * We expect to get the same format back.
   *
   * @param \Civi\Api4\Generic\Result $result
   */
  public function _run(Result $result) {
    foreach ($this->getBatchRecords() as $item) {
      $result[] = $this->writeRecord($this->values + $item);
    }
  }

  /**
   * This Basic Update class can be used in one of two ways:
   *
   * 1. Use this class directly by passing a callable ($setter) to the constructor.
   * 2. Extend this class and override this function.
   *
   * Either way, this function should return an array representing the one modified object.
   *
   * @param array $item
   * @return array
   */
  protected function writeRecord($item) {
    return call_user_func($this->setter, $item, $this);
  }

}