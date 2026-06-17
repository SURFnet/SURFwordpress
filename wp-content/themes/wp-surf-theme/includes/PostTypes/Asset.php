<?php

namespace SURF\PostTypes;

use Exception;
use SURF\Core\Exceptions\MismatchingPostTypesException;
use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\HasFactory;
use SURF\Core\PostTypes\Registers;
use SURF\Core\Taxonomies\TermCollection;
use SURF\Core\Traits\HasFields;
use SURF\Core\Traits\HasLocalizedSettingsPage;
use SURF\Core\Traits\HasSettingsPage;
use SURF\Enums\AssetStatus;
use SURF\Enums\Theme;
use SURF\Helpers\ACFHelper;
use SURF\Taxonomies\AssetCategory;
use SURF\Taxonomies\Tag;
use SURF\Traits\HasIndexedPdf;
use SURF\Traits\HasManagedTaxonomies;
use WP_Post;
use WP_Query;
use WP_Term;
use WP_User;

/**
 * Class Asset
 * @package SURF\PostTypes
 */
class Asset extends BasePost
{

	use Registers, HasFields, HasSettingsPage, HasLocalizedSettingsPage, HasFactory, HasManagedTaxonomies, HasIndexedPdf;

	protected static string $postType = 'surf-asset';

	public const FIELD_AUTHOR_TYPE         = 'author_type';
	public const FIELD_AUTHOR_USER         = 'author_user';
	public const FIELD_AUTHOR_TEXT         = 'author_text';
	public const FIELD_VERSION             = 'version';
	public const FIELD_VERSION_DATE		   = 'version_date';
	public const FIELD_VERSION_NOTES       = 'version_notes';
	public const FIELD_REVIEW_DATE         = 'review_date';
	public const FIELD_LABEL               = 'label';
	public const FIELD_LIFECYCLE_TEXT      = 'lifecycle_text';
	public const FIELD_FILE_ID             = 'file_id';
	public const FIELD_INDEX_STATUS        = 'index_status';
	public const FIELD_FILE_DESCRIPTION    = 'file_description';
	public const FIELD_EXAMPLE_TITLE       = 'example_title';
	public const FIELD_EXAMPLE_DESCRIPTION = 'example_description';
	public const FIELD_EXAMPLES            = 'examples';
	public const FIELD_RELATED_TITLE       = 'related_title';
	public const FIELD_RELATED_DESCRIPTION = 'related_description';
	public const FIELD_RELATED             = 'related';

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'assets', 'CPT slug', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Asset', 'CPT label singular', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'Assets', 'CPT label plural', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getCategoryTaxonomy(): string
	{
		return AssetCategory::getName();
	}

