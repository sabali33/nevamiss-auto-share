<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;


use Nevamiss\Domain\Factory\Factory;

trait RepositoryCommon {
    public function __construct(private Factory $factory, private \wpdb $wpdb)
    {
    }
}