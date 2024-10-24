<?php

declare(strict_types=1);

namespace Nevamiss\Tests\Unit\Presentation;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Nevamiss\Infrastructure\Networks\Media_Network_Collection;
use Nevamiss\Presentation\Pages\Settings_Page;
use Nevamiss\Presentation\Tabs\Tab_Collection;
use Nevamiss\Presentation\Tabs\Tab_Interface;
use Nevamiss\Services\Settings;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function Brain\Monkey\Functions\expect;
use function Brain\Monkey\Functions\stubs;
use function Brain\Monkey\Functions\stubTranslationFunctions;
use function Brain\Monkey\Functions\when;
use function Brain\Monkey\setUp;

#[CoversClass(Settings_Page::class)]
class SettingsPageTest extends TestCase
{
	use MockeryPHPUnitIntegration;

	public static function settingsSection(): array
	{
		return [
			['general'], //Section and whether setting has been saved already
			['general'],
			['network_api_keys'],
			['post']
		];
	}

	public static function tabs()
	{
		return [
			['general'],
			['general'],
			['network-accounts'],
			['stats'],
			['logs'],
			['upgrade'],
		];
	}

	public static function settingsExists()
	{
		return [
			[true],
			[false]
		];
	}

	protected function setUp(): void
	{
		parent::setUp(); // TODO: Change the autogenerated stub
		setUp();
	}

	#[DataProvider('settingsSection')]
	public function test_it_can_save_settings(string $section)
	{
		$this->setSettingsFormData($section);

		$expectedData = [
			'general' => [
				'repeat_cycle'        => '1',
				'pause_all_schedules' => '1',
				'keep_records'        => '1',
				'logging_option'      => 'database',
			],
			'network_api_keys' => [
				'networks_to_post' => ['facebook', 'x'],
				'facebook' => ['client_id' => 'ertrtrty', 'client_secret' => 'sfhfrt', 'app_configuration' => 'sfdfgd'],
				'linkedin' => ['client_id' => 'ertrtrty', 'client_secret' => 'sfhfrt'],
				'instagram' => ['client_id' => 'ertrtrty', 'client_secret' => 'sfhfrt'],
				'x' => ['client_id' => 'ertrtrty', 'client_secret' => 'sfhfrt', 'version' => 'v2'],
				'url_shortner_client' => 'rebrandly',
				'rebrandly' => [ 'api_key' => 'sdhdfkd', 'shortlink' => '']
			],
			'post' => [
				'share_on_publish' => array('post')
			]
		];

		$settingsMock = $this->createMock(Settings::class);
		$mediaCollectionMock = $this->createMock(Media_Network_Collection::class);
		$tabCollectionMock = $this->createMock(Tab_Collection::class);
		stubTranslationFunctions();

		stubs([
			'admin_url' => 'https://sagani-site.ddev.site/wp-admin/',
			'add_query_arg' => 'https://sagani-site.ddev.site/wp-admin/admin.php?page=nevamiss-settings',
			'wp_verify_nonce' => true,
			'wp_safe_redirect' => function(){
				throw new \Exception('Exiting');
			},
		]);

		when('wp_unslash')->returnArg();
		when('sanitize_text_field')->returnArg();
		when('map_deep')->returnArg();

		expect('get_option')->with(Settings_Page::GENERAL_SETTINGS)->andReturnNull();
		expect('update_option')->with(
			Settings_Page::GENERAL_SETTINGS,
			array( $section => $expectedData[$section] )
		);


		$settingsPage = new Settings_Page($settingsMock,$mediaCollectionMock, $tabCollectionMock);
		$this->assertNotEmpty($_POST);
		try{
			$settingsPage->save_form();
		}catch (\Throwable $throwable){
			$this->assertSame('Exiting', $throwable->getMessage());
		}

	}
	private function setSettingsFormData(string $section): void
	{
		switch ($section){
			case 'general':
				$_POST['repeat_cycle'] = '1';
				$_POST['pause_all_schedules'] = '1';
				$_POST['keep_records'] = '1';
				$_POST['logging_option'] = 'database';
				break;
			case 'network_api_keys':
				$_POST['networks_to_post'] = ['facebook', 'x'];
				$_POST['facebook'] = ['client_id' => 'ertrtrty', 'client_secret' => 'sfhfrt', 'app_configuration' => 'sfdfgd'];
				$_POST['linkedin'] = ['client_id' => 'ertrtrty', 'client_secret' => 'sfhfrt'];
				$_POST['instagram'] = ['client_id' => 'ertrtrty', 'client_secret' => 'sfhfrt'];
				$_POST['x'] = ['client_id' => 'ertrtrty', 'client_secret' => 'sfhfrt', 'version' => 'v2'];
				$_POST['url_shortner_client'] = 'rebrandly';
				$_POST['rebrandly'] = [ 'api_key' => 'sdhdfkd', 'shortlink' => ''];
				break;
			default:
				$_POST['share_on_publish'] = array('post');
		}

		$_POST['section'] = $section;
		$_POST['_wpnonce'] = 'eywhrysd';

	}

	#[DataProvider('tabs')]
	public function test_it_can_render_tabs(string $tab)
	{
		$settingsMock = $this->createMock(Settings::class);
		$mediaCollectionMock = $this->createMock(Media_Network_Collection::class);
		$tabCollectionMock = $this->createMock(Tab_Collection::class);
		$tabCollectionMock->method('tab_exists')->with($tab)->willReturn(true);
		$tabCollectionMock->expects($this->once())->method('get')->with($tab);

		$settingsPage = new Settings_Page($settingsMock,$mediaCollectionMock, $tabCollectionMock);

		$tabObject = $settingsPage->render_tab($tab);
		if($tabObject){
			self::assertInstanceOf(Tab_Interface::class, $tabObject);
		}else{
			self::assertNull($tabObject);
		}

	}
}