<?php

namespace SURF\Core\Widgets;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
use SURF\Core\PostTypes\PostTypeRepository;
use WP_Term;
use WP_Widget;

/**
 * Class Widget
 * @package SURF\Core\Widgets
 */
abstract class Widget extends WP_Widget
{

	public function __construct()
	{
		add_action( 'acf/init', [ $this, 'registerFields' ] );
		add_filter( 'acf/pre_render_fields', [ $this, 'filterWidgetArea' ], 10, 2 );

		parent::__construct(
			$this->getId(),
			$this->title(),
			[
				'classname'   => Str::of( class_basename( $this ) )->snake()->title(),
				'description' => $this->description(),
			]
		);
	}

	/**
	 * @return string
	 */
	public function getId(): string
	{
		// surf-widget gets used in JS helper functions, so we need to keep it.
		return Str::of( class_basename( $this ) . '-surf-widget' )->kebab();
	}

	/**
	 * @return string
	 */
	abstract protected function description(): string;

	/**
	 * @return string
	 */
	abstract protected function title(): string;

	/**
	 * @return array
	 */
	abstract protected function fields(): array;

	/**
	 * @param $instance
	 * @return void
	 */
	public function form( $instance )
	{
		// do nothing
	}

	/**
	 * @param $args
	 * @param $instance
	 * @return void
	 */
	public function widget( $args, $instance )
	{
		$settings = $this->getCustomSettings( $args );
		echo surfView( 'wp-widgets.' . $this->id_base, [
			'args'     => $args,
			'instance' => $instance,
			'settings' => $settings,
		] );
	}

	/**
	 * @param array $args
	 * @return array
	 */
	public function getCustomSettings( array $args ): array
	{
		$fields   = $this->fields();
		$names    = array_column( $fields, 'name' );
		$settings = array_fill_keys( $names, '' );
		$widgetID = $args['widget_id'];
		foreach ( $fields as $field ) {
			$optionKey              = $field['name'];
			$settings[ $optionKey ] = $this->getCustomSettingByField( $field, $widgetID );
		}

		return $settings;
	}

	/**
	 * @param array $field
	 * @param string $widgetID
	 * @param string|null $parent
	 * @return mixed
	 */
	public function getCustomSettingByField( array $field, string $widgetID, ?string $parent = null ): mixed
	{
		$optionKey = ( !empty( $parent ) ? $parent . '_' : '' ) . $field['name'];
		switch ( $field['type'] ) {
			case 'group':
				$subFields = $field['sub_fields'] ?? [];
				$subNames  = array_column( $subFields, 'name' );
				$subValues = array_fill_keys( $subNames, '' );
				foreach ( $subFields as $subField ) {
					$subOptionKey                   = $optionKey . '_' . $subField['name'];
					$subValues[ $subField['name'] ] = $this->getCustomSettingByField( $subField, $widgetID, $optionKey );
				}

				return $subValues;

			case 'taxonomy':
				$terms = surfGetWidgetOption( $optionKey, $widgetID );
				if ( is_array( $terms ) ) {
					return array_map( function ( $term )
					{
						$term = get_term( $term );

						return $term instanceof WP_Term ? $term : null;
					}, $terms );
				}

				return [];

			default:
				return surfGetWidgetOption( $optionKey, $widgetID );
		}
	}

	/**
	 * @param $fields
	 * @param $post
	 * @return array
	 * @throws BindingResolutionException
	 */
	public function filterWidgetArea( $fields, $post = 0 ): array
	{
		// parse widget location
		preg_match( "/widget_([a-z-]*)-(\d)/", $post, $matches );

		$widget           = $matches[1] ?? false;
		$widgetInstanceId = $matches[2] ?? false;

		if ( $widget !== $this->getId() || !$widgetInstanceId ) {
			return $fields;
		}

		// get the current area.
		$areas = get_option( 'sidebars_widgets' );
		$area  = surfSearchArrayAndGetKey( "$widget-$widgetInstanceId", $areas );

		if ( !$area ) {
			return $fields;
		}

		// filter which post type is using this widget area
		$types = surfApp()->make( PostTypeRepository::class );
		$type  = array_reduce( $types->all(), function ( $carry, $type ) use ( $area )
		{
			if ( method_exists( $type, 'getWidgetAreaId' ) ) {
				return $type::getWidgetAreaId() === $area
					? $type
					: $carry;
			}

			return $carry;
		}, null );

		if ( !$type ) {
			return $fields;
		}

		$fields = $this->filterFields( $fields, $type );

		return $fields;
	}

	/**
	 * @param array $fields
	 * @param string $type
	 * @return array
	 */
	public function filterFields( array $fields, string $type ): array
	{
		foreach ( $fields as $key => $field ) {
			switch ( $field['type'] ):
				case 'taxonomy':
					// get the taxonomy, check if the post type is using it.
					$taxonomy   = get_taxonomy( $field['taxonomy'] );
					$objectType = $taxonomy->object_type;
					if ( !in_array( $type::getName(), $objectType ) ) {
						unset( $fields[ $key ] );
					}
					break;

				// if more field types get added which need to be filtered based on where the widget is used, add them here.
			endswitch;
		}

		return $fields;
	}

	/**
	 * @return void
	 */
	public function registerFields()
	{
		if ( !function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group( [
			'key'      => 'group_' . Str::of( class_basename( $this ) )->kebab(),
			'title'    => $this->title(),
			'fields'   => $this->fields(),
			'location' => [
				[
					[
						'param'    => 'widget',
						'operator' => '==',
						'value'    => $this->getId(),
					],
				],
			],
		] );
	}

}
