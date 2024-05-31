<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Delete_All_Interface;
use Nevamiss\Domain\Contracts\Delete_Interface;
use Nevamiss\Domain\Contracts\Get_All_Interface;
use Nevamiss\Domain\Contracts\Get_One_Interface;
use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Factory\Factory;


class Network_Account_Repository implements Create_Interface, Delete_Interface, Get_All_Interface, Get_One_Interface
{
    public function __construct(private readonly Factory $factory, private \wpdb $wpdb)
    {

    }
    public function create(mixed $data)
    {
        throw new \Exception("Implement this method");
    }

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
        $sql = $this->wpdb->prepare("SELECT * FROM {$this->wpdb->prefix}_nevamiss_network_account WHERE id='%s'", $id);

        $data = $this->wpdb->get_results($sql);

        if(!$data){
            throw new Not_Found_Exception('No account found with the ID');
        }
        return $this->factory->new(Network_Account::class, $data);
    }
}