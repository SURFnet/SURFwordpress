<?php

namespace SURF\Core\Blocks;

use Illuminate\Support\Str;

/**
 * Class AcfBlock
 * @package SURF\Core\Blocks
 */
abstract class AcfBlock
{

	protected string  $namespace   = 'acf';
	protected ?string $name        = null;
	protected ?string $title       = null;
	protected ?string $description = null;
	protected ?string $category    = null;
	protected ?string $icon        = null;
	protected ?array  $keywords    = null;

	/**
	 * @return void
	 */
	public function register()
	{
		if ( function_exists( 'acf_register_block_type' ) && function_exists( 'acf_add_local_field_group' ) ) {
			acf_register_block_type( $this->getBlockType() );
			acf_add_local_field_group( $this->getFieldGroup() );
		}
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
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title ?? Str::of( $this->getName() )->replace( '-', ' ' )->title();
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description ?? '';
	}

	/**
	 * @return string
	 */
	public function getCategory(): string
	{
		return $this->category ?? 'surf';
	}

	/**
	 * @return string
	 */
	public function getIcon(): string
	{
		return $this->icon ?? 'block-default';
	}

	/**
	 * @return array
	 */
	public function getKeyWords(): array
	{
		return $this->keywords ?? [];
	}

	/**
	 * @return string
	 */
	public function getView(): string
	{
		return 'blocks.' . $this->getName();
	}

	/**
	 * @param array $block
	 * @return void
	 */
	public function render( array $block )
	{
		echo surfView( $this->getView(), $block['data'] ?? [] );
	}

	/**
	 * @return array
	 */
	public function getBlockType(): array
	{
		return [
			'name'            => $this->getName(),
			'title'           => $this->getTitle(),
			'description'     => $this->getDescription(),
			'render_callback' => [ $this, 'render' ],
			'category'        => $this->getCategory(),
			'icon'            => $this->getIcon(),
			'keywords'        => $this->getKeyWords(),
		];
	}

	/**
	 * @return array
	 */
	public function getFieldGroup(): array
	{
		return [
			'key'      => $this->getName() . '_field_group',
			'title'    => $this->getTitle() . ' Field Group',
			'fields'   => $this->parseFields(
				$this->getFields()
			),
			'location' => $this->getLocation(),
		];
	}

	/**
	 * @param array $fields
	 * @return array
	 */
	public function parseFields( array $fields ): array
	{
		return $fields;
	}

	/**
	 * @return array
	 */
	public function getFields(): array
	{
		return [];
	}

	/**
	 * @return array[]
	 */
	public function getLocation(): array
	{
		return [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => $this->getFullName(),
				],
			],
		];
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public function getFieldKey( string $name ): string
	{
		return $this->getFullName() . '_' . $name;
	}

}
