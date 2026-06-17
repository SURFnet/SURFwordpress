<?php

namespace SURF\Taxonomies;

use SURF\Core\Taxonomies\Registers;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\Core\Traits\HasFields;
use SURF\PostTypes\Agenda;
use WP_Term;

/**
 * Class AgendaLocation
 * @package SURF\Taxonomies
 */
class AgendaLocation extends Taxonomy
{

	use Registers, HasFields;

	protected static string $taxonomy  = 'surf-agenda-location';
	protected static array  $postTypes = [ Agenda::class ];

	public const FIELD_LOCATION_STREET            = 'location_street';
	public const FIELD_LOCATION_CITY              = 'location_city';
	public const FIELD_LOCATION_ZIPCODE           = 'location_zipcode';
	public const FIELD_LOCATION_COUNTRY           = 'location_country';
	public const FIELD_LOCATION_URL               = 'location_url';
	public const FIELD_LOCATION_OPENSTREETMAP_URL = 'location_openstreetmap_url';
	public const FIELD_LOCATION_IMAGE             = 'location_image';

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Location', 'label singular - agenda-location', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'Location', 'label plural - agenda-location', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'location', 'tax slug - agenda-location', 'wp-surf-theme' );
	}

	/**
	 * @param $id
	 * @return array
	 */
	public static function getPrimaryLocationInfo( $id ): array
	{
		$term = surfGetPrimaryTerm( $id, static::getName() );
		if ( !( $term instanceof WP_Term ) ) {
			return [];
		}

		$data = static::listPrimaryLocationInfo( $term );
		if ( empty( $data ) ) {
			return [];
		}

		return array_merge( [ 'id' => $term->term_id ], $data );
	}

	/**
	 * @return array
	 */
	public static function getPrimaryLocationInfoArchive(): array
	{
		$term_id = get_queried_object()?->term_id ?? null;
		if ( empty( $term_id ) ) {
			return [];
		}

		$term = get_term( $term_id, static::getName() );
		if ( !( $term instanceof WP_Term ) ) {
			return [];
		}

		return static::listPrimaryLocationInfo( $term );
	}

	/**
	 * @param WP_Term $term
	 * @return array
	 */
	public static function listPrimaryLocationInfo( WP_Term $term ): array
	{
		$name = $term->name ?? '';
		$term = static::fromTerm( $term );

		return [
			'name'              => $name,
			'street'            => $term->getMeta( static::FIELD_LOCATION_STREET ),
			'zipcode'           => $term->getMeta( static::FIELD_LOCATION_ZIPCODE ),
			'city'              => $term->getMeta( static::FIELD_LOCATION_CITY ),
			'country'           => $term->getMeta( static::FIELD_LOCATION_COUNTRY ),
			'url'               => $term->getMeta( static::FIELD_LOCATION_URL ),
			'openstreetmap_url' => $term->getMeta( static::FIELD_LOCATION_OPENSTREETMAP_URL ),
			'image'             => $term->getMeta( static::FIELD_LOCATION_IMAGE ),
		];
	}

	/**
	 * @return array[]
	 */
	public static function getFields(): array
	{
		$identifier = str_replace( '-', '_', static::getName() );

		return [
			[
				'key'    => 'group_' . $identifier . '_settings',
				'title'  => _x( 'Location settings', 'admin', 'wp-surf-theme' ),
				'fields' => [
					[
						'key'   => 'field_location_street',
						'label' => _x( 'Street', 'admin', 'wp-surf-theme' ),
						'name'  => static::FIELD_LOCATION_STREET,
						'type'  => 'text',
					],
					[
						'key'   => 'field_location_city',
						'label' => _x( 'City', 'admin', 'wp-surf-theme' ),
						'name'  => static::FIELD_LOCATION_CITY,
						'type'  => 'text',
					],
					[
						'key'   => 'field_location_zipcode',
						'label' => _x( 'Zipcode', 'admin', 'wp-surf-theme' ),
						'name'  => static::FIELD_LOCATION_ZIPCODE,
						'type'  => 'text',
					],
					[
						'key'   => 'field_location_country',
						'label' => _x( 'Country', 'admin', 'wp-surf-theme' ),
						'name'  => static::FIELD_LOCATION_COUNTRY,
						'type'  => 'text',
					],
					[
						'key'   => 'field_location_location_url',
						'label' => _x( 'Location URL', 'admin', 'wp-surf-theme' ),
						'name'  => static::FIELD_LOCATION_URL,
						'type'  => 'url',
					],
					[
						'key'   => 'field_location_openstreetmap_url',
						'label' => _x( 'Openstreetmap URL', 'admin', 'wp-surf-theme' ),
						'name'  => static::FIELD_LOCATION_OPENSTREETMAP_URL,
						'type'  => 'url',
					],
					[
						'key'           => 'field_location_image',
						'label'         => _x( 'Image', 'admin', 'wp-surf-theme' ),
						'name'          => static::FIELD_LOCATION_IMAGE,
						'type'          => 'image',
						'return_format' => 'id',
					],
				],
			],
		];
	}

}
