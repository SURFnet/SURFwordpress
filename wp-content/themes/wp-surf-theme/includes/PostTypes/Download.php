<?php

namespace SURF\PostTypes;

use SURF\Core\Exceptions\MismatchingPostTypesException;
use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\Registers;
use SURF\Core\Taxonomies\TermCollection;
use SURF\Core\Traits\HasFields;
use SURF\Enums\PdfIndexStatus;
use SURF\Helpers\ColorHelper;
use SURF\Taxonomies\DownloadCategory;
use SURF\Taxonomies\DownloadTag;
use SURF\Taxonomies\Tag;
use SURF\Traits\HasArchiveWidgetAreaFilters;
use SURF\Traits\HasIndexedPdf;
use SURF\Traits\HasPublicationDate;
use SURF\Traits\HasManagedTaxonomies;
use WP_Term;

/**
 * Class Download
 * @package SURF\PostTypes
 */
class Download extends BasePost
{

	use Registers, HasFields, HasArchiveWidgetAreaFilters, HasManagedTaxonomies, HasPublicationDate, HasIndexedPdf;

	protected static string $postType = 'surf-download';

	public const FIELD_DATE = 'date';

	/**
	 * @return array
	 */
	public static function getArgs(): array
	{
		return [
			'menu_icon'  => 'dashicons-download',
			'supports'   => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ],
			'taxonomies' => [ Tag::getName() ],
		];
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'downloads', 'CPT slug', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Download', 'CPT label singular', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'Downloads', 'CPT label plural', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getCategoryTaxonomy(): string
	{
		return DownloadCategory::getName();
	}

	/**
	 * @return string
	 */
	public static function getTagTaxonomy(): string
	{
		return DownloadTag::getName();
	}

	/**
	 * @param string $format
	 * @return string
	 */
	public function date( string $format = 'Y-m-d' ): string
	{
		return wp_date( $format, strtotime( $this->getMeta( 'date' ) ) );
	}

	/**
	 * @return Attachment|null
	 * @throws MismatchingPostTypesException
	 */
	public function file(): ?Attachment
	{
		return $this->getFile();
	}

	/**
	 * @param array $args
	 * @return TermCollection
	 */
	public function categories( array $args = [] ): TermCollection
	{
		return $this->getCategories( $args );
	}

	/**
	 * @param bool $fallback
	 * @return string
	 */
	public function getCategoryColor( bool $fallback = false ): string
	{
		$all_terms = $this->getTerms( static::getCategoryTaxonomy() );
		if ( empty( $all_terms ) ) {
			return '';
		}

		$term = reset( $all_terms );
		if ( !( $term instanceof WP_Term ) ) {
			return '';
		}

		$default = ColorHelper::getHexByName();

		return DownloadCategory::getTermColor( $term->term_id, $default );
	}

	/**
	 * @param $id
	 * @return string|null
	 */
	public function getPrimaryCategoryName( $id ): ?string
	{
		$term = surfGetPrimaryTerm( $id, static::getCategoryTaxonomy() );

		return $term instanceof WP_Term ? ( $term->name ?? null ) : null;
	}

	/**
	 * @param $id
	 * @return int|null
	 */
	public function getPrimaryCategoryId( $id ): ?int
	{
		$term = surfGetPrimaryTerm( $id, static::getCategoryTaxonomy() );

		return $term instanceof WP_Term ? ( $term->term_id ?? null ) : null;
	}

	/**
	 * @param $id
	 * @return string
	 */
	public function getPrimaryCategoryColor( $id ): string
	{
		$default = ColorHelper::getHexByName();
		$term    = surfGetPrimaryTerm( $id, static::getCategoryTaxonomy() );
		if ( !( $term instanceof WP_Term ) ) {
			return '';
		}

		return DownloadCategory::getTermColor( $term->term_id, $default );
	}

	/**
	 * @return array
	 */
	public static function getFields(): array
	{
		return [
			[
				'key'    => 'group_download_settings',
				'title'  => _x( 'Settings', 'admin', 'wp-surf-theme' ),
				'fields' => [
					array_merge( static::getFileIDField( 'download' ), [
						'instructions' => _x( 'PDFs will be indexed for inclusion in search results', 'admin', 'wp-surf-theme' ),
						'required'     => true,
					] ),
					static::getFileIndexStatusField( 'download' ),
					[
						'key'   => 'field_download_' . static::FIELD_DATE,
						'label' => _x( 'Date', 'admin', 'wp-surf-theme' ),
						'name'  => static::FIELD_DATE,
						'type'  => 'date_picker',
					],
				],
			],
			static::getImageGroup( static::getName() ),
			static::getPublicationGroup( static::getName() ),
		];
	}

	/**
	 * @return void
	 */
	public static function registered(): void
	{
		static::registerPdfIndexHooks( 'download' );
	}

}
