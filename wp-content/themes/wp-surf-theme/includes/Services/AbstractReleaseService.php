<?php

namespace SURF\Services;

use SURF\Helpers\Helper;

/**
 * Class AbstractReleaseService
 * @package SURF\Services
 */
abstract class AbstractReleaseService
{

	public const PHP_VERSION = '8.2';
	public const WP_VERSION  = '';

	/**
	 * @param string $zip_name
	 * @param bool $allow_beta
	 * @return null|array
	 */
	abstract public function getReleaseForUpdate( string $zip_name, bool $allow_beta = false ): ?array;

	/**
	 * @param string|null $value
	 * @return string|null
	 */
	public function base64Encode( ?string $value ): ?string
	{
		if ( is_null( $value ) ) {
			return null;
		}

		return str_replace( '=', '', strtr( base64_encode( $value ), '+/', '-_' ) );
	}

	/**
	 * @param $value
	 * @return string
	 */
	public function jsonEncode( $value ): string
	{
		return json_encode( $value, JSON_UNESCAPED_SLASHES );
	}

	/**
	 * @param string $markdown
	 * @return string
	 */
	public function formatMarkDown( string $markdown ): string
	{
		return Helper::formatMarkDown( $markdown );
	}

}
