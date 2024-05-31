<?php

declare(strict_types=1);

/**
 *  Plugin Name: Nevamiss Auto Share
 *  Plugin URI:  https://wordpress.org/plugins/nevamiss
 *  Description: A WordPress plugin to share content to social media Networks
 *  Author:      Eliasu Abraman
 *  Author URI:  https://saganithemes.com
 *  Text Domain: nevamiss
 *  Domain Path: /languages
 *  Version:     1.0.0
 */


namespace Nevamiss;

use Exception;
use Inpsyde\Modularity\Package;
use Inpsyde\Modularity\Properties\PluginProperties;
use Nevamiss\Application\Application_Module;
use Nevamiss\Application\DB;
use Nevamiss\Application\Plugin;
use Nevamiss\Presentation\Pages\Presentation_Module;
use Nevamiss\Service\Repositories_Module;
use Nevamiss\Services\Contracts\Services_Module;
use Throwable;

defined('ABSPATH') || die('Not authorized');

define('NEVAMISS_PATH', plugin_dir_path(__FILE__));
define('NEVAMISS_URL', plugin_dir_url(__FILE__));
define('NEVAMISS_ROOT', __FILE__);

/**
 * @throws Exception
 */
function autoload(): void
{

    if( !file_exists(NEVAMISS_PATH . '/vendor/autoload.php') ){
        throw new Exception('Autoload file can not be found');
    }

    require_once NEVAMISS_PATH . '/vendor/autoload.php';
}

function error_notice(string $message): void
{
    foreach (['admin_notices', 'network_admin_notices'] as $hook) {
        add_action(
            $hook,
            static function () use ($message) {
                $class = 'notice notice-error';

                printf(
                    '<div class="%1$s"><p>%2$s</p></div>',
                    esc_attr($class),
                    wp_kses_post($message)
                );
            }
        );
    }
}

/**
 * @throws Exception
 * @throws Throwable
 */
function plugin(): Package {
    static $package;

    if (!$package) {
        $properties = PluginProperties::new(__FILE__);
        $package = Package::new($properties);
        $package->
        addModule(new Application_Module())->
        addModule(new Repositories_Module())->
        addModule(new Presentation_Module())->
        addModule(new Services_Module());
    }

    return $package;
}

add_action(
    'plugins_loaded',
    static function(): void {

        try {
            autoload();
            plugin()->boot();
        }catch ( Throwable $exception ){
            error_notice($exception->getMessage());
        }

    }
);

register_activation_hook(

    NEVAMISS_ROOT,

    static function(){
        plugin()->container()->get(Plugin::class)->activate();
    }
);