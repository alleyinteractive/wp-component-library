<?php
/**
 * WP Component Library includes: Html_Node class.
 *
 * @package WP_Component_Library
 */

namespace WP_Component_Library\Blocks;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use InvalidArgumentException;

/**
 * A thin wrapper around a DOM element, used to read a block's saved markup.
 *
 * Supports the subset of CSS selectors the block classes need: tag names,
 * class names (`.foo`, `figure.foo`) and the descendant combinator (`tbody tr`).
 */
class Html_Node {
	/**
	 * Constructor.
	 *
	 * @param DOMElement $element The wrapped element.
	 */
	public function __construct( protected DOMElement $element ) {}

	/**
	 * Parse an HTML fragment and return a node wrapping its container.
	 *
	 * The container is never part of the fragment, so `first_child()` returns
	 * the fragment's outermost element and `find()` searches the whole fragment.
	 *
	 * @param string $html The HTML fragment.
	 * @return Html_Node
	 */
	public static function from_html( string $html ): Html_Node {
		$document = new DOMDocument();
		$previous = libxml_use_internal_errors( true );

		// The charset meta keeps libxml from reading the fragment as ISO-8859-1.
		$document->loadHTML(
			'<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>' . $html . '</body></html>',
			LIBXML_NONET
		);

		libxml_clear_errors();
		libxml_use_internal_errors( $previous );

		return new self( $document->getElementsByTagName( 'body' )->item( 0 ) );
	}

	/**
	 * The first child element, ignoring text and comment nodes.
	 *
	 * @return Html_Node|null
	 */
	public function first_child(): ?Html_Node {
		return $this->get_children()[0] ?? null;
	}

	/**
	 * The child elements, ignoring text and comment nodes.
	 *
	 * @return Html_Node[]
	 */
	public function get_children(): array {
		$children = [];

		foreach ( $this->element->childNodes as $child ) {
			if ( $child instanceof DOMElement ) {
				$children[] = new self( $child );
			}
		}

		return $children;
	}

	/**
	 * Find the descendants matching a selector, in document order.
	 *
	 * @param string $selector The selector, e.g. `a`, `.caption` or `tbody tr`.
	 * @return Html_Node[]
	 */
	public function find( string $selector ): array {
		$xpath = new DOMXPath( $this->element->ownerDocument );
		$found = [];

		foreach ( $xpath->query( self::selector_to_xpath( $selector ), $this->element ) as $element ) {
			$found[] = new self( $element );
		}

		return $found;
	}

	/**
	 * Find the first descendant matching a selector.
	 *
	 * @param string $selector The selector, e.g. `a`, `.caption` or `tbody tr`.
	 * @return Html_Node|null
	 */
	public function first( string $selector ): ?Html_Node {
		return $this->find( $selector )[0] ?? null;
	}

	/**
	 * The element's attributes, keyed by name, with entities decoded. A bare
	 * boolean attribute such as `controls` has the value `null`, while
	 * `controls=""` has `''`.
	 *
	 * @return array<string, string|null>
	 */
	public function get_attributes(): array {
		$attributes = [];

		foreach ( $this->element->attributes as $attribute ) {
			// libxml gives a value-less attribute no text child.
			$attributes[ $attribute->nodeName ] = null === $attribute->firstChild ? null : $attribute->nodeValue;
		}

		return $attributes;
	}

	/**
	 * A single attribute's value, or null when the element does not have it.
	 *
	 * @param string $name The attribute name.
	 * @return string|null
	 */
	public function get_attribute( string $name ): ?string {
		return $this->element->hasAttribute( $name ) ? $this->element->getAttribute( $name ) : null;
	}

	/**
	 * The lowercase tag name, e.g. `h2`.
	 *
	 * @return string
	 */
	public function tag_name(): string {
		return strtolower( $this->element->localName );
	}

	/**
	 * The element's contents as HTML, without its own tag.
	 *
	 * @return string
	 */
	public function inner_html(): string {
		$html = '';

		foreach ( $this->element->childNodes as $child ) {
			$html .= $this->save( $child );
		}

		return $html;
	}

	/**
	 * The element as HTML, including its own tag.
	 *
	 * @return string
	 */
	public function outer_html(): string {
		return $this->save( $this->element );
	}

	/**
	 * Serialize a node.
	 *
	 * @param DOMNode $node The node.
	 * @return string
	 */
	protected function save( DOMNode $node ): string {
		return (string) $this->element->ownerDocument->saveHTML( $node );
	}

	/**
	 * Translate a selector into a relative XPath expression.
	 *
	 * @throws InvalidArgumentException When the selector uses syntax this class does not support.
	 *
	 * @param string $selector The selector.
	 * @return string
	 */
	protected static function selector_to_xpath( string $selector ): string {
		$steps = [];

		foreach ( preg_split( '/\s+/', trim( $selector ) ) as $compound ) {
			if ( ! preg_match( '/^([a-z][a-z0-9-]*)?((?:\.[a-z0-9_-]+)*)$/i', $compound, $matches ) || '' === $compound ) {
				throw new InvalidArgumentException( sprintf( 'Unsupported selector: %s', $selector ) ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			}

			$step = '' !== $matches[1] ? strtolower( $matches[1] ) : '*';

			foreach ( array_filter( explode( '.', $matches[2] ) ) as $class ) {
				$step .= sprintf( '[contains(concat(" ", normalize-space(@class), " "), " %s ")]', $class );
			}

			$steps[] = $step;
		}

		return './/' . implode( '//', $steps );
	}
}
