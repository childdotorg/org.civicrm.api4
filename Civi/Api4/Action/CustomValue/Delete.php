<?php

namespace Civi\Api4\Action\CustomValue;

/**
 * Delete one or more items, based on criteria specified in Where param.
 */
class Delete extends \Civi\Api4\Generic\Action\DAO\Delete {
  use \Civi\Api4\Generic\CustomValueCRUD;

}
