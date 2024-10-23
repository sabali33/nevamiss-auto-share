<?php

declare(strict_types=1);

namespace Nevamiss\Tests\Unit\Presentation;

use Brain\Monkey\Expectation\Exception\ExpectationArgsRequired;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Presentation\Pages\Schedule_Form;
use Nevamiss\Services\Form_Validator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use function Brain\Monkey\Functions\expect;
use function Brain\Monkey\Functions\stubEscapeFunctions;
use function Brain\Monkey\Functions\stubs;
use function Brain\Monkey\Functions\stubTranslationFunctions;
use function Brain\Monkey\Functions\when;
use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;

#[CoversClass(Schedule_Form::class)]
class ScheduleFormTest extends TestCase {
	use MockeryPHPUnitIntegration;

	public static function sanitizationValidationProvider(): array
	{
		return [
			['daily', 'sanitize_assoc_array_of_numbers', 'validate_assoc_array_of_numbers'],
			['weekly', ['sanitize_array_of_string', 'sanitize_number'], null],
			['monthly', 'sanitize_assoc_array_of_numbers', 'validate_assoc_array_of_numbers'],
			['none', 'sanitize_date', null],
		];
	}

	protected function setUp(): void
	{
		parent::setUp(); // TODO: Change the autogenerated stub
		setUp();
	}

	/**
	 * @throws Exception
	 * @throws ExpectationArgsRequired
	 * @throws \ReflectionException|Not_Found_Exception
	 */
	public function test_it_can_authorize_form_submission()
	{
		$scheduleRepositoryMock = $this->createMock(Schedule_Repository::class);
		$networkAccountRepoMock = $this->createMock(Network_Account_Repository::class);
		$formValidatorMock = $this->createMock(Form_Validator::class);
		$factoryMock = $this->createMock(Factory::class);
		stubTranslationFunctions();
		$_POST['_wpnonce'] = '437ruerher';
		stubs([
			'admin_url' => 'https://sagani-site.ddev.site/wp-admin/',
			'is_null' => false,
		]);
		when('wp_unslash')->returnArg();
		when('Nevamiss\sanitize_text_input_field')->justReturn('437ruerher');

		$form = new \ReflectionClass(Schedule_Form::class);
		$method = $form->getMethod ('is_authorized');
		$method->setAccessible(true);

		$formMocked = new Schedule_Form($scheduleRepositoryMock, $networkAccountRepoMock, $formValidatorMock, $factoryMock);
		expect('wp_verify_nonce')->once()->with($_POST['_wpnonce'], 'nevamiss_create_schedule');

		$method->invoke($formMocked);

	}

	public static function provideScheduleId()
	{
		return [
			[1],
			[null]
		];
	}

	#[DataProvider('provideScheduleId')]
	public function test_it_can_register_form_page(?int $schedule_id)
	{
		$scheduleRepositoryMock = $this->createMock(Schedule_Repository::class);
		$networkAccountRepoMock = $this->createMock(Network_Account_Repository::class);
		$formValidatorMock = $this->createMock(Form_Validator::class);
		$factoryMock = $this->createMock(Factory::class);
		stubTranslationFunctions();
		stubs([
			'admin_url' => 'https://sagani-site.ddev.site/wp-admin/',
		]);
		$_REQUEST['schedule_id'] = $schedule_id;

		$form = new Schedule_Form($scheduleRepositoryMock, $networkAccountRepoMock, $formValidatorMock, $factoryMock);

		expect('add_submenu_page')->once()->with(null, $form->title(), $form->title(), 'manage_options', 'edit-schedule', [$form, 'render']);
		$form->register();
	}

