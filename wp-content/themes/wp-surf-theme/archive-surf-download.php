<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\ArchiveController;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;
use SURF\PostTypes\Download;
use SURF\Taxonomies\DownloadCategory;

/**
 * Class ArchiveSURFDownloadController
 * @package SURF
 */
class ArchiveSURFDownloadController extends TemplateController
{

	use ArchiveController;

	/**
	 * @param PostCollection $downloads
	 * @return View
	 */
	public function handle( PostCollection $downloads ): View
	{
		$page = Download::getArchivePage();

		$categoryName = DownloadCategory::getName();
		$categoryList = DownloadCategory::querySortedByPriority( [
			'hide_empty' => false,
		] )->reduce( function ( array $carry, DownloadCategory $cat )
		{
			$carry[ $cat->term_id ] = $cat->name;

			return $carry;
		}, [] );

		$widgetAreaPosition = Download::getArchiveWidgetAreaPosition();
		$widgetAreaId       = Download::getWidgetAreaId();

		$columnSpanClass = static::getColumnSpanClass();
		$postItemType    = static::getPostItemType();

		return $this->view(
			'download.archive',
			compact(
				'downloads',
				'categoryName',
				'categoryList',
				'page',

				'widgetAreaPosition',
				'widgetAreaId',

				'columnSpanClass',
				'postItemType'
			)
		);
	}

	/**
	 * @param PostCollection $downloads
	 * @return string
	 */
	public static function renderPosts( PostCollection $downloads ): string
	{
		$widgetAreaPosition = Download::getArchiveWidgetAreaPosition();
		$postItemType       = static::getPostItemType();

		$html = '';
		foreach ( $downloads as $download ) {
			$html .= '<div class="' . static::getColumnSpanClass() . '">';
			$html .= surfView( 'download.item', [
				'download'   => $download,
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
		$html .= surfView( 'download.not-found' );
		$html .= '</div>';

		return $html;
	}

	/**
	 * @return string
	 */
	public static function getColumnSpanClass(): string
	{
		$widgetAreaPosition = Download::getArchiveWidgetAreaPosition();
		$columnCount        = in_array( $widgetAreaPosition, [
			'hidden',
			'top',
		] ) ? Download::getColumnCount() : Download::getColumnCountWithWidgetArea();

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
		$widgetAreaPosition = Download::getArchiveWidgetAreaPosition();
		$columnCount        = in_array( $widgetAreaPosition, [
			'hidden',
			'top',
		] ) ? Download::getColumnCount() : Download::getColumnCountWithWidgetArea();

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
