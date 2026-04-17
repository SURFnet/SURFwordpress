<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\ArchiveController;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;
use SURF\PostTypes\Vacancy;
use SURF\Taxonomies\VacancyCategory;
use SURF\Taxonomies\VacancyHours;
use WP_Query;

/**
 * Class ArchiveSURFVacancyController
 * @package SURF
 */
class ArchiveSURFVacancyController extends TemplateController
{

	use ArchiveController;

	/**
	 * @param PostCollection $vacancies
	 * @return View
	 */
	public function handle( PostCollection $vacancies ): View
	{
		$page = Vacancy::getArchivePage();

		// Category
		$categoryName = VacancyCategory::getQueryKey();
		$categoryList = [];
		foreach ( VacancyCategory::query( [ 'hide_empty' => false ] ) as $term ) {
			$categoryList[ $term->term_id ] = $term->name;
		}

		// Hours
		$hoursName = VacancyHours::getQueryKey();
		$hoursList = [];
		foreach ( VacancyHours::query( [ 'hide_empty' => false ] ) as $term ) {
			$hoursList[ $term->term_id ] = $term->name;
		}

		$widgetAreaPosition = Vacancy::getArchiveWidgetAreaPosition();
		$widgetAreaId       = Vacancy::getWidgetAreaId();

		$columnSpanClass = static::getColumnSpanClass();
		$postItemType    = static::getPostItemType();

		return $this->view(
			'vacancy.archive',
			compact(
				'vacancies',
				'categoryName',
				'categoryList',
				'hoursName',
				'hoursList',
				'page',

				'widgetAreaPosition',
				'widgetAreaId',

				'columnSpanClass',
				'postItemType'
			)
		);
	}

	/**
	 * @param WP_Query $query
	 * @return WP_Query
	 */
	public static function afterQuery( WP_Query $query ): WP_Query
	{
		$excluded = Vacancy::query( [
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'meta_query'     => [
				[
					'key'   => Vacancy::FIELD_HIDE_FROM_ARCHIVE,
					'value' => true,
				],
			],
		] );
		if ( $excluded->count() === 0 ) {
			return $query;
		}

		$query->set( 'post__not_in', $excluded->map( fn( $post ) => $post->ID )->toArray() );

		return $query;
	}

	/**
	 * @return int
	 */
	public static function getPostsPerPage(): int
	{
		return 12;
	}

	/**
	 * @param PostCollection $vacancies
	 * @return string
	 */
	public static function renderPosts( PostCollection $vacancies ): string
	{
		$widgetAreaPosition = Vacancy::getArchiveWidgetAreaPosition();
		$postItemType       = static::getPostItemType();

		$html = '';
		foreach ( $vacancies as $vacancy ) {
			$html = '<div class="' . static::getColumnSpanClass() . '">';
			$html .= surfView( 'vacancy.item', [
				'vacancy'    => $vacancy,
				'type'       => $postItemType,
				'headingTag' => 'h2',
			] );
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * @return string
	 */
	public static function renderNothingFound(): string
	{
		$html = '';
		$html .= '<div class="column span-4-sm span-8-md span-12-lg">';
		$html .= surfView( 'vacancy.not-found' );
		$html .= '</div>';

		return $html;
	}

	/**
	 * @return array
	 */
	public static function getTaxonomyAliases(): array
	{
		return [
			VacancyCategory::getName() => VacancyCategory::getQueryKey(),
			VacancyHours::getName()    => VacancyHours::getQueryKey(),
		];
	}

	/**
	 * @return string
	 */
	public static function getColumnSpanClass(): string
	{
		$widgetAreaPosition = Vacancy::getArchiveWidgetAreaPosition();
		$columnCount        = in_array( $widgetAreaPosition, [
			'hidden',
			'top',
		] ) ? Vacancy::getColumnCount() : Vacancy::getColumnCountWithWidgetArea();

		switch ( $columnCount ) {
			case 1:
				return 'column span-4-sm span-8-md span-12-lg';

			case 2:
				return 'column span-4-sm span-4-md span-6-lg';

			case 3:
				return 'column span-4-sm span-4-md span-4-lg';

			default:
				return 'column span-4-sm span-8-md span-12-lg';
		}
	}

	/**
	 * @return string
	 */
	public static function getPostItemType(): string
	{
		$widgetAreaPosition = Vacancy::getArchiveWidgetAreaPosition();
		$columnCount        = in_array( $widgetAreaPosition, [
			'hidden',
			'top',
		] ) ? Vacancy::getColumnCount() : Vacancy::getColumnCountWithWidgetArea();

		switch ( $columnCount ) {
			case 1:
				return 'row';

			case 2:
				return in_array( $widgetAreaPosition, [ 'hidden', 'top' ] ) ? 'row' : 'block';

			case 3:
				return 'block';

			default:
				return 'block';
		}
	}

}
