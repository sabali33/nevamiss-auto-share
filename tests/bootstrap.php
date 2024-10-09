<?php

declare(strict_types=1);

use function Patchwork\redefine;

$vendor = dirname(__DIR__) . '/vendor/';

if (!file_exists($vendor . 'autoload.php')) {
	die('Please install via Composer before running tests.');
}

require_once $vendor . 'antecedent/patchwork/Patchwork.php';
require_once $vendor . 'autoload.php';
unset($vendor);
require_once './functions.php';
