<?php

declare(strict_types=1);

use Nevamiss\Presentation\Tabs\General_Tab;
use Nevamiss\Presentation\Tabs\Logs_Tab;
use Nevamiss\Presentation\Tabs\Network_Accounts_Tab;
use Nevamiss\Presentation\Tabs\Stats_Tab;
use Nevamiss\Presentation\Pages\Settings_Page;

/**
 * @var Settings_Page $this
 */

//$networks = $this->network_collection();
//
//$enabled_networks = $this->settings()->enabled_networks();

//foreach( $enabled_networks as $enabled_network){
//	$network = ucfirst($enabled_network);
//	/**
//	 * @var Network_Clients_Interface $client
//	 */
//	$client = $networks->get($enabled_network);
//
//	echo "<a href='{$client->auth_link()}'> Login to $network</a>";
//}
?>

<?php

$active_tab = $_GET['tab'] ?? 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$tabs = $this->tabs();

?>
<div class="wrap">
    <?php $this->notices(); ?>
<h2>
<?php esc_html_e('Settings', 'nevamiss'); ?>
</h2>
<hr>
<h2 class="nav-tab-wrapper">

<?php
	foreach ($tabs as $tab){
		echo $tab->link($active_tab)->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
?>
</h2>

    <div class="tab-content">
        <?php
        switch ($active_tab) {
            case Logs_Tab::SLUG:
	            echo "Logs";
                break;
            case Network_Accounts_Tab::SLUG:
	            echo $this->tab(Network_Accounts_Tab::SLUG)->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                break;
            case Stats_Tab::SLUG:
	            echo $this->tab(Stats_Tab::SLUG)->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                break;
            default:
	            echo $this->tab(General_Tab::SLUG)->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped


        }
        ?>
    </div>
</div>
