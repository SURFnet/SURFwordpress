<?php

namespace SURF\Widgets;

use SURF\Core\Widgets\Widget;

/**
 * Class TermFilter
 * @package SURF\Widgets
 */
class TermFilter extends Widget
{

	/**
	 * @return string
	 */
	protected function description(): string
	{
		return _x( 'Displays a list of terms to filter the current query', 'admin', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	protected function title(): string
	{
		return _x( 'Term Filter', 'admin', 'wp-surf-theme' );
	}

	/**
	 * @param $args
	 * @param $instance
	 * @return void
	 */
	public function widget( $args, $instance )
	{
		$taxonomyFields = [];
		foreach ( $this->getTaxonomyFields() as $field ) {
			$taxonomyName                    = str_replace( 'group_', '', $field['name'] );
			$taxonomyFields[ $taxonomyName ] = [
				'field_name' => $field['name'],
				'query_key'  => $this->getQueryKeyForTaxonomy( $taxonomyName ),
			];
		}

		$settings = $this->getCustomSettings( $args );
		echo surfView( 'wp-widgets.' . $this->id_base, [
			'args'           => $args,
			'instance'       => $instance,
			'settings'       => $settings,
			'taxonomyFields' => $taxonomyFields,
		] );
	}

	/**
	 * @return array
	 */
	protected function fields(): array
	{
		return [
			[
				'key'           => 'field_' . $this->getId() . '_title',
				'label'         => _x( 'Title', 'admin', 'wp-surf-theme' ),
				'name'          => 'title',
				'type'          => 'text',
				'default_value' => $this->title(),
			],
			...$this->getTaxonomyFields(),
		];
	}

	/**
	 * @return array
	 */
	public function getTaxonomyFields(): array
	{
		return array_map( function ( $taxonomy )
		{
			$types = array_map( function ( $type )
			{
				return get_post_type_object( $type )?->label;
			}, $taxonomy->object_type );

			return [
				'key'        => 'field_' . $this->getId() . '_group_' . $taxonomy->name,
				'label'      => sprintf( _x( '%s (%s)', 'admin', 'wp-surf-theme' ), $taxonomy->label, implode( ', ', $types ) ),
				'name'       => 'group_' . $taxonomy->name,
				'type'       => 'group',
				'sub_fields' => [
					[
						'key'           => 'field_' . $this->getId() . '_title_' . $taxonomy->name,
						'label'         => _x( 'Title', 'admin', 'wp-surf-theme' ),
						'name'          => 'title',
						'type'          => 'text',
						'default_value' => $taxonomy->label,
					],
					[
						'key'           => 'field_' . $this->getId() . '_terms_' . $taxonomy->name,
						'label'         => _x( 'Terms', 'admin', 'wp-surf-theme' ),
						'name'          => 'terms',
						'type'          => 'taxonomy',
						'taxonomy'      => $taxonomy->name,
						'field_type'    => 'multi_select',
						'return_format' => 'object',
					],
				],
			];
		}, array_values( get_taxonomies( [ 'public' => true ], 'objects' ) ) );
	}

	/**
	 * @param string $taxonomyName
	 * @return string
	 */
	private function getQueryKeyForTaxonomy( string $taxonomyName ): string
	{
		return 'tax_' . $taxonomyName;
	}

}
