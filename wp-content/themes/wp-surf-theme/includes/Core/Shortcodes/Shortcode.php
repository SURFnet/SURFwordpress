<?php

namespace SURF\Core\Shortcodes;

/**
 * Class Shortcode
 * Base class for creating WordPress shortcodes
 * @package SURF\Core\Shortcodes
 */
abstract class Shortcode
{

	protected ?string $name = null;

	/**
	 * @return void
	 */
	public function register(): void
	{
		add_shortcode( $this->getName(), [ $this, 'render' ] );
	}

	/**
	 * @param $attributes
	 * @param string $content
	 * @param string $tag
	 * @return string
	 */
	public function render( $attributes, string $content, string $tag ): string
	{
		return (string) surfView( $this->getView(), [
			'shortcodeName' => $tag,
			'content'       => $content,
			'shortcode'     => $this,
		] );
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name ?? strtolower( class_basename( $this ) );
	}

	/**
	 * @return string
	 */
	public function getView(): string
	{
		return "shortcodes.{$this->getName()}";
	}

}
