<?php
/**
 * WP Component Library includes: Table Block class
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks\Core;

use PHPHtmlParser\Dom\Node\HtmlNode;
use WP_Component_Library\Blocks\Anonymous;

/**
 * Renders a Table block as a WPCL component.
 */
class Table extends Anonymous {

	/**
	 * Render the the block.
	 *
	 * @return void
	 */
	public function render(): void {

		$this->root = $this->dom_parser->firstChild();

		foreach ( $this->root->getAttributes() as $attr => $value ) {
			$this->attrs[ $attr ] = $value;
		}

		$this->attrs['header']   = $this->extract_header();
		$this->attrs['footer']   = $this->extract_footer();
		$this->attrs['body']     = $this->extract_body();
		$this->attrs['citation'] = optional( $this->root->find( 'figcaption', 0 ) )->innerHtml() ?? '';

		$this->render_component( $this->attrs );
	}

	/**
	 * A helper to get the table body rows and cells.
	 *
	 * @return array
	 */
	private function extract_body(): array {
		return $this->extract_table_section( 'tbody tr' );
	}

	/**
	 * A helper to get the table footer rows and cells.
	 *
	 * @return array
	 */
	private function extract_footer(): array {
		return $this->extract_table_section( 'tfoot tr' );
	}

	/**
	 * A helper to get the table header rows and cells.
	 *
	 * @return array
	 */
	private function extract_header(): array {
		return $this->extract_table_section( 'thead tr', 'th' );
	}

	/**
	 * Get the rows and cells of a section within the root element.
	 *
	 * @param string $section_selector A selector string.
	 * @param string $cell_selector A selector string.
	 * @return array
	 */
	private function extract_table_section( string $section_selector, string $cell_selector = 'td' ): array {
		$section = [];

		$rows = $this->root->find( $section_selector ) ?? [];

		foreach ( $rows as $row ) {
			$section[] = $this->extract_row( $row, $cell_selector );
		}

		return $section;
	}

	/**
	 * Get the cells within a row node.
	 *
	 * @param HtmlNode $row The row node.
	 * @param string   $cell_selector The cell selector string.
	 * @return array
	 */
	private function extract_row( HtmlNode $row, string $cell_selector = 'td' ): array {
		$inner = [];

		foreach ( $row->find( $cell_selector ) ?? [] as $cell ) {
			$inner[] = $cell->innerHtml();
		}

		return $inner;
	}
}
