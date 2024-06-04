<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Delete_Interface;
use Nevamiss\Domain\Contracts\Get_All_Interface;
use Nevamiss\Domain\Contracts\Get_One_Interface;
use Nevamiss\Domain\Entities\Network_Account;


class Network_Account_Repository implements Create_Interface, Delete_Interface, Get_All_Interface, Get_One_Interface
{
    use Repository_Common;
    use Create_Trait;

    public function get_all(array $data = [])
    {
        throw new \Exception("Implement this method");
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @throws Not_Found_Exception
     */
    public function get(int $id): Network_Account
    {
        $sql = $this->wpdb->prepare("SELECT * FROM {$this->table_name()} WHERE id='%s'", $id);

        $data = $this->wpdb->get_results($sql);

        if(!$data){
            throw new Not_Found_Exception('No account found with the ID');
        }
        return $this->factory->new(Network_Account::class, $data);
    }

    private function table_name(): string
    {
        return "{$this->wpdb->prefix}_nevamiss_network_account";
    }
}