	/**
	 * @throws Not_Found_Exception
	 * @throws Exception
	 * @throws \ReflectionException
	 * @throws \Exception
	 */
	#[DataProvider('sanitizationValidationProvider')]
	public function test_it_can_sanitize_post_data(string $repeat_frequency, string|array $sanitize_func, ?string $validate_func)
	{
		$times = [
			'weekly' => ['days' => ['monday'], 'hours' => [13], 'minutes' => [30]],
			'monthly' => ['days' => [10], 'hours' => [13], 'minutes' => [30]],
			'daily' => ['hours' => [13], 'minutes' => [30]],
			'none' => ['2024-10-18'],
		];
		$scheduleRepositoryMock = $this->createMock(Schedule_Repository::class);
		$networkAccountRepoMock = $this->createMock(Network_Account_Repository::class);

		$formValidatorMock = $this->createMock(Form_Validator::class);
		$formValidatorMock->expects($this->atLeast(2))->method('sanitize_string');
		$formValidatorMock->expects($this->atLeast(2))->method('sanitize_array_of_string');
		$formValidatorMock->expects($this->atLeast(1))->method('sanitize_date');

		if(is_array($sanitize_func)){
			foreach ($sanitize_func as $func){
				$formValidatorMock->expects($this->atLeast(1))->method($func);
			}
		}else{
			if('none' !== $repeat_frequency){
				$formValidatorMock->expects($this->once())->method($sanitize_func)->with($times[$repeat_frequency]);

			}else{
				$formValidatorMock->expects($this->once())->method($sanitize_func);
			}

		}

		if($validate_func){
			$formValidatorMock->expects($this->once())->method($validate_func);
		}

		$factoryMock = $this->createMock(Factory::class);
		stubTranslationFunctions();

		stubs([
			'admin_url' => 'https://sagani-site.ddev.site/wp-admin/',
		]);

		$_POST['_wpnonce'] = '437ruerher';
		$form = new \ReflectionClass(Schedule_Form::class);
		$method = $form->getMethod ('sanitize_validation_func');
		$method->setAccessible(true);

		$formMocked = new Schedule_Form($scheduleRepositoryMock, $networkAccountRepoMock, $formValidatorMock, $factoryMock);
		$schedule_post_data = [
			'schedule_name' => 'example name',
			'repeat_frequency' => $repeat_frequency,
			'start_date' => '2024-10-10',
			'weekly_times' => null,
			'monthly_times' => null,
			'network_accounts' => [2],
			'query_args' => ['per_page' => 2, 'post_type' => 'post'],
			'one_time_schedule' => null,
		];

		if( 'none' !== $repeat_frequency){
			$schedule_post_data["{$repeat_frequency}_times" ] = $times[$repeat_frequency];
		}
		foreach (
			$schedule_post_data as $post_field => $value){
			$method->invoke($formMocked, $post_field)($value);
		}
	}


