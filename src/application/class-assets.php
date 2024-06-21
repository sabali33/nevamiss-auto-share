<?php

declare(strict_types=1);

namespace Nevamiss\Application;

class Assets {

	public function enqueue_script()
	{
		['dependencies' => $deps, 'version' => $version ] = require NEVAMISS_PATH . '/build/main.asset.php';
		wp_register_script('nevamiss-scripts', NEVAMISS_URL . '/build/main.js', $deps, $version, true);
		wp_register_style('nevamiss-flatpickr-style', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
		wp_register_style('nevamiss-style', NEVAMISS_URL . '/css/style.css');
		wp_enqueue_script('nevamiss-scripts');
		wp_enqueue_style('nevamiss-flatpickr-style');
		wp_enqueue_style('nevamiss-style');
	}
}