	/**
	 * @return string
	 */
	public static function getTagTaxonomy(): string
	{
		return Tag::getName();
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
	 * @return TermCollection
	 */
	public function getCategoriesBySecondNiveau(): TermCollection
	{
		$all_terms    = $this->getCategories();
		$parent_terms = $all_terms->filter( function ( $category )
		{
			return $category->parent === 0; // Assuming 0 is the root parent ID
		} );
		if ( $parent_terms->isEmpty() ) {
			return new TermCollection();
		}

		return $all_terms->filter( function ( $category ) use ( $parent_terms )
		{
			return $parent_terms->contains( 'term_id', $category->parent );
		} );
	}

	/**
	 * @return string|null
	 */
	public function label(): ?string
	{
		return $this->getMeta( static::FIELD_LABEL );
	}

	/**
	 * @return string|null
	 */
	public function lifecycleText(): ?string
	{
		return $this->getMeta( static::FIELD_LIFECYCLE_TEXT );
	}

	/**
	 * @return string|null
	 */
	public function version(): ?string
	{
		return $this->getMeta( static::FIELD_VERSION );
	}

	/**
	 * @param string $format
	 * @return string
	 */
	public function versionDate( string $format = 'Ymd' ): string
	{
		$value = $this->getMeta( static::FIELD_VERSION_DATE );
		if ( empty( $value ) ) {
			return '';
		}

		return wp_date( $format, strtotime( $value ) );
	}

	/**
	 * @return string|null
	 */
	public function versionNotes(): ?string
	{
		return $this->getMeta( static::FIELD_VERSION_NOTES );
	}

	/**
	 * @param string $format
	 * @return string
	 */
	public function reviewDate( string $format = 'Ymd' ): string
	{
		$value = $this->getMeta( static::FIELD_REVIEW_DATE );
		if ( empty( $value ) ) {
			return '';
		}

		return wp_date( $format, strtotime( $value ) );
	}

	/**
	 * @return bool
	 */
	public function awaitingReview(): bool
	{
		$date = $this->reviewDate();
		if ( empty( $date ) ) {
			return true;
		}

		$review_date  = strtotime( '+0 minutes', $date );
		$current_date = strtotime( 'today' );

		return $review_date > $current_date;
	}

	/**
	 * @return string
	 */
	public function status(): string
	{
		$default     = AssetStatus::OUT_OF_DATE;
		$review_date = strtotime( $this->reviewDate() );
		if ( empty( $review_date ) ) {
			return $default;
		}

		$expire_date  = strtotime( '+365 days', $review_date );
		$current_date = strtotime( 'today' );

		return $expire_date >= $current_date
			? AssetStatus::UP_TO_DATE
			: $default;
	}

	/**
	 * @return bool
	 */
	public function isUpToDate(): bool
	{
		return $this->status() === AssetStatus::UP_TO_DATE;
	}

	/**
	 * @return bool
	 */
	public function isOutOfDate(): bool
	{
		return $this->status() === AssetStatus::OUT_OF_DATE;
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
	 * @return string|null
	 */
	public function fileDescription(): ?string
	{
		return $this->getMeta( static::FIELD_FILE_DESCRIPTION );
	}

	/**
	 * @return array
	 */
	public function examples(): array
	{
		$meta = $this->getRepeaterMeta( static::FIELD_EXAMPLES, [
			'file_id'          => 0,
			'file_description' => '',
		] );
		if ( empty( $meta ) ) {
			return [];
		}

		foreach ( $meta as $row => $values ) {
			try {
				$file = Attachment::find( (int) $values['file_id'] );
				if ( empty( $file ) ) {
					throw new Exception( 'File not found' );
				}
			} catch ( Exception $exception ) {
				unset( $meta[ $row ] );
				continue;
			}

			$meta[ $row ]['file'] = $file;
		}

		return $meta;
	}

	/**
	 * @return string
	 */
	public static function getDefaultExamplesTitle(): string
	{
		return __( 'Examples', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public function examplesTitle(): string
	{
		$value = $this->getMeta( static::FIELD_EXAMPLE_TITLE );

		return !empty( $value ) ? $value : static::getDefaultExamplesTitle();
	}

	/**
	 * @return string|null
	 */
	public function examplesDescription(): ?string
	{
		return $this->getMeta( static::FIELD_EXAMPLE_DESCRIPTION );
	}

	/**
	 * @return WP_Post|null
	 */
	public static function getAssetsPage(): ?WP_Post
	{
		$pages = get_pages( [
			'meta_key'   => '_wp_page_template',
			'meta_value' => 'page-templates/asset-categories-template.php',
		] );

		return empty( $pages ) ? null : $pages[0];
	}

	/**
	 * @return string
	 */
	public function getAuthor(): string
	{
		$type = $this->getMeta( static::FIELD_AUTHOR_TYPE );
		switch ( $type ) {
			case 'user':
				$id   = $this->getMeta( static::FIELD_AUTHOR_USER );
				$user = get_user_by( 'ID', $id );
				if ( $user instanceof WP_User ) {
					return $user->display_name;
				}

				return _x( 'Surf', 'default author name', 'wp-surf-theme' );

			case 'text':
				return $this->getMeta( static::FIELD_AUTHOR_TEXT );

			default:
				return '';
		}
	}

	/**
	 * @return string
	 */
	public static function getDefaultRelatedTitle(): string
	{
		return __( 'Related assets', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public function relatedTitle(): string
	{
		$value = $this->getMeta( static::FIELD_RELATED_TITLE );

		return !empty( $value ) ? $value : static::getDefaultRelatedTitle();
	}

	/**
	 * @return string|null
	 */
	public function relatedDescription(): ?string
	{
		return $this->getMeta( static::FIELD_RELATED_DESCRIPTION );
	}

	/**
	 * @return array
	 */
	public function related(): array
	{
		$list = $this->getMeta( static::FIELD_RELATED );
		if ( empty( $list ) ) {
			return $this->getDefaultRelated();
		}

		$result = [];
		foreach ( $list as $post_id ) {
			try {
				$asset = static::find( (int) $post_id );
				if ( empty( $asset ) ) {
					throw new Exception( 'Asset not found' );
				}

				$result[ $post_id ] = $asset;
			} catch ( Exception $exception ) {
				continue;
			}
		}

		return $result;
	}

	/**
	 * @return array
	 */
	public function getDefaultRelated(): array
	{
		$categories = $this->categories();
		if ( $categories->isEmpty() ) {
			return [];
		}

		$primary_category = surfGetPrimaryTerm( $this->ID, static::getCategoryTaxonomy() );
		if ( !( $primary_category instanceof WP_Term ) ) {
			return [];
		}

		return static::query( [
			'tax_query'    => [
				[
					'taxonomy' => static::getCategoryTaxonomy(),
					'field'    => 'slug',
					'terms'    => $primary_category->slug,
				],
			],
			'post__not_in' => [ $this->ID ],
			'limit'        => -1,
		] )->toArray();
	}

	// Assets settings

	/**
	 * @return string
	 */
	public function getContactTitle(): string
	{
		return (string) ( Theme::getOption( 'surf-assets-contact-title' ) );
	}

	/**
	 * @return string
	 */
	public function getContactDescription(): string
	{
		return (string) ( Theme::getOption( 'surf-assets-contact-description' ) );
	}

	/**
	 * @return null|array
	 */
	public function getContactButton(): ?array
	{
		$value = Theme::getOption( 'surf-assets-contact-button' );

		return !empty( $value['url'] ) ? $value : null;
	}

	/**
	 * @return array
	 */
	public function getDownloadPropertyList(): array
	{
		$list   = [];
		$format = 'd/m/Y';
		switch ( $this->label() ) {
			case 'surf-approved':
				$list[] = [
					'icon'  => 'surf-approved',
					'label' => __( 'SURF approved', 'wp-surf-theme' ),
				];
				break;

			case 'surf-developed':
				$list[] = [
					'icon'  => 'surf-developed',
					'label' => __( 'SURF developed', 'wp-surf-theme' ),
				];
				break;
		}

		// Add version info
		$version = $this->version();
		if ( !empty( $version ) ) {
			try {
				$date = $this->versionDate( $format );
				if ( !empty( $date ) ) {
					$version = sprintf( __( '%1$s, date %2$s', 'wp-surf-theme' ), $version, $date );
				}
			} catch ( Exception $exception ) {}

			$list[] = [
				'icon'    => 'layer-group',
				'label'   => $version,
				'tooltip' => $this->versionNotes(),
			];
		}

		// Add production date
		try {
			$date   = $this->date( $format );
			$list[] = [
				'icon'  => 'calendar',
				'label' => sprintf( __( 'Production date %1$s', 'wp-surf-theme' ), $date ),
			];
		} catch ( Exception $exception ) {}

		// Add last update date
		$modified = $this->modifiedDate( $format );
		if ( !empty( $modified ) && $modified !== ( $date ?? '' ) ) {
			$list[] = [
				'icon'  => 'calendar-checked',
				'label' => sprintf( __( 'Last update on %1$s', 'wp-surf-theme' ), $modified ),
			];
		}

		// Add planned review date
		$reviewed = $this->reviewDate( $format );
		if ( !empty( $reviewed ) ) {
			$now_date = strtotime( 'today' );
			if ( strtotime( $reviewed ) > $now_date ) {
				$list[] = [
					'icon'  => 'eye',
					'label' => sprintf( __( 'Planned review on %1$s', 'wp-surf-theme' ), $reviewed ),
				];
			} else {
				$list[] = [
					'icon'  => 'eye',
					'label' => sprintf( __( 'Reviewed at %1$s', 'wp-surf-theme' ), $reviewed ),
				];
			}
		}

		// Collect file info, if a file is attached to the asset
		try {
			$file = $this->file();
			if ( !empty( $file ) ) {
				$list[] = [
					'icon'  => 'file',
					'label' => strtoupper( $file->extension() ) . ' ' . surfFileSize( $file->size() ),
				];
			}
		} catch ( Exception $exception ) {}

		return $list;
	}

	/**
	 * @return void
	 */
	public static function registered(): void
	{
		add_action( 'init', function ()
		{
			register_taxonomy_for_object_type( static::getTagTaxonomy(), static::getName() );
		} );

		add_filter( 'manage_' . static::getName() . '_posts_columns', function ( array $columns )
		{
			$columns[ static::FIELD_REVIEW_DATE ] = _x( 'Review Date', 'admin', 'wp-surf-theme' );

			return $columns;
		} );

		add_action( 'manage_' . static::getName() . '_posts_custom_column', function ( $column, $post_id )
		{
			switch ( $column ) {
				case static::FIELD_REVIEW_DATE:
					$value = get_post_meta( $post_id, static::FIELD_REVIEW_DATE, true );
					if ( empty( $value ) ) {
						echo '-';

						return;
					}

					echo wp_date( get_option( 'date_format' ), strtotime( $value ) );

					return;
			}
		}, 10, 2 );

		add_filter( 'manage_edit-' . static::getName() . '_sortable_columns', function ( $columns )
		{
			$columns[ static::FIELD_REVIEW_DATE ] = static::FIELD_REVIEW_DATE;

			return $columns;
		} );

		add_action( 'pre_get_posts', function ( WP_Query $query )
		{
			if ( !is_admin() || !$query->is_main_query() || $query->get( 'post_type' ) !== static::getName() ) {
				return;
			}

			if ( $query->get( 'orderby' ) === static::FIELD_REVIEW_DATE ) {
				$query->set( 'orderby', 'meta_value' );
				$query->set( 'meta_key', static::FIELD_REVIEW_DATE );
			}
		} );

		add_filter( 'wpseo_breadcrumb_links', function ( $crumbs )
		{
			$archive = get_post_type_archive_link( static::getName() );

			return array_map( function ( $crumb ) use ( $archive )
			{
				if ( ( $crumb['url'] ?? null ) !== $archive ) {
					return $crumb;
				}

				$page = static::getAssetsPage();
				if ( empty( $page ) ) {
					return $crumb;
				}

				$crumb['url']  = get_permalink( $page->ID );
				$crumb['text'] = get_the_title( $page->ID );

				return $crumb;
			}, $crumbs );
		} );

		add_filter( 'post_type_link', function ( string $link, WP_Post $post )
		{
			if ( $post->post_type !== static::getName() ) {
				return $link;
			}

			// when on the assets categories archive pages, include the category in the link,
			// so we can add a back link to the category.
			if ( is_tax( static::getCategoryTaxonomy() ) ) {
				$category = get_queried_object();

				return add_query_arg( [ 'category' => $category->slug ], $link );
			}

			return $link;
		}, 10, 2 );

		static::registerPdfIndexHooks( 'asset' );
	}

	/**
	 * @return array
	 */
	public static function getFields(): array
	{
		$fields = [
			[
				'key'       => 'tab_asset_general',
				'label'     => _x( 'General', 'admin', 'wp-surf-theme' ),
				'type'      => 'tab',
				'placement' => 'left',
			],
			static::getFileIDField( 'asset' ),
			static::getFileIndexStatusField( 'asset' ),
			[
				'key'   => 'field_asset_' . static::FIELD_FILE_DESCRIPTION,
				'label' => _x( 'File description', 'admin', 'wp-surf-theme' ),
				'name'  => static::FIELD_FILE_DESCRIPTION,
				'type'  => 'textarea',
			],
			[
				'key'     => 'field_asset_' . static::FIELD_AUTHOR_TYPE,
				'label'   => _x( 'Author Type', 'admin', 'wp-surf-theme' ),
				'name'    => static::FIELD_AUTHOR_TYPE,
				'type'    => 'select',
				'choices' => [
					'user' => _x( 'User', 'admin', 'wp-surf-theme' ),
					'text' => _x( 'Text', 'admin', 'wp-surf-theme' ),
				],
				'wrapper' => [ 'width' => 50 ],
			],
			[
				'key'               => 'field_asset_' . static::FIELD_AUTHOR_USER,
				'label'             => _x( 'User', 'admin', 'wp-surf-theme' ),
				'name'              => static::FIELD_AUTHOR_USER,
				'type'              => 'user',
				'required'          => true,
				'conditional_logic' => [
					[
						'field'    => 'field_asset_' . static::FIELD_AUTHOR_TYPE,
						'operator' => '==',
						'value'    => 'user',
					],
				],
				'wrapper'           => [ 'width' => 50 ],
			],
			[
				'key'               => 'field_asset_' . static::FIELD_AUTHOR_TEXT,
				'label'             => _x( 'Author', 'admin', 'wp-surf-theme' ),
				'name'              => static::FIELD_AUTHOR_TEXT,
				'type'              => 'text',
				'required'          => true,
				'conditional_logic' => [
					[
						'field'    => 'field_asset_' . static::FIELD_AUTHOR_TYPE,
						'operator' => '==',
						'value'    => 'text',
					],
				],
				'wrapper'           => [ 'width' => 50 ],
			],
			[
				'key'      => 'field_asset_' . static::FIELD_VERSION,
				'label'    => _x( 'Version', 'admin', 'wp-surf-theme' ),
				'name'     => static::FIELD_VERSION,
				'type'     => 'text',
				'required' => false,
				'wrapper'  => [ 'width' => 50 ],
			],
			[
				'key'      => 'field_asset_' . static::FIELD_VERSION_DATE,
				'label'    => _x( 'Version Date', 'admin', 'wp-surf-theme' ),
				'name'     => static::FIELD_VERSION_DATE,
				'type'     => 'date_picker',
				'required' => true,
				'wrapper'  => [ 'width' => 50 ],
			],
			[
				'key'     => 'field_asset_' . static::FIELD_VERSION_NOTES,
				'label'   => _x( 'Version notes', 'admin', 'wp-surf-theme' ),
				'name'    => static::FIELD_VERSION_NOTES,
				'type'    => 'textarea',
			],
			[
				'key'      => 'field_asset_' . static::FIELD_REVIEW_DATE,
				'label'    => _x( 'Review Date', 'admin', 'wp-surf-theme' ),
				'name'     => static::FIELD_REVIEW_DATE,
				'type'     => 'date_picker',
				'required' => true,
				'wrapper'  => [ 'width' => 50 ],
			],
			[
				'key'     => 'field_asset_' . static::FIELD_LABEL,
				'label'   => _x( 'Label', 'admin', 'wp-surf-theme' ),
				'name'    => static::FIELD_LABEL,
				'type'    => 'select',
				'wrapper' => [ 'width' => 50 ],
				'choices' => [
					null             => _x( 'None', 'admin', 'wp-surf-theme' ),
					'surf-approved'  => _x( 'SURF approved', 'Asset label', 'wp-surf-theme' ),
					'surf-developed' => _x( 'SURF developed', 'Asset label', 'wp-surf-theme' ),
				],
			],
			[
				'key'     => 'field_asset_' . static::FIELD_LIFECYCLE_TEXT,
				'label'   => _x( 'Lifecycle text', 'admin', 'wp-surf-theme' ),
				'name'    => static::FIELD_LIFECYCLE_TEXT,
				'type'    => 'textarea',
				'wrapper' => [ 'width' => 50 ],
			],
			[
				'key'   => 'tab_asset_examples',
				'label' => _x( 'Examples', 'admin', 'wp-surf-theme' ),
				'type'  => 'tab',
			],
			[
				'key'           => 'field_asset_' . static::FIELD_EXAMPLE_TITLE,
				'label'         => _x( 'Examples title', 'admin', 'wp-surf-theme' ),
				'name'          => static::FIELD_EXAMPLE_TITLE,
				'type'          => 'text',
				'default_value' => static::getDefaultExamplesTitle(),
			],
			[
				'key'   => 'field_asset_' . static::FIELD_EXAMPLE_DESCRIPTION,
				'label' => _x( 'Intro examples', 'admin', 'wp-surf-theme' ),
				'name'  => static::FIELD_EXAMPLE_DESCRIPTION,
				'type'  => 'textarea',
			],
		];
		if ( ACFHelper::allowsRepeater() || ACFHelper::usesPro() ) {
			$fields[] = [
				'key'          => 'field_asset_' . static::FIELD_EXAMPLES,
				'label'        => _x( 'Examples', 'admin', 'wp-surf-theme' ),
				'name'         => static::FIELD_EXAMPLES,
				'type'         => 'repeater',
				'sub_fields'   => [
					[
						'key'      => 'field_asset_examples_file',
						'label'    => _x( 'File', 'admin', 'wp-surf-theme' ),
						'name'     => static::FIELD_FILE_ID,
						'type'     => 'file',
						'required' => true,
						'wrapper'  => [ 'width' => 50 ],
					],
					[
						'key'     => 'field_asset_examples_' . static::FIELD_FILE_DESCRIPTION,
						'label'   => _x( 'File description', 'admin', 'wp-surf-theme' ),
						'name'    => static::FIELD_FILE_DESCRIPTION,
						'type'    => 'textarea',
						'wrapper' => [ 'width' => 50 ],
					],
				],
				'button_label' => _x( 'Add example', 'admin', 'wp-surf-theme' ),
				'layout'       => 'block',
			];
		}

		$fields[] = [
			'key'   => 'tab_asset_related',
			'label' => _x( 'Related assets', 'admin', 'wp-surf-theme' ),
			'type'  => 'tab',
		];
		$fields[] = [
			'key'           => 'field_asset_' . static::FIELD_RELATED_TITLE,
			'label'         => _x( 'Related title', 'admin', 'wp-surf-theme' ),
			'name'          => static::FIELD_RELATED_TITLE,
			'type'          => 'text',
			'default_value' => static::getDefaultRelatedTitle(),
		];
		$fields[] = [
			'key'   => 'field_asset_' . static::FIELD_RELATED_DESCRIPTION,
			'label' => _x( 'Related description', 'admin', 'wp-surf-theme' ),
			'name'  => static::FIELD_RELATED_DESCRIPTION,
			'type'  => 'textarea',
		];
		$fields[] = [
			'key'     => 'field_asset_' . static::FIELD_RELATED . '_message',
			'label'   => '',
			'type'    => 'message',
			'message' => _x( 'By default, this section will be automatically filled with assets in the same (primary) category.<br/>Use the settings below, to override the default behaviour.', 'admin', 'wp-surf-theme' ),
		];
		$fields[] = [
			'key'       => 'field_asset_' . static::FIELD_RELATED,
			'label'     => _x( 'Related assets', 'admin', 'wp-surf-theme' ),
			'name'      => static::FIELD_RELATED,
			'type'      => 'relationship',
			'post_type' => Asset::getName(),
			'multiple'  => 1,
			'filters'   => [ 'search', 'taxonomy' ],
		];

		return [
			[
				'key'    => 'group_asset_settings',
				'title'  => _x( 'Settings', 'admin', 'wp-surf-theme' ),
				'fields' => $fields,
			],
		];
	}

	/**
	 * @return array
	 */
	public static function getLocalizedSettingsFields(): array
	{
		return [
			'key'    => 'group_localized_assets_settings',
			'title'  => _x( 'Assets settings', 'admin', 'wp-surf-theme' ),
			'fields' => [
				[
					'key'       => 'field_assets_settings_contact',
					'label'     => _x( 'Contact block (single)', 'admin', 'wp-surf-theme' ),
					'name'      => '',
					'placement' => 'left',
					'type'      => 'tab',
				],
				[
					'key'   => 'field_assets_settings_contact_title',
					'label' => _x( 'Title', 'admin', 'wp-surf-theme' ),
					'name'  => 'surf-assets-contact-title',
					'type'  => 'text',
				],
				[
					'key'   => 'field_assets_settings_contact_description',
					'label' => _x( 'Description', 'admin', 'wp-surf-theme' ),
					'name'  => 'surf-assets-contact-description',
					'type'  => 'textarea',
				],
				[
					'key'   => 'field_assets_settings_contact_button',
					'label' => _x( 'Button', 'admin', 'wp-surf-theme' ),
					'name'  => 'surf-assets-contact-button',
					'type'  => 'link',
				],
			],
		];
	}

	/**
	 * @return array
	 */
	public static function getSettingsFields(): array
	{
		return [
			'key'    => 'group_assets_settings',
			'title'  => _x( 'Assets settings', 'admin', 'wp-surf-theme' ),
			'fields' => [
				[
					'key'       => 'field_assets_settings_search',
					'label'     => _x( 'Search', 'admin', 'wp-surf-theme' ),
					'name'      => '',
					'placement' => 'left',
					'type'      => 'tab',
				],
				[
					'key'          => 'field_assets_settings_search_deep',
					'label'        => _x( 'Search deep', 'admin', 'wp-surf-theme' ),
					'name'         => 'surf-assets-search-deep',
					'type'         => 'true_false',
					'ui'           => true,
					'instructions' => _x(
						'Allow the global search to also look within Assets authors, categories, tags and files. <strong>Please note:</strong> this may affect performance.',
						'admin',
						'wp-surf-theme'
					),
				],
				[
					'key'       => 'field_assets_settings_header',
					'label'     => _x( 'Header', 'admin', 'wp-surf-theme' ),
					'name'      => '',
					'placement' => 'left',
					'type'      => 'tab',
				],
				[
					'key'          => 'field_assets_settings_header_active',
					'label'        => _x( 'Fancy header', 'admin', 'wp-surf-theme' ),
					'name'         => 'surf-assets-header-active',
					'type'         => 'true_false',
					'ui'           => true,
					'instructions' => _x( 'Make the header, with the navigation and search, fancy.', 'admin', 'wp-surf-theme' ),
				],
				[
					'key'               => 'field_assets_settings_header_content_position',
					'label'             => _x( 'Content position', 'admin', 'wp-surf-theme' ),
					'name'              => 'surf-assets-header-content-position',
					'type'              => 'radio',
					'choices'           => [
						'top'    => _x( 'Top', 'admin', 'wp-surf-theme' ),
						'bottom' => _x( 'Bottom', 'admin', 'wp-surf-theme' ),
					],
					'conditional_logic' => [
						'field'    => 'field_assets_settings_header_active',
						'operator' => '==',
						'value'    => '1',
					],
				],
				[
					'key'               => 'field_assets_settings_header_title',
					'label'             => _x( 'Title', 'admin', 'wp-surf-theme' ),
					'name'              => 'surf-assets-header-title',
					'type'              => 'text',
					'conditional_logic' => [
						'field'    => 'field_assets_settings_header_active',
						'operator' => '==',
						'value'    => '1',
					],
				],
				[
					'key'               => 'field_assets_settings_header_description',
					'label'             => _x( 'Description', 'admin', 'wp-surf-theme' ),
					'name'              => 'surf-assets-header-description',
					'type'              => 'wysiwyg',
					'conditional_logic' => [
						'field'    => 'field_assets_settings_header_active',
						'operator' => '==',
						'value'    => '1',
					],
				],
				[
					'key'               => 'field_assets_settings_header_image',
					'label'             => _x( 'Image', 'admin', 'wp-surf-theme' ),
					'name'              => 'surf-assets-header-image',
					'type'              => 'image',
					'return_format'     => 'id',
					'conditional_logic' => [
						'field'    => 'field_assets_settings_header_active',
						'operator' => '==',
						'value'    => '1',
					],
					'wrapper'           => [
						'width' => '33',
					],
				],
				[
					'key'               => 'field_assets_settings_header_top_fade',
					'label'             => _x( 'Top fade', 'admin', 'wp-surf-theme' ),
					'name'              => 'surf-assets-header-top-fade',
					'type'              => 'range',
					'min'               => 0,
					'max'               => 1,
					'step'              => 0.01,
					'default_value'     => 0,
					'conditional_logic' => [
						'field'    => 'field_assets_settings_header_active',
						'operator' => '==',
						'value'    => '1',
					],
					'wrapper'           => [
						'width' => '33',
					],
				],
				[
					'key'               => 'field_assets_settings_header_bottom_fade',
					'label'             => _x( 'Bottom fade', 'admin', 'wp-surf-theme' ),
					'name'              => 'surf-assets-header-bottom-fade',
					'type'              => 'range',
					'min'               => 0,
					'max'               => 1,
					'step'              => 0.01,
					'default_value'     => 100,
					'conditional_logic' => [
						'field'    => 'field_assets_settings_header_active',
						'operator' => '==',
						'value'    => '1',
					],
					'wrapper'           => [
						'width' => '33',
					],
				],
				[
					'key'       => 'field_assets_settings_archive',
					'label'     => _x( 'Archive', 'admin', 'wp-surf-theme' ),
					'name'      => '',
					'placement' => 'left',
					'type'      => 'tab',
				],
				[
					'key'           => 'field_assets_settings_archive_filters_position',
					'label'         => _x( 'Filters position', 'admin', 'wp-surf-theme' ),
					'name'          => 'surf-assets-archive-filters-position',
					'type'          => 'radio',
					'default_value' => 'top',
					'choices'       => [
						'none'  => _x( 'None', 'admin', 'wp-surf-theme' ),
						'top'   => _x( 'Top', 'admin', 'wp-surf-theme' ),
						'left'  => _x( 'Left', 'admin', 'wp-surf-theme' ),
						'right' => _x( 'Right', 'admin', 'wp-surf-theme' ),
					],
				],
			],
		];
	}

	/**
	 * @return array
	 */
	public static function listSortingOptions(): array
	{
		$options = [
			'title'                   => [
				'label'     => __( 'Name %1$s', 'wp-surf-theme' ),
				'asc_text'  => __( '(A - Z)', 'wp-surf-theme' ),
				'desc_text' => __( '(Z - A)', 'wp-surf-theme' ),
			],
			'date'                    => [
				'label'     => __( 'Publication date %1$s', 'wp-surf-theme' ),
				'asc_text'  => __( '(oldest first)', 'wp-surf-theme' ),
				'desc_text' => __( '(newest first)', 'wp-surf-theme' ),
			],
			'modified'                => [
				'label'     => __( 'Modified date %1$s', 'wp-surf-theme' ),
				'asc_text'  => __( '(oldest first)', 'wp-surf-theme' ),
				'desc_text' => __( '(latest first)', 'wp-surf-theme' ),
			],
			static::FIELD_REVIEW_DATE => [
				'label'     => __( 'Review date %1$s', 'wp-surf-theme' ),
				'asc_text'  => __( '(oldest first)', 'wp-surf-theme' ),
				'desc_text' => __( '(latest first)', 'wp-surf-theme' ),
			],
		];

		$list    = [
			'' => __( 'Sort', 'wp-surf-theme' ),
		];
		foreach ( $options as $key => $texts ) {
			foreach ( [ 'asc', 'desc' ] as $order ) {
				$list_key          = $key . '___' . $order;
				$list[ $list_key ] = sprintf( $texts['label'], $texts[ $order . '_text' ] );
			}
		}

		return $list;
	}

	/**
	 * @param WP_Query $query
	 * @param null|string $sort_by
	 * @return WP_Query
	 */
	public static function setOrderBy( WP_Query $query, ?string $sort_by = null ): WP_Query
	{
		$allowed = static::listSortingOptions();
		if ( empty( $sort_by ) || empty( $allowed[ $sort_by ] ) ) {
			$sort_by = array_key_first( $allowed );
		}

		$parts   = explode( '___', $sort_by );
		$orderby = reset( $parts );
		if ( !in_array( $orderby, [ 'title', 'date', 'modified', 'menu_order', 'rand' ] ) ) {
			$query->set( 'meta_key', $orderby );
			$query->set( 'orderby', 'meta_value' );
		} else {
			$query->set( 'orderby', $orderby );
		}
		$query->set( 'order', $parts[1] ?? 'asc' );

		return $query;
	}

}