	public function test_it_can_create_daily_schedule()
	{
		$this->setSchedulePostData();
		$allowedData = [
			'schedule_name' => 'daily',
			'repeat_frequency' => 'daily',
			'start_date' => '2024-14-11',
			'daily_times' => ['hours' => [15], 'minutes' => [30]],
			'network_accounts' => [2,3,4],
			'query_args' => ['per_page' => 2, 'post_type' => 'post'],
		];
		$formatedData = array_merge(
			$allowedData,
			[
				'daily_times' => '[{"hour":15,"minute":30}]',
				'query_args' => wp_json_encode(['per_page' => 2, 'post_type' => 'post']),
				'network_accounts' => '[2,3,4]'
			]
		);
		when('wp_unslash')->returnArg();

		$scheduleRepositoryMock = $this->createMock(Schedule_Repository::class);
		$scheduleRepositoryMock->method('allowed_data')->willReturn($allowedData);
		$scheduleRepositoryMock->method('allow_columns')->willReturn([
			'schedule_name',
			'repeat_frequency',
			'start_date',
			'daily_times',
			'network_accounts',
			'query_args'
		]);
		$networkAccountRepoMock = $this->createMock(Network_Account_Repository::class);

		$formValidatorMock = $this->createMock(Form_Validator::class);
		$formValidatorMock->method('sanitize_string')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_array_of_string')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_date')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_assoc_array_of_numbers')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_number')->willReturnArgument(0);
		when('wp_unslash')->returnArg();
		when('sanitize_text_field')->returnArg();

		$factoryMock = $this->createMock(Factory::class);
		stubTranslationFunctions();
		stubEscapeFunctions();

		stubs([
			'admin_url' => 'https://sagani-site.ddev.site/wp-admin/',
			'add_query_arg' => 'https://sagani-site.ddev.site/wp-admin/admin.php?page=nevamiss-schedules',
			'wp_verify_nonce' => true,
			'wp_safe_redirect' => function(){ throw new \Exception('Exiting'); },
		]);

		$form = new Schedule_Form($scheduleRepositoryMock, $networkAccountRepoMock, $formValidatorMock, $factoryMock);
		$scheduleRepositoryMock->expects($this->once())->method('create')->with($formatedData);
		try {
			$form->maybe_save_form();
		}catch (\Throwable $throwable){
			$this->assertSame('Exiting', $throwable->getMessage());
		}

	}

	public function test_it_can_update_existing_schedule()
	{
		$this->setSchedulePostData();
		$_POST['schedule_id'] = '1';

		$allowedData = [
			'schedule_name' => 'daily',
			'repeat_frequency' => 'daily',
			'start_date' => '2024-14-11',
			'daily_times' => ['hours' => [15], 'minutes' => [30]],
			'network_accounts' => [2,3,4],
			'query_args' => ['per_page' => 2, 'post_type' => 'post'],
		];
		$formatedData = array_merge(
			$allowedData,
			[
				'daily_times' => '[{"hour":15,"minute":30}]',
				'query_args' => wp_json_encode(['per_page' => 2, 'post_type' => 'post']),
				'network_accounts' => '[2,3,4]'
			]
		);

		$scheduleRepositoryMock = $this->createMock(Schedule_Repository::class);
		$scheduleRepositoryMock->method('allowed_data')->willReturn($allowedData);
		$scheduleRepositoryMock->method('allow_columns')->willReturn([
			'schedule_name',
			'repeat_frequency',
			'start_date',
			'daily_times',
			'network_accounts',
			'query_args'
		]);

		$networkAccountRepoMock = $this->createMock(Network_Account_Repository::class);

		$formValidatorMock = $this->createMock(Form_Validator::class);
		$formValidatorMock->method('sanitize_string')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_array_of_string')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_date')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_assoc_array_of_numbers')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_number')->willReturnArgument(0);
		when('wp_unslash')->returnArg();
		when('sanitize_text_field')->returnArg();

		$factoryMock = $this->createMock(Factory::class);
		stubTranslationFunctions();
		stubEscapeFunctions();

		stubs([
			'admin_url' => 'https://sagani-site.ddev.site/wp-admin/',
			'add_query_arg' => 'https://sagani-site.ddev.site/wp-admin/admin.php?page=nevamiss-schedules',
			'wp_verify_nonce' => true,
			'wp_safe_redirect' => function(){ throw new \Exception('Exiting'); },
			'is_null' => false
		]);

		$form = new Schedule_Form($scheduleRepositoryMock, $networkAccountRepoMock, $formValidatorMock, $factoryMock);
		$scheduleRepositoryMock->expects($this->once())->method('update')->with(1, $formatedData);
		try {
			$form->maybe_save_form();
		}catch (\Throwable $throwable){
			$this->assertSame('Exiting', $throwable->getMessage());
		}

	}

	public function test_it_can_redirect_on_validation_error()
	{
		$scheduleRepositoryMock = $this->createMock(Schedule_Repository::class);

		$networkAccountRepoMock = $this->createMock(Network_Account_Repository::class);

		$formValidatorMock = $this->createMock(Form_Validator::class);
		$formValidatorMock->method('errors')->willReturn(['Example error']);


		$formValidatorMock->method('sanitize_string')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_array_of_string')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_date')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_assoc_array_of_numbers')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_number')->willReturnArgument(0);

		$factoryMock = $this->createMock(Factory::class);
		stubTranslationFunctions();
		stubEscapeFunctions();

		stubs([
			'admin_url' => 'https://sagani-site.ddev.site/wp-admin/',
			'add_query_arg' => 'https://sagani-site.ddev.site/wp-admin/admin.php?page=nevamiss-schedules',
			'wp_verify_nonce' => true,
			'wp_safe_redirect' => function(){ throw new \Exception('Example error'); },
		]);

		$form = new Schedule_Form($scheduleRepositoryMock, $networkAccountRepoMock, $formValidatorMock, $factoryMock);

		$scheduleRepositoryMock->expects($this->never())->method('create');
		$scheduleRepositoryMock->expects($this->never())->method('update');

		try {
			$form->maybe_save_form();
		}catch (\Throwable $throwable){
			$this->assertSame('Example error', $throwable->getMessage());
		}
	}
	protected function tearDown(): void
	{
		parent::tearDown(); // TODO: Change the autogenerated stub
		tearDown();
	}

	public function setSchedulePostData()
	{
		$_POST['schedule_name'] = 'daily';
		$_POST['repeat_frequency'] = 'daily';
		$_POST['start_date'] = '2024-14-11';
		$_POST['daily_times'] = ['hours' => [15], 'minutes' => [30]];
		$_POST['network_accounts'] = [2,3,4];
		$_POST['query_args'] = ['per_page' => 2, 'post_type' => 'post'];
		$_POST['_wpnonce'] = '437ruerher';
	}
	public function setWeeklySchedulePostData()
	{
		$_POST['start_date'] = '2024-14-11';
		$_POST['repeat_frequency'] = 'weekly';
		$_POST['schedule_name'] = 'weekly';
		$_POST['weekly_times'] = ['days' => ['monday'], 'hours' => [15], 'minutes' => [30]];
		$_POST['daily_times'] = [['hours' => [15], 'minutes' => [30]]];
		$_POST['network_accounts'] = [2,3,4];
		$_POST['query_args'] = ['per_page' => 2, 'post_type' => 'post'];
		$_POST['_wpnonce'] = '437ruerher';
	}
	public function test_it_can_create_weekly_schedule()
	{
		$this->setWeeklySchedulePostData();

		$allowedData = [
			'schedule_name' => 'weekly',
			'repeat_frequency' => 'weekly',
			'start_date' => '2024-14-11',
			'weekly_times' => ['days' => ['monday'], 'hours' => [15], 'minutes' => [30]],
			'network_accounts' => [2,3,4],
			'query_args' => ['per_page' => 2, 'post_type' => 'post'],
		];
		$formatedData = array_merge(
			$allowedData,
			[
				'weekly_times' => '[{"day":"monday","hour":15,"minute":30}]',
				'query_args' => wp_json_encode(['per_page' => 2, 'post_type' => 'post']),
				'network_accounts' => '[2,3,4]'
			]
		);

		$scheduleRepositoryMock = $this->createMock(Schedule_Repository::class);

		$scheduleRepositoryMock->method('allow_columns')->willReturn([
			'schedule_name',
			'repeat_frequency',
			'start_date',
			'weekly_times',
			'network_accounts',
			'query_args'
		]);
		$networkAccountRepoMock = $this->createMock(Network_Account_Repository::class);

		$formValidatorMock = $this->createMock(Form_Validator::class);
		$formValidatorMock->method('sanitize_string')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_array_of_string')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_date')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_assoc_array_of_numbers')->willReturnArgument(0);
		$formValidatorMock->method('sanitize_number')->willReturnArgument(0);
		when('wp_unslash')->returnArg();
		when('sanitize_text_field')->returnArg();

		$factoryMock = $this->createMock(Factory::class);
		stubTranslationFunctions();
		stubEscapeFunctions();

		stubs([
			'admin_url' => 'https://sagani-site.ddev.site/wp-admin/',
			'add_query_arg' => 'https://sagani-site.ddev.site/wp-admin/admin.php?page=nevamiss-schedules',
			'wp_verify_nonce' => true,
			'wp_safe_redirect' => function(){
				throw new \Exception('Exiting');
			},
		]);

		$form = new Schedule_Form($scheduleRepositoryMock, $networkAccountRepoMock, $formValidatorMock, $factoryMock);
		$scheduleRepositoryMock->expects($this->once())->method('create')->with($formatedData);
		try {
			$form->maybe_save_form();
		}catch (\Throwable $throwable){
			$this->assertSame('Exiting', $throwable->getMessage());
		}

	}
}