<?php

namespace SURF\Core\PostTypes;

use ArrayAccess;
use Exception;
use SURF\Core\Exceptions\MismatchingTaxonomyException;
use SURF\Helpers\ColorHelper;
use SURF\Taxonomies\Category;
use SURF\Taxonomies\Tag;
use SURF\Traits\HasFeaturedImage;
use UnexpectedValueException;
use SURF\Core\Exceptions\MismatchingPostTypesException;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\Core\Taxonomies\TermCollection;
use SURF\Core\Traits\HasAttributes;
use SURF\Core\Traits\HasMeta;
use SURF\Enums\Theme;
use SURF\Helpers\AttachmentHelper;
use SURF\Helpers\Helper;
use WP_Error;
use WP_Post;
use WP_Query;
use WP_Term;
use WP_User;

/**
 * Class BasePost
 * @property int ID
 * @property string post_author
 * @property string post_date
 * @property string post_date_gmt
 * @property string post_content
 * @property string post_title
 * @property string post_excerpt
 * @property string post_status
 * @property string comment_status
 * @property string ping_status
 * @property string post_password
 * @property string post_name
 * @property string to_ping
 * @property string pinged
 * @property string post_modified
 * @property string post_modified_gmt
 * @property string post_content_filtered
 * @property int post_parent
 * @property string guid
 * @property int menu_order
 * @property string post_type
 * @property string post_mime_type
 * @property string comment_count
 * @property string filter
 */
class BasePost implements ArrayAccess
{

	use HasTemplateTags, HasAttributes, HasMeta, HasFeaturedImage;

	protected static string $postType = 'any';

	protected array $guarded = [ 'ID' ];

	public ?WP_Post $post = null;

	public const FIELD_HIDE_FEATURED_IMAGE = 'hide_featured_image';
	public const FIELD_CONTENT_OVER_IMAGE  = 'content_over_image';

	/**
	 * @param array $attributes
	 */
	public function __construct( array $attributes = [] )
	{
		$this->attributes = $attributes;
	}

	/**
	 * Get a Collection of posts from the provided args.
	 * Uses WP_Query args, see: https://developer.wordpress.org/reference/classes/wp_query/.
	 * @param array $args
	 * @return PostCollection
	 */
	public static function query( array $args = [] ): PostCollection
	{
		$query = new WP_Query(
			array_merge(
				[ 'post_type' => static::getName() ],
				$args
			)
		);

		return static::fromQuery( $query );
	}

	/**
	 * Get a raw query from the provided args.
	 * Uses WP_Query args, see: https://developer.wordpress.org/reference/classes/wp_query/.
	 * @param array $args
	 * @return WP_Query
	 */
	public static function rawQuery( array $args = [] ): WP_Query
	{
		return new WP_Query(
			array_merge(
				[ 'post_type' => static::getName() ],
				$args
			)
		);
	}

	/**
	 * @param int $id
	 * @return static|null
	 * @throws MismatchingPostTypesException
	 */
	public static function find( int $id ): ?static
	{
		$post = get_post( $id );
		if ( empty( $post ) ) {
			return null;
		}

		if ( static::$postType !== 'any' && static::$postType !== $post->post_type ) {
			return null;
		}

		return static::fromPost( $post );
	}

	/**
	 * @param int $id
	 * @param string|null $lang
	 * @return static|null
	 * @throws MismatchingPostTypesException
	 */
	public static function findForLanguage( int $id, string $lang = null ): ?static
	{
		if ( function_exists( 'pll_get_post' ) ) {
			$id = pll_get_post( $id, $lang ) ?? $id;
		}

		return static::find( $id );
	}

	/**
	 * @param WP_Query $query
	 * @return PostCollection
	 */
	public static function fromQuery( WP_Query $query ): PostCollection
	{
		return PostCollection::fromQuery( $query );
	}

	/**
	 * @return string
	 */
	public static function getName(): string
	{
		return static::$postType;
	}

