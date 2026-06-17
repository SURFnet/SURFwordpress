<?php

namespace SURF\PostTypes;

use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\HasFactory;
use SURF\Core\PostTypes\Registers;
use SURF\Core\Taxonomies\TermCollection;
use SURF\Core\Traits\HasFields;
use SURF\Enums\Theme;
use SURF\Helpers\ColorHelper;
use SURF\Taxonomies\AgendaCategory;
use SURF\Taxonomies\AgendaLocation;
use SURF\Traits\HasArchiveWidgetAreaFilters;
use SURF\Traits\HasPublicationDate;
use SURF\Traits\HasManagedTaxonomies;
use WP_Query;
use WP_Term;

/**
 * Class Agenda
 * @package SURF\PostTypes
 */
class Agenda extends BasePost
{

	use Registers, HasFactory, HasFields, HasArchiveWidgetAreaFilters, HasManagedTaxonomies, HasPublicationDate;

	protected static string $postType = 'surf-agenda';

	public const FIELD_START_DATE     = 'start_date';
	public const FIELD_END_DATE       = 'end_date';
	public const FIELD_SHOW_LOCATION  = 'show_location';
	public const SETTING_SHOW_RELATED = 'agenda_single_show_related_items';
	public const SETTING_HIDE_EXPIRED = 'agenda_archive_hide_expired';

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'agenda', 'CPT slug', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Event', 'CPT label singular', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'Events', 'CPT label plural', 'wp-surf-theme' );
	}

	/**
	 * @return array
	 */
	public static function getArgs(): array
	{
		return [
			'menu_icon' => 'dashicons-calendar-alt',
			'supports'  => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ],
		];
	}

	/**
	 * @return int|null
	 */
	public function shouldShowLocation(): ?int
	{
		return (int) $this->getMeta( static::FIELD_SHOW_LOCATION );
	}

	/**
	 * @param string $format
	 * @return string
	 */
	public function startDate( string $format = 'Y-m-d' ): string
	{
		return date( $format, strtotime( $this->getMeta( static::FIELD_START_DATE ) ) );
	}

	/**
	 * @param string $format
	 * @return string
	 */
	public function endDate( string $format = 'Y-m-d' ): string
	{
		return date( $format, strtotime( $this->getMeta( static::FIELD_START_DATE ) ) );
	}

	/**
	 * @param string|null $format
	 * @return string
	 */
	public function date( string $format = null ): string
	{
		$startDate = strtotime( $this->getMeta( static::FIELD_START_DATE ) );
		$endDate   = strtotime( $this->getMeta( static::FIELD_END_DATE ) );
		if ( !$startDate && !$endDate ) {
			return '';
		}

		if ( $format !== null ) {
			return date( $format, $startDate ) . ' - ' . date( $format, $endDate );
		}

		$time       = 'H:i';
		$day        = 'j';
		$month      = 'F';
		$shortMonth = 'M';
		$year       = 'Y';

		$showYear    = Theme::showEventYear();
		$currentYear = date( $year );
		$startYear   = $showYear === false ? $currentYear : date( $year, $startDate );
		$endYear     = $showYear === false ? $currentYear : date( $year, $endDate );

		// if no start date is set
		if ( !$startDate ) {
			$parts = [ 'd' => $day, 'm' => $month, 'y' => $year, 't' => $time ];
			if ( $endYear === $currentYear && $showYear !== true ) {
				unset( $parts['y'] );
			}

			return date( implode( ' ', $parts ), $endDate );
		}

		// if no end date is set
		if ( !$endDate ) {
			$parts = [ 'd' => $day, 'm' => $month, 'y' => $year, 't' => $time ];
			if ( $endYear === $currentYear && $showYear !== true ) {
				unset( $parts['y'] );
			}

			return date( implode( ' ', $parts ), $startDate );
		}

		// if start and end date are the same day (and month & year match too)
		if ( date( 'Ymd', $startDate ) === date( 'Ymd', $endDate ) ) {
			$parts = [
				'start' => [ 'd' => $day, 'm' => $shortMonth, 'y' => $year, 't' => $time ],
				'end'   => [ 't' => $time ],
			];
			if ( $startYear === $currentYear && $showYear !== true ) {
				unset( $parts['start']['y'] );
			}

			return date( implode( ' ', $parts['start'] ), $startDate ) . ' - ' . date(
					implode( ' ', $parts['end'] ),
					$endDate
				);
		}

		// if start and end date are the same month (and year!)
		if ( date( 'Ym', $startDate ) === date( 'Ym', $endDate ) ) {
			$parts = [
				'start' => [ 'd' => $day ],
				'end'   => [ 'd' => $day, 'm' => $month, 'y' => $year ],
			];
			if ( $endYear === $currentYear && $showYear !== true ) {
				unset( $parts['end']['y'] );
			}

			return date( implode( ' ', $parts['start'] ), $startDate ) . ' - ' . date(
					implode( ' ', $parts['end'] ),
					$endDate
				);
		}

		// if start and end date are the same year
		if ( date( 'Y', $startDate ) === date( 'Y', $endDate ) ) {
			$parts = [
				'start' => [ 'd' => $day, 'm' => $shortMonth ],
				'end'   => [ 'd' => $day, 'm' => $shortMonth, 'y' => $year ],
			];
			if ( $startYear === $currentYear && $showYear !== true ) {
				unset( $parts['end']['y'] );
			}

			return date( implode( ' ', $parts['start'] ), $startDate ) . ' - ' . date(
					implode( ' ', $parts['end'] ),
					$endDate
				);
		}

		// if start and end date are different days (but keep showing the year if they are different)
		$parts = [
			'start' => [ 'd' => $day, 'm' => $shortMonth, 'y' => $year ],
			'end'   => [ 'd' => $day, 'm' => $shortMonth, 'y' => $year ],
		];
		if ( $startYear === $currentYear && $showYear !== true && $startYear === $endYear ) {
			unset( $parts['start']['y'] );
		}
		if ( $endYear === $currentYear && $showYear !== true && $startYear === $endYear ) {
			unset( $parts['end']['y'] );
		}

		return date( implode( ' ', $parts['start'] ), $startDate ) . ' - ' . date( implode( ' ', $parts['end'] ), $endDate );
	}

	/**
	 * @return string
	 */
	public function location(): string
	{
		$locations = $this->locations();
		$ids       = $locations->pluck( 'term_id' )->toArray();
		$sorted    = [ ...$ids ];

		foreach ( $ids as $index => $id ) {
			$location = $locations->firstWhere( 'term_id', $id );

			if ( $parentIndex = array_search( $location->parent, $sorted ) ) {
				$sorted = surfMoveElement( $sorted, $index, $parentIndex + 1 );
			}
		}

		return collect( $sorted )->reverse()
		                         ->map( fn( $id ) => $locations->firstWhere( 'term_id', $id ) )
		                         ->map( fn( $l ) => $l->name )
		                         ->join( ', ' );
	}

	/**
	 * @param array $args
	 * @return TermCollection
	 */
	public function categories( array $args = [] ): TermCollection
	{
		return $this->getTerms( AgendaCategory::getName(), $args );
	}

	/**
	 * @param array $args
	 * @return TermCollection
	 */
	public function locations( array $args = [] ): TermCollection
	{
		return $this->getTerms( AgendaLocation::getName(), $args );
	}

	/**
	 * @param $id
	 * @return string|null
	 */
	public function getPrimaryCategoryName( $id ): ?string
	{
		$term = surfGetPrimaryTerm( $id, AgendaCategory::getName() );

		return !empty( $term ) ? ( $term->name ?? null ) : null;
	}

	/**
	 * @param $id
	 * @return int|null
	 */
	public function getPrimaryCategoryId( $id ): ?int
	{
		$term = surfGetPrimaryTerm( $id, AgendaCategory::getName() );

		return $term instanceof WP_Term ? ( $term->term_id ?? null ) : null;
	}

	/**
	 * @param $id
	 * @return mixed|string|null
	 */
	public function getPrimaryCategoryColor( $id )
	{
		$default = ColorHelper::getHexByName();
		$term    = surfGetPrimaryTerm( $id, AgendaCategory::getName() );
		if ( !($term instanceof WP_Term) ) {
			return $default;
		}

		$key   = 'category_color';
		$color = get_term_meta( $term->term_id, $key, true ) ?? $default;

		// Add fallback from yellow to orange because yellow is giving bad contrast with white text and backgrounds
		if ( strtoupper( $color ) === Theme::getColorHexBySlug( ColorHelper::COLOR_YELLOW ) ) {
			return Theme::getColorHexBySlug( ColorHelper::COLOR_ORANGE );
		}

		return get_term_meta( $term->term_id, $key, true ) ?? $default;
	}

	/**
	 * @return bool
	 */
	public function showRelatedItems(): bool
	{
		return (bool) Theme::getGlobalOption( static::SETTING_SHOW_RELATED );
	}

	/**
	 * @return bool
	 */
	public static function hideExpired(): bool
	{
		return (bool) Theme::getGlobalOption( static::SETTING_HIDE_EXPIRED );
	}

	/**
	 * @return array
	 */
	public static function getFields(): array
	{
		return [
			[
				'key'    => 'group_agenda_settings',
				'title'  => _x( 'Settings', 'admin', 'wp-surf-theme' ),
				'fields' => [
					[
						'key'            => 'field_agenda_settings_start_date',
						'label'          => _x( 'Start date', 'admin', 'wp-surf-theme' ),
						'name'           => static::FIELD_START_DATE,
						'type'           => 'date_time_picker',
						'display_format' => 'Y-m-d H:i:s',
						'wrapper'        => [ 'width' => 50 ],
					],
					[
						'key'            => 'field_agenda_settings_end_date',
						'label'          => _x( 'End date', 'admin', 'wp-surf-theme' ),
						'name'           => static::FIELD_END_DATE,
						'type'           => 'date_time_picker',
						'display_format' => 'Y-m-d H:i:s',
						'wrapper'        => [ 'width' => 50 ],
					],
				],
			],
			static::getImageGroup( static::getName() ),
			[
				'key'      => 'group_agenda_location_settings',
				'title'    => _x( 'Location Settings', 'admin', 'wp-surf-theme' ),
				'position' => 'side',
				'fields'   => [
					[
						'key'          => 'field_show_location',
						'label'        => _x( 'Show location', 'admin', 'wp-surf-theme' ),
						'instructions' => _x( 'Show location on the single page', 'admin', 'wp-surf-theme' ),
						'name'         => static::FIELD_SHOW_LOCATION,
						'type'         => 'true_false',
						'ui'           => true,
					],
				],
			],
			static::getPublicationGroup( static::getName() ),
		];
	}

	/**
	 * @return int[]
	 */
	public static function orderedByStartDate( bool $hideExpired = false ): array
	{
		$meta = [
			[
				'key'     => static::FIELD_START_DATE,
				'compare' => 'EXISTS',
			],
			[
				'key'     => static::FIELD_START_DATE,
				'value'   => '',
				'compare' => '!=',
			],
		];

		if ( $hideExpired ) {
			$meta[] = [
				'key'     => static::FIELD_END_DATE,
				'value'   => date( 'Y-m-d H:i:s' ),
				'compare' => '>=',
				'type'    => 'DATE',
			];
		}

		$withStartDate = ( new WP_Query( [
			'post_type'       => static::getName(),
			'fields'          => 'ids',
			'surf_skip_hooks' => true,
			'posts_per_page'  => -1,
			'no_found_rows'   => true,
			'meta_key'        => static::FIELD_START_DATE,
			'meta_type'       => 'DATETIME',
			'orderby'         => 'meta_value',
			'order'           => 'ASC',
			'meta_query'      => [ $meta ],
		] ) )->get_posts();

		$withoutStartDate = ( new WP_Query( [
			'post_type'       => static::getName(),
			'fields'          => 'ids',
			'surf_skip_hooks' => true,
			'posts_per_page'  => -1,
			'no_found_rows'   => true,
			'orderby'         => 'title',
			'order'           => 'ASC',
			'meta_query'      => [
				[
					'relation' => 'OR',
					[
						'key'     => static::FIELD_START_DATE,
						'compare' => 'NOT EXISTS',
					],
					[
						'key'     => static::FIELD_START_DATE,
						'value'   => '',
						'compare' => '==',
					],
				],
			],
		] ) )->get_posts();

		return array_merge( $withStartDate, $withoutStartDate );
	}

}
