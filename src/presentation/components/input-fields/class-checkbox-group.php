<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Components\Input_Fields;
;
use Nevamiss\Presentation\Components\Renderable;
use Nevamiss\Presentation\Components\Wrapper;
use function Nevamiss\component;
use function Nevamiss\plugin;

class Checkbox_Group implements Renderable {


	/**
	 * @param $attributes
	 * @return string
	 */
	public function render($attributes = array()): string
	{
		['choices' => $choices] = $attributes;

		$fields = [];
		foreach($choices as $choice){
			$fields[] = component(Input::class, [
				'type' => 'checkbox',
				'name' => $attributes['name'],
				'value' => $choice,
				'label' => ucfirst($choice),
				'checked' => in_array($choice, $attributes['value'])
			]);
		}
		$heading = component(
			Wrapper::class,
			[
				'tag' => 'h2',
				'text' => $attributes['label']
			]
		);
		return component(Wrapper::class,
			[
			'attributes' => [
				'class' => 'checkbox-group'
			]
		],
		[
			$heading,
			...$fields
		]
		)->render();
	}
}