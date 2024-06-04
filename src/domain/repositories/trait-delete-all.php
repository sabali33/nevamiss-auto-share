<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Exception;

trait Delete_All_Trait  {

    /**
     * @throws Exception
     */
    public function clear(int $id): bool
    {
        $sql = "DELETE * FROM {$this->table_name()}";

        $entity = $this->wpdb->query($sql);

        if(!$entity){
            throw new Exception( "Unable to clear records for self::ENTITY_NAME");
        }
        return true;
    }
}