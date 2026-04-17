<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\ArchiveController;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;
use SURF\PostTypes\Agenda;
use SURF\Taxonomies\AgendaCategory;
use SURF\Taxonomies\AgendaLocation;

/**
 * Class ArchiveSURFAgendaController
 * @package SURF
 */
class ArchiveSURFAgendaController extends TemplateController
{

	use ArchiveController;

	/**
	 * @param PostCollection $events
	 * @return View
	 */
	public function handle( PostCollection $events ): View
	{
		$page = Agenda::getArchivePage();

		// Category
		$categoryName = AgendaCategory::getQueryKey();
		$categoryList = AgendaCategory::querySortedByPriority( [
			'hide_empty' => false,
		] )->reduce( function ( array $carry, AgendaCategory $cat )
		{
			$carry[ $cat->term_id ] = $cat->name;

			return $carry;
		}, [] );

		// Location
		$locationName = AgendaLocation::getQueryKey();
		$locationList = [];
		foreach ( AgendaLocation::query( [ 'hide_empty' => false ] ) as $term ) {
			$locationList[ $term->term_id ] = $term->name;
		}

		// Past events filters
		$pastEventsFilters = [];
		$hideExpiredItems  = Agenda::hideExpired();
		if ( $hideExpiredItems ) {
			$pastEventsFilters['show'] = __( 'Events in the past', 'wp-surf-theme' );
		}

		$widgetAreaPosition = Agenda::getArchiveWidgetAreaPosition();
		$widgetAreaId       = Agenda::getWidgetAreaId();

		$columnSpanClass = static::getColumnSpanClass();
		$postItemType    = static::getPostItemType();

		return $this->view(
			'agenda.archive',
			compact(
				'events',
				'categoryName',
				'categoryList',
				'locationName',
				'locationList',
				'pastEventsFilters',
				'page',

				'widgetAreaPosition',
				'widgetAreaId',

				'columnSpanClass',
				'postItemType',
			)
		);
	}

	/**
	 * @param PostCollection $events
	 * @return string
	 */
	public static function renderPosts( PostCollection $events ): string
	{
		$widgetAreaPosition = Agenda::getArchiveWidgetAreaPosition();
		$postItemType       = static::getPostItemType();

		$html = '';
		foreach ( $events as $event ) {
			$html .= '<div class="' . static::getColumnSpanClass() . '">';
			$html .= surfView( 'agenda.item', [ 'event' => $event, 'type' => $postItemType, 'headingTag' => 'h2' ] );
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
		$html .= surfView( 'agenda.not-found' );
		$html .= '</div>';

		return $html;
	}

	/**
	 * @return array
	 */
	public static function getTaxonomyAliases(): array
	{
		return [
			AgendaCategory::getName() => AgendaCategory::getQueryKey(),
			AgendaLocation::getName() => AgendaLocation::getQueryKey(),
		];
	}

	/**
	 * @return string
	 */
	public static function getColumnSpanClass(): string
	{
		$widgetAreaPosition = Agenda::getArchiveWidgetAreaPosition();
		$columnCount        = in_array( $widgetAreaPosition, [
			'hidden',
			'top',
		] ) ? Agenda::getColumnCount() : Agenda::getColumnCountWithWidgetArea();

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
		$widgetAreaPosition = Agenda::getArchiveWidgetAreaPosition();

		$columnCount = in_array( $widgetAreaPosition, [
			'hidden',
			'top',
		] ) ? Agenda::getColumnCount() : Agenda::getColumnCountWithWidgetArea();

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
