<?php

declare(strict_types=1);

namespace Nevamiss\Infrastructure\Url_Shortner;

class Url_Shortner_Response implements URL_Shortner_Response_Interface{

	public function __construct(
		private string $url,
		private string $id,
		private bool $status,
		private bool $is_public
	)
	{
	}

	/**
	 * @return string
	 */
	public function id(): string
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function short_url(): string
	{
		return $this->url;
	}

	public function status(): bool
	{
		return $this->status;
	}

	public function is_public(): bool
	{
		return $this->is_public;
	}

	public function __toString()
	{
		return wp_json_encode([
			'id' => $this->id(),
			'status' => $this->status(),
			'is_public' => $this->is_public(),
			'short_url' => $this->short_url(),
		]);
	}
}