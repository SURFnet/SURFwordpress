<?php

namespace SURF\Traits;

use SURF\Providers\PublicationServiceProvider;

/**
 * Trait HasPublicationDate
 * @package SURF\Traits
 */
trait HasPublicationDate
{

	public const FIELD_DEPUBLICATION = PublicationServiceProvider::META_KEY;

	/**
	 * @return array[]
	 */
	public static function getDepublicationField( string $identifier = 'surf' ): array
	{
		return [
			'key'            => 'field_' . $identifier . '_' . static::FIELD_DEPUBLICATION,
			'label'          => _x( 'Depublication Date', 'admin', 'wp-surf-theme' ),
			'name'           => static::FIELD_DEPUBLICATION,
			'instructions'   => _x( 'The date and time at which the post will be put back to draft.', 'admin', 'wp-surf-theme' ),
			'type'           => 'date_time_picker',
			'display_format' => 'd F Y H:i',
		];
	}

	/**
	 * @return array[]
	 */
	public static function getPublicationGroup( string $identifier = 'surf' ): array
	{
		return [
			'key'      => 'group_' . $identifier . '_publication_settings',
			'title'    => _x( 'Publication Settings', 'admin', 'wp-surf-theme' ),
			'position' => 'side',
			'fields'   => [
				static::getDepublicationField( $identifier ),
			],
		];
	}

}
