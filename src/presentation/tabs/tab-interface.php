<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Tabs;

use Nevamiss\Presentation\Components\Component;

interface Tab_Interface {

	public function label();

	public function slug();

	public function render();

	public function link( string $active_tab ): Component;

}
