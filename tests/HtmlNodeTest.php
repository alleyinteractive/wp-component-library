<?php
/**
 * WP Component Library Tests: HtmlNodeTest class
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */

use WP_Component_Library\Blocks\Core\Audio;
use WP_Component_Library\Blocks\Core\Button;
use WP_Component_Library\Blocks\Core\Gallery;
use WP_Component_Library\Blocks\Core\Heading;
use WP_Component_Library\Blocks\Core\Image;
use WP_Component_Library\Blocks\Core\ListBlock;
use WP_Component_Library\Blocks\Core\Paragraph;
use WP_Component_Library\Blocks\Core\Quote;
use WP_Component_Library\Blocks\Core\Separator;
use WP_Component_Library\Blocks\Core\Table;
use WP_Component_Library\Blocks\Html_Node;

require_once __DIR__ . '/trait-captures-component-attrs.php';

/**
 * Tests the HTML parser behind the core block classes, and what each block
 * class extracts from saved block markup.
 *
 * @package WP_Component_Library
 * @subpackage Tests
 */
class HtmlNodeTest extends WP_UnitTestCase {
	/**
	 * A raw block, as parse_blocks() would produce it.
	 *
	 * @param string $html  The block's innerHTML.
	 * @param array  $attrs The block's comment attributes.
	 * @return array
	 */
	protected function raw_block( string $html, array $attrs = [] ): array {
		return [
			'blockName'   => 'core/test',
			'attrs'       => $attrs,
			'innerHTML'   => $html,
			'innerBlocks' => [],
		];
	}

	public function test_first_child_skips_text_and_comments(): void {
		$node = Html_Node::from_html( "<!-- c -->\n<p class=\"a\">x</p>" );

		$this->assertSame( 'p', $node->first_child()?->tag_name() );
		$this->assertSame( [ 'class' => 'a' ], $node->first_child()->get_attributes() );
	}

	public function test_empty_html_has_no_children(): void {
		$this->assertNull( Html_Node::from_html( '' )->first_child() );
	}

	public function test_inner_html_preserves_utf8_and_markup(): void {
		$node = Html_Node::from_html( '<p>Bass &amp; walleye — “caught” café <strong>big</strong></p>' );

		$this->assertSame( 'Bass &amp; walleye — “caught” café <strong>big</strong>', $node->first_child()->inner_html() );
	}

	public function test_boolean_attributes_are_null(): void {
		$attributes = Html_Node::from_html( '<video controls muted="" src="v.mp4"></video>' )->first_child()->get_attributes();

		$this->assertSame( [
			'controls' => null,
			'muted'    => '',
			'src'      => 'v.mp4',
		], $attributes );
	}

	public function test_selectors(): void {
		$node = Html_Node::from_html( '<figure><table><thead><tr><th>A</th></tr></thead><tbody><tr><td>1</td></tr><tr><td>2</td></tr></tbody></table><figcaption class="x wp-element-caption">Cap</figcaption></figure>' );

		$this->assertCount( 2, $node->find( 'tbody tr' ) );
		$this->assertCount( 1, $node->find( 'thead tr' ) );
		$this->assertSame( [], $node->find( 'tfoot tr' ) );
		$this->assertSame( 'Cap', $node->first( '.wp-element-caption' )?->inner_html() );
		$this->assertSame( 'Cap', $node->first( 'figcaption.x' )?->inner_html() );
		$this->assertNull( $node->first( '.wp-element' ) );
	}

	public function test_unsupported_selector_throws(): void {
		$this->expectException( InvalidArgumentException::class );

		Html_Node::from_html( '<p></p>' )->find( 'p > a' );
	}

	public function test_paragraph(): void {
		$block = new class( $this->raw_block( '<p class="has-text-align-center">Hi <em>there</em></p>' ) ) extends Paragraph {
			use WPCL_Captures_Component_Attrs;
		};
		$block->render();
		$attrs = $block->captured;

		$this->assertSame( 'has-text-align-center', $attrs['class'] );
		$this->assertSame( 'Hi <em>there</em>', $attrs['content'] );
	}

	public function test_heading(): void {
		$block = new class( $this->raw_block( '<h3 class="wp-block-heading" id="a">Elite <em>Series</em></h3>' ) ) extends Heading {
			use WPCL_Captures_Component_Attrs;
		};
		$block->render();
		$attrs = $block->captured;

		$this->assertSame( 3, $attrs['level'] );
		$this->assertSame( 'Elite <em>Series</em>', $attrs['text'] );
		$this->assertSame( 'a', $attrs['id'] );
	}

