<?php

declare(strict_types=1);

namespace Nevamiss\Infrastructure\Url_Shortner;

interface URL_Shortner_Response_Interface
{
	public function id();
	public function short_url();
	public function status();
	public function is_public();

}