<?php

namespace SURF\Taxonomies;

use SURF\Core\Taxonomies\Registers;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\PostTypes\Vacancy;

/**
 * Class VacancyHours
 * @package SURF\Taxonomies
 */
class VacancyHours extends Taxonomy
{

	use Registers;

	protected static string $taxonomy  = 'surf-vacancy-hours';
	protected static array  $postTypes = [ Vacancy::class ];

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Hours', 'label singular - vacancy-hours', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'Hours', 'label plural - vacancy-hours', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'vacancy-hours', 'tax slug - vacancy-hours', 'wp-surf-theme' );
	}

}
