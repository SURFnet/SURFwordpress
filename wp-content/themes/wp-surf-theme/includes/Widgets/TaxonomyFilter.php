<?php

namespace SURF\Widgets;

use SURF\Core\Widgets\Widget;
use SURF\Helpers\PolylangHelper;

/**
 * Class TaxonomyFilter
 * @package SURF\Widgets
 */
class TaxonomyFilter extends Widget
{

	/**
	 * @return string
	 */
	protected function description(): string
	{
		return _x( 'Displays a list of taxonomies to filter the current query', 'admin', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	protected function title(): string
	{
		return _x( 'Taxonomy Filter', 'admin', 'wp-surf-theme' );
	}

	/**
	 * @return array[]
	 */
	protected function fields(): array
	{
		$choices = get_taxonomies( [ 'public' => true ], 'objects' );
		$choices = array_map( function ( $taxonomy )
		{
			$types = array_map( function ( $type )
			{
				return get_post_type_object( $type )?->label;
			}, $taxonomy->object_type );

			$label = PolylangHelper::getThemeOption( "search_taxonomy_{$taxonomy->name}" ) ?: $taxonomy->label;

			return $label . ' (' . implode( ', ', $types ) . ')';
		}, $choices );

		asort( $choices );

		return [
			[
				'key'           => 'field_' . $this->getId() . '_title',
				'label'         => _x( 'Title', 'admin', 'wp-surf-theme' ),
				'name'          => 'title',
				'type'          => 'text',
				'default_value' => $this->title(),
			],
			[
				'key'     => 'field_' . $this->getId() . '_taxonomies',
				'label'   => _x( 'Taxonomies', 'admin', 'wp-surf-theme' ),
				'name'    => 'taxonomies',
				'type'    => 'checkbox',
				'choices' => $choices,
			],
		];
	}

	/**
	 * @param array $fields
	 * @param string $type
	 * @return array
	 */
	public function filterFields( array $fields, string $type ): array
	{
		$fields = parent::filterFields( $fields, $type );
		foreach ( $fields as $key => $field ) {
			if ( $field['type'] === 'checkbox' && $field['name'] === 'taxonomies' ) {
				$choices = array_filter( $field['choices'], function ( $label, $slug ) use ( $type )
				{
					$taxonomy = get_taxonomy( $slug );

					return in_array( $type::getName(), $taxonomy->object_type );
				}, ARRAY_FILTER_USE_BOTH );

				$fields[ $key ]['choices'] = $choices;
			}
		}

		return $fields;
	}

}
