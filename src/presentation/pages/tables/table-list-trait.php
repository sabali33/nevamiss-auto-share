<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages\Tables;

trait Table_List_Trait
{
	/**
	 * @param object $item
	 * @param $column_name
	 * @param $primary
	 * @return array|string
	 */
	protected function handle_row_actions( $item, $column_name, $primary ): array|string
	{
		if ($primary !== $column_name) {
			return '';
		}
		$actions = array_map(
			function ( $action ) {
				return $this->link( $action );
			},
			$this->action_list( $item )
		);
		return $this->row_actions($actions);
	}
	private function link(array $action): string
	{
		if ( ! isset( $action['url'] ) ) {
			return '';
		}
		$title = $action['label'] ?? __( 'no label', 'nevamiss' );
		$class = $action['class'] ?? '';
		return "<span class='$class'><a href='{$action['url']}' title='$title' class='$class'> $title</a></span>";
	}
	protected function _bulk_actions(): array
	{
		return [
			'delete_all' => __('Delete', 'nevamiss')
		];
	}
	public function _column_cb( $item, string $input_name): void
	{
		$show = current_user_can( 'manage_options', $item->id() );

		if(!$show){
			return;
		}
		?>
		<input id="cb-select-<?php esc_attr_e($item->id()); ?>" type="checkbox" name="<?php echo esc_attr($input_name); ?>[]" value="<?php esc_attr_e($item->id()); ?>" />
		<label for="cb-select-<?php esc_attr_e($item->id()); ?>">
			<span class="screen-reader-text">
			<?php
			/* translators: %s: Post title. */
			printf( __( 'Select %s' ), $item->name() );
			?>
			</span>
		</label>
		<div class="locked-indicator">
			<span class="locked-indicator-icon" aria-hidden="true"></span>
			<span class="screen-reader-text">
			<?php
			printf(
			/* translators: Hidden accessibility text. %s: Post title. */
				__( '&#8220;%s&#8221; is locked' ),
				$item->name()
			);
			?>
			</span>
		</div>
		<?php

	}

	/**
	 * @return array|string
	 */
	private function search_text(): string|array
	{
		return isset($_REQUEST['s']) ? wp_unslash(trim($_REQUEST['s'])) : '';
	}
	/**
	 * @return array
	 */
	private function query_args(array $parameters): array
	{
		$search = $this->search_text();
		$per_page = 10;
		$paged = $this->get_pagenum();

        $search_field = $parameters['search_field'] ?? 'name';
		$args = array(
			'per_page' => $per_page,
			'offset' => ($paged - 1) * $per_page,
			'search' => [$search_field, $search],
		);

		if (isset($_REQUEST['orderby'])) {
			$args['orderby'] = $_REQUEST['orderby'];
		}

		if (isset($_REQUEST['order'])) {
			$args['order'] = $_REQUEST['order'];
		}
		return array($per_page, $args);
	}
}