	/**
	 * @param WP_Post $post
	 * @return static
	 * @throws MismatchingPostTypesException
	 */
	public static function fromPost( WP_Post $post ): static
	{
		if (
			static::class !== BasePost::class &&
			$post->post_type !== static::getName()
		) {
			$class = static::class;
			throw new MismatchingPostTypesException(
				"Can not create a '{$class}' instance from a post with type '{$post->post_type}'."
			);
		}

		$instance             = new static();
		$instance->post       = $post;
		$instance->attributes = (array) $post;

		return $instance;
	}

	/**
	 * @return string
	 */
	public static function getArchiveLink(): string
	{
		return get_post_type_archive_link( static::getName() );
	}

	/**
	 * @return WP_Post
	 * @throws Exception
	 */
	public function getPost(): WP_Post
	{
		if ( !$this->exists() ) {
			throw new UnexpectedValueException(
				"Post does not exist yet, call 'save' method before trying to access the WP_Post object."
			);
		}

		return $this->post;
	}

	/**
	 * @return WP_User|null
	 */
	public function author(): ?WP_User
	{
		return get_user_by( 'ID', $this->post_author ) ?: null;
	}

	/**
	 * @return int|null
	 */
	public function postThumbnailId(): ?int
	{
		return $this->getMeta( '_thumbnail_id' );
	}

	/**
	 * Save the post
	 * @return bool
	 */
	public function save(): bool
	{
		if ( !$this->exists() ) {
			try {
				static::create( $this->attributes );

				return true;
			} catch ( Exception $e ) {
				return false;
			}
		}

		$result = wp_update_post( $this->attributes );

		return !( $result === 0 || is_wp_error( $result ) );
	}

	/**
	 * Create a post from the provided attributes
	 * Keys and values in the $attributes['meta'] array will be added as post meta
	 * @param array $attributes
	 * @return static
	 * @throws Exception
	 */
	public static function create( array $attributes ): self
	{
		$attributes['post_type'] = static::$postType;

		if ( isset( $attributes['meta'] ) ) {
			// Add 'meta' to the 'meta_input' array which is wp_insert_post's default method of adding metadata
			$attributes['meta_input'] = array_merge_recursive( $attributes['meta_input'] ?? [], $attributes['meta'] );
			unset( $attributes['meta'] );
		}

		$id = wp_insert_post( $attributes );

		if ( is_wp_error( $id ) || $id === 0 ) {
			throw new Exception( 'Could not insert post' );
		}

		$post = static::fromPost( get_post( $id ) );

		// Set taxonomy data using wp_set_object_terms
		// We're using this because 'tax_input' in wp_insert_post doesn't always work the way we want
		if ( isset( $attributes['taxonomy'] ) && is_array( $attributes['taxonomy'] ) ) {
			foreach ( $attributes['taxonomy'] as $taxonomy => $terms ) {
				$termIds = wp_set_object_terms( $id, $terms, $taxonomy );

				if ( $termIds instanceof WP_Error ) {
					continue;
				}

				foreach ( $termIds as $termId ) {
					update_term_meta( $termId, 'made_in_factory', true );
				}
			}
		}

		$featured_image = $attributes['featured_image'] ?? null;
		if ( $featured_image && filter_var( $featured_image, FILTER_VALIDATE_URL ) ) {
			AttachmentHelper::createFeaturedImageFromUrl( $post->ID, $featured_image );
		}

		if ( $featured_image && Helper::isBase64( $featured_image ) ) {
			AttachmentHelper::createFeaturedImageFromBase64( $post->ID, $featured_image );
		}

		return $post;
	}