	public function test_image(): void {
		$block = new class( $this->raw_block(
			'<figure class="wp-block-image size-large"><a href="https://example.com/?a=1&amp;b=2"><img src="https://example.com/a.jpg" alt="A bass" class="wp-image-12"/></a><figcaption class="wp-element-caption">Caption <em>here</em></figcaption></figure>',
			[
				'id'              => 12,
				'sizeSlug'        => 'large',
				'linkDestination' => 'custom',
			]
		) ) extends Image {
			use WPCL_Captures_Component_Attrs;
		};
		$block->render();
		$attrs = $block->captured;

		$this->assertSame( 'https://example.com/a.jpg', $attrs['src'] );
		$this->assertSame( 'A bass', $attrs['alt'] );
		$this->assertSame( 12, $attrs['image_id'] );
		$this->assertSame( 'large', $attrs['size'] );
		$this->assertSame( 'https://example.com/?a=1&b=2', $attrs['link'] );
		$this->assertSame( 'Caption <em>here</em>', $attrs['caption'] );
	}

	public function test_list(): void {
		$block = new class( $this->raw_block( '<ol class="wp-block-list" start="3"><li>One</li><li>Two <strong>b</strong></li></ol>' ) ) extends ListBlock {
			use WPCL_Captures_Component_Attrs;
		};
		$block->render();
		$attrs = $block->captured;

		$this->assertSame( 'ordered', $attrs['type'] );
		$this->assertSame( '3', $attrs['start'] );
		$this->assertSame( [ 'One', 'Two <strong>b</strong>' ], $attrs['items'] );
	}

	public function test_table(): void {
		$block = new class( $this->raw_block(
			'<figure class="wp-block-table"><table><thead><tr><th>Angler</th><th>Weight</th></tr></thead><tbody><tr><td>Bob</td><td>20-4</td></tr><tr><td>Al</td><td>18-1</td></tr></tbody><tfoot><tr><td>Total</td><td>38-5</td></tr></tfoot></table><figcaption class="wp-element-caption">Day 1</figcaption></figure>'
		) ) extends Table {
			use WPCL_Captures_Component_Attrs;
		};
		$block->render();
		$attrs = $block->captured;

		$this->assertSame( [ [ 'Angler', 'Weight' ] ], $attrs['header'] );
		$this->assertSame( [ [ 'Bob', '20-4' ], [ 'Al', '18-1' ] ], $attrs['body'] );
		$this->assertSame( [ [ 'Total', '38-5' ] ], $attrs['footer'] );
		$this->assertSame( 'Day 1', $attrs['citation'] );
	}

	public function test_gallery(): void {
		$block = new class( $this->raw_block(
			'<figure class="wp-block-gallery has-nested-images"><figcaption class="blocks-gallery-caption wp-element-caption">Gallery cap</figcaption></figure>',
			[ 'ids' => [ 1, 2 ] ]
		) ) extends Gallery {
			use WPCL_Captures_Component_Attrs;
		};
		$block->render();
		$attrs = $block->captured;

		$this->assertSame( [ 1, 2 ], $attrs['items'] );
		$this->assertSame( 'Gallery cap', $attrs['caption'] );
	}

	public function test_audio_boolean_attributes_are_true(): void {
		$block = new class( $this->raw_block( '<figure class="wp-block-audio"><audio controls loop src="https://example.com/a.mp3"></audio></figure>' ) ) extends Audio {
			use WPCL_Captures_Component_Attrs;
		};
		$block->render();
		$attrs = $block->captured;

		$this->assertTrue( $attrs['controls'] );
		$this->assertTrue( $attrs['loop'] );
		$this->assertSame( 'https://example.com/a.mp3', $attrs['src'] );
		$this->assertArrayNotHasKey( 'autoplay', $attrs );
	}

	public function test_button(): void {
		$block = new class( $this->raw_block( '<div class="wp-block-button"><a class="wp-block-button__link" href="/tickets" target="_blank">Buy <strong>now</strong></a></div>' ) ) extends Button {
			use WPCL_Captures_Component_Attrs;
		};
		$block->render();
		$attrs = $block->captured;

		$this->assertSame( '/tickets', $attrs['link'] );
		$this->assertSame( 'Buy <strong>now</strong>', $attrs['content'] );
		$this->assertSame( [
			'class'  => 'wp-block-button__link',
			'target' => '_blank',
		], $attrs['linkAttributes'] );
	}

	public function test_quote(): void {
		$block = new class( $this->raw_block( '<blockquote class="wp-block-quote"><p>Fish on!</p><cite>KVD</cite></blockquote>' ) ) extends Quote {
			use WPCL_Captures_Component_Attrs;
		};
		$block->render();
		$attrs = $block->captured;

		$this->assertSame( 'Fish on!', $attrs['content'] );
		$this->assertSame( 'KVD', $attrs['citation'] );
	}

	public function test_separator(): void {
		$block = new class( $this->raw_block( '<hr class="wp-block-separator has-alpha-channel-opacity"/>' ) ) extends Separator {
			use WPCL_Captures_Component_Attrs;
		};
		$block->render();
		$attrs = $block->captured;

		$this->assertSame( 'wp-block-separator has-alpha-channel-opacity', $attrs['class'] );
	}
}
