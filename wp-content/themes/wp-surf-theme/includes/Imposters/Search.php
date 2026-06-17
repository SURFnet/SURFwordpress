<?php

namespace SURF\Imposters;

use SURF\Core\Imposters\BaseImposter;
use SURF\Traits\HasArchiveWidgetAreaFilters;

/**
 * Class Search
 * @package SURF\Imposters
 */
class Search extends BaseImposter
{

	use HasArchiveWidgetAreaFilters;

	/**
	 * @return void
	 */
	public function setupImposter(): void
	{
		static::registerWidgetAreas();
	}

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return __( 'Search', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getName(): string
	{
		return 'search';
	}

	/**
	 * @return string
	 */
	public static function getParent(): string
	{
		// since search is not a post type, it doesn't have a parent, so add it to settings.
		return 'options-general.php';
	}

}
