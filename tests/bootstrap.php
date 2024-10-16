<?php

declare(strict_types=1);

use function Brain\Monkey\Functions\stubEscapeFunctions;

$vendor = dirname(__DIR__) . '/vendor/';

if (!file_exists($vendor . 'autoload.php')) {
	die('Please install via Composer before running tests.');
}

define("NEVAMISS_PATH", dirname(__DIR__) . "/");
define( 'ABSPATH',   realpath(dirname(__DIR__) . "/../../../public/wp") . '/');
define( 'WP_CONTENT_DIR',   realpath(dirname(__DIR__) . "/../../../public/wp/wp-content") . '/');
//define( 'WP_LANG_DIR',   realpath(dirname(__DIR__) . "/../../../public/wp/wp-content/languages") . '/');
//stubEscapeFunctions();
require_once( ABSPATH . 'wp-load.php' );

//Brain\Monkey\Functions\stubTranslationFunctions();

if ( ! function_exists( 'WP_Filesystem' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
}

WP_Filesystem();

require_once $vendor . 'antecedent/patchwork/Patchwork.php';
require_once $vendor . 'autoload.php';
unset($vendor);
require_once './functions.php';
