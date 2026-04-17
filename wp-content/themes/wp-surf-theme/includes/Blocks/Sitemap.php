<?php

namespace SURF\Blocks;

use Illuminate\Contracts\Container\BindingResolutionException;
use SURF\Core\Blocks\Block;
use SURF\Core\PostTypes\BasePost;
use SURF\Services\PostTypeSitemapService;
use WP_Block;

/**
 * Class Sitemap
 * @package SURF\Blocks
 */
class Sitemap extends Block
{

	protected static ?array $slug_map   = null;
	protected ?array        $attributes = [
		'title'       => [
			'type' => 'string',
		],
		'postType'    => [
			'type' => 'string',
		],
		'hideEmpty'   => [
			'type'    => 'boolean',
			'default' => true,
		],
		'primaryOnly' => [
			'type'    => 'boolean',
			'default' => false,
		],
	];

	/**
	 * @return void
	 * @throws BindingResolutionException
	 */
	public function register(): void
	{
		parent::register();

		add_filter( 'surf_editor_blocks_data', [ static::class, 'addBlockData' ] );
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
	 * Adds necessary data for the sitemap block to the editor blocks data
	 * @param array $blocks
	 * @return array
	 */
	public static function addBlockData( array $blocks ): array
	{
		$options = [];
		foreach ( static::listAllowedPostTypes() as $cpt_data ) {
			$options[] = [ 'value' => $cpt_data['value'], 'label' => $cpt_data['label'] ];
		}

		return array_merge( $blocks, [
			'sitemap' => [
				'postTypes' => $options,
			],
		] );
	}

	/**
	 * @param null|string $post_type
	 * @param bool $hide_empty
	 * @param bool $primary_only
	 * @return array
	 */
	public static function getTree( ?string $post_type = null, bool $hide_empty = true, bool $primary_only = false ): array
	{
		$cpt_class = static::convertSlugToClass( $post_type );
		if ( empty( $cpt_class ) ) {
			return [];
		}

		return PostTypeSitemapService::build( $cpt_class, $hide_empty, $primary_only );
	}

	/**
	 * Converts a CPT slug to its corresponding class name, if allowed
	 * @param null|string $cpt_slug
	 * @return null|string
	 */
	public static function convertSlugToClass( ?string $cpt_slug = null ): ?string
	{
		if ( empty( $cpt_slug ) ) {
			return null;
		}

		return static::getSlugMap()[ $cpt_slug ] ?? null;
	}

	/**
	 * Lists allowed CPTs with additional metadata for rendering (label, class reference)
	 * @return array
	 */
	public static function listAllowedPostTypes(): array
	{
		$cpt_list = [];
		foreach ( PostTypeSitemapService::listAllowedPostTypes() as $cpt_class ) {
			/** @var class-string<BasePost> $cpt_class */
			$cpt_list[] = [
				'label' => static::getClassLabel( $cpt_class ),
				'value' => $cpt_class::getName(),
				'class' => $cpt_class,
			];
		}

		return $cpt_list;
	}

	/**
	 * Gets (or generates) the option label for a CPT class
	 * @param class-string<BasePost> $cpt_class
	 * @return string
	 */
	public static function getClassLabel( string $cpt_class ): string
	{
		return method_exists( $cpt_class, 'getPluralLabel' )
			? $cpt_class::getPluralLabel()
			: preg_replace( '/([a-z])([A-Z])/', '$1 $2', class_basename( $cpt_class ) );
	}

	/**
	 * Builds a mapping of CPT slugs to their corresponding class names for quick lookup
	 * @return array
	 */
	public static function getSlugMap(): array
	{
		if ( static::$slug_map !== null ) {
			return static::$slug_map;
		}

		$map = [];
		foreach ( static::listAllowedPostTypes() as $cpt_data ) {
			$map[ $cpt_data['value'] ] = $cpt_data['class'];
		}
		static::$slug_map = $map;

		return $map;
	}

}