	/**
	 * @return bool
	 */
	public function exists(): bool
	{
		return $this->post !== null;
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public function setupPostdata()
	{
		global $post;
		$post = $this->getPost();
		setup_postdata( $this->getPost() );
	}

	/**
	 * @return void
	 */
	public function resetPostdata()
	{
		wp_reset_postdata();
	}

	/**
	 * @return string
	 */
	public static function getCategoryTaxonomy(): string
	{
		return Category::getName();
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
	public function getCategories( array $args = [] ): TermCollection
	{
		return $this->getTerms( static::getCategoryTaxonomy(), $args );
	}

	/**
	 * @param array $args
	 * @return TermCollection
	 */
	public function getTags( array $args = [] ): TermCollection
	{
		return $this->getTerms( static::getTagTaxonomy(), $args );
	}

	/**
	 * @param string $taxonomy
	 * @param array $args
	 * @return TermCollection|WP_Term[]
	 */
	public function getTerms( string $taxonomy, array $args = [] ): TermCollection|array
	{
		$tax = is_a( $taxonomy, Taxonomy::class, true )
			? $taxonomy::getName()
			: $taxonomy;

		return TermCollection::fromQuery(
			array_merge(
				[
					'taxonomy'   => $tax,
					'hide_empty' => false,
					'object_ids' => $this->ID,
				],
				$args
			)
		);
	}

	/**
	 * @param string $taxonomy
	 * @return Taxonomy|null
	 * @throws MismatchingTaxonomyException
	 */
	public function getPrimaryTerm( string $taxonomy ): Taxonomy|null
	{
		$tax = is_a( $taxonomy, Taxonomy::class, true )
			? $taxonomy::getName()
			: $taxonomy;

		if ( function_exists( 'yoast_get_primary_term_id' ) ) {
			$term_id = yoast_get_primary_term_id( $tax, $this->ID() );
		}

		if ( empty( $term_id ) ) {
			$term_id = $this->getTerms( $taxonomy )->first()?->term_id;
		}

		if ( !$term_id ) {
			return null;
		}

		$term = get_term( $term_id );

		return is_a( $term, WP_Term::class )
			? Taxonomy::fromTerm( $term )
			: null;
	}

	/**
	 * @return string
	 */
	public function getMetaType(): string
	{
		return 'post';
	}

	/**
	 * @return int
	 */
	public function getMetaId(): int
	{
		return wp_is_post_revision( $this->ID ) ?: $this->ID;
	}

	/**
	 * @param array $args
	 * @return TermCollection
	 */
	public function categories( array $args = [] ): TermCollection
	{
		return new TermCollection();
	}

	/**
	 * @param string $taxonomy
	 * @param bool $fallback
	 * @return WP_Term|null
	 */
	public function primaryCategory( string $taxonomy = 'category', bool $fallback = false ): ?WP_Term
	{
		$term_id = null;
		if ( function_exists( 'yoast_get_primary_term_id' ) ) {
			$term_id = yoast_get_primary_term_id( $taxonomy, $this->ID() );
		}
		if ( empty( $term_id ) && $fallback ) {
			$term_id = $this->getTerms( $taxonomy )->first()?->term_id;
		}
		if ( empty( $term_id ) ) {
			return null;
		}

		$term = get_term( $term_id, $taxonomy );
		if ( !$term || is_wp_error( $term ) ) {
			return null;
		}

		return $term;
	}

	/**
	 * @param bool $fallback
	 * @return string|null
	 */
	public function getCategoryName( bool $fallback = false ): ?string
	{
		return $this->primaryCategory( fallback: $fallback )?->name ?? '';
	}

	/**
	 * @param bool $fallback
	 * @return int|null
	 */
	public function getCategoryId( bool $fallback = false ): ?int
	{
		return $this->primaryCategory( fallback: $fallback )?->term_id ?? 0;
	}

	/**
	 * @param bool $fallback
	 * @return string|null
	 */
	public function getCategoryUrl( bool $fallback = false ): ?string
	{
		$term = $this->primaryCategory( fallback: $fallback );
		if ( empty( $term ) ) {
			return null;
		}

		$url = get_term_link( $term, $term->taxonomy );

		return !is_wp_error( $url ) ? $url : null;
	}

	/**
	 * @param bool $fallback
	 * @return mixed|string
	 */
	public function getCategoryColor( bool $fallback = false )
	{
		$default = ColorHelper::getHexByName();
		$term_id = $this->getCategoryId( $fallback );
		if ( empty( $term_id ) ) {
			return $default;
		}

		$color = get_term_meta( $term_id, 'category_color', true );
		if ( empty( $color ) ) {
			return $default;
		}

		$allowed = Theme::categoryColorPaletteFlat();
		if ( !array_key_exists( $color, $allowed ) ) {
			return $default;
		}

		return $color;
	}

	/**
	 * @return bool|null
	 */
	public function getContentOverImage(): ?bool
	{
		return (bool) $this->getMeta( static::FIELD_CONTENT_OVER_IMAGE, true );
	}

}
