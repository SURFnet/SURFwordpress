<?php

namespace SURF\Core\Blocks;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use WP_Block;

/**
 * Class Block
 * @package SURF\Core\Blocks
 */
abstract class Block
{

	protected ?string $name       = null;
	protected ?array  $attributes = null;
	protected string  $namespace  = 'surf';

	/**
	 * @return void
	 * @throws BindingResolutionException
	 */
	public function register(): void
	{
		register_block_type( $this->getFullName(), array_merge( [
			'editor_script'   => 'surf.gutenberg',
			'render_callback' => $this->isServerSide() ? [ $this, 'render' ] : null,
			'attributes'      => $this->getAttributes(),
		], $this->getBlockTypeArgs() ) );
	}

	/**
	 * @return array
	 */
	public function getBlockTypeArgs(): array
	{
		return [];
	}

	/**
	 * @param array $attributes
	 * @param string $content
	 * @param WP_Block $wpBlock
	 * @return string
	 */
	public function render( array $attributes, string $content, WP_Block $wpBlock ): string
	{
		return (string) surfView( $this->getView(), [
			'blockAttributes' => $attributes,
			'blockName'       => $this->getName(),
			'content'         => $content,
			'block'           => $this,
		] );
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name ?? Str::of( class_basename( $this ) )->kebab();
	}

	/**
	 * @return string
	 */
	public function getFullName(): string
	{
		return $this->namespace . '/' . $this->getName();
	}

	/**
	 * @return array|null
	 */
	public function getAttributes(): ?array
	{
		return $this->attributes;
	}

	/**
	 * @return bool
	 * @throws BindingResolutionException
	 */
	public function isServerSide(): bool
	{
		return surfViewExists( $this->getView() );
	}

	/**
	 * @return string
	 */
	public function getView(): string
	{
		return 'blocks.' . $this->getName();
	}

	/**
	 * @return bool
	 */
	public function inEditor(): bool
	{
		if ( !defined( 'REST_REQUEST' ) ) {
			return false;
		}

		/** @var Request $request */
		$request = surfApp( Request::class );

		return $request->get( 'context' ) === 'edit';
	}

}
