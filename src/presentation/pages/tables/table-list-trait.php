<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages\Tables;

use function Nevamiss\sanitize_text_input_field;

trait Table_List_Trait {

	/**
	 * @param object      $item
	 * @param $column_name
	 * @param $primary
	 * @return array|string
	 */
	protected function handle_row_actions( $item, $column_name, $primary ): array|string {
		if ( $primary !== $column_name ) {
			return '';
		}
		$actions = array_map(
			function ( $action ) {
				return $this->link( $action );
			},
			$this->action_list( $item )
		);
		return $this->row_actions( $actions );
	}
	private function link( array $action ): string {
		if ( ! isset( $action['url'] ) ) {
			return '';
		}
		$title      = isset( $action['label'] ) ? esc_html( $action['label'] ) : __( 'no label', 'nevamiss' );
		$class      = isset( $action['class'] ) ? esc_attr( $action['class'] ) : '';
		$title_attr = isset( $action['label'] ) ? esc_attr( $title ) : '';
		$url        = esc_url( $action['url'] );
		return "<span class='$class'><a href='{$url}' title='$title_attr' class='$class'> $title</a></span>";
	}
	protected function _bulk_actions(): array {
		return array(
			'delete_all' => __( 'Delete', 'nevamiss' ),
		);
	}
	public function _column_cb( $item, string $input_name ): void {
		$show = current_user_can( 'manage_options', $item->id() );

		if ( ! $show ) {
			return;
		}
		?>
		<input id="cb-select-<?php echo esc_attr( $item->id() ); ?>" type="checkbox" name="<?php echo esc_attr( $input_name ); ?>[]" value="<?php echo esc_attr( $item->id() ); ?>" />
		<label for="cb-select-<?php echo esc_attr( $item->id() ); ?>">
			<span class="screen-reader-text">
			<?php
			if ( method_exists( $item, 'name' ) ) {
				/* translators: %s: An item name */
				printf( esc_html__( 'Select %s' ), esc_html( $item->name() ) );
			}
			?>
			</span>
		</label>
		<div class="locked-indicator">
			<span class="locked-indicator-icon" aria-hidden="true"></span>
			<span class="screen-reader-text">
			<?php
			if ( method_exists( $item, 'name' ) ) {
				printf(
				/* translators: Hidden accessibility text. %s: Post title. */
					esc_html__( '&#8220;%s&#8221; is locked' ),
					esc_html( $item->name() )
				);
			}

			?>
			</span>
		</div>
		<?php
	}

	/**
	 * @return array|string
	 */
	private function search_text(): string|array {
		$search_text = sanitize_text_input_field( 's' );
		return $search_text ? trim($search_text) : '';
	}

	/**
	 * @param array $parameters
	 * @return array
	 */
	private function query_args( array $parameters ): array {
		$search   = $this->search_text();
		$per_page = 10;
		$paged    = $this->get_pagenum();

		$search_field = $parameters['search_field'] ?? 'name';
		$args         = array(
			'per_page' => $per_page,
			'offset'   => ( $paged - 1 ) * $per_page,
			'search'   => array( $search_field, $search ),
		);

		if ( isset( $_REQUEST['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$args['orderby'] = sanitize_text_input_field('orderby');
		}

		if ( isset( $_REQUEST['order'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$args['order'] = sanitize_text_input_field('order');
		}
		return array( $per_page, $args );
	}
}