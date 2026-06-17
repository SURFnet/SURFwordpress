<?php

namespace SURF\Core\Taxonomies;

use ArrayAccess;
use SURF\Core\Exceptions\MismatchingTaxonomyException;
use SURF\Core\PostTypes\BasePost;
use SURF\Core\Traits\HasAttributes;
use SURF\Core\Traits\HasMeta;
use SURF\Helpers\Helper;
use WP_Term;
use WP_Term_Query;

/**
 * Class Taxonomy.
 * @property int term_id
 * @property string name
 * @property string slug
 * @property int term_group
 * @property int term_taxonomy_id
 * @property string taxonomy
 * @property string description
 * @property int parent
 * @property int count
 * @property string filters
 */
class Taxonomy implements ArrayAccess
{

	use HasAttributes, HasMeta;

	protected static string $taxonomy = '';
	protected static array  $postTypes = [];

	protected WP_Term $term;

	protected array $guarded = [ 'term_id' ];

	/**
	 * @param array $attributes
	 */
	public function __construct( array $attributes = [] )
	{
		$this->attributes = $attributes;
	}

	/**
	 * @return $this|null
	 * @throws MismatchingTaxonomyException
	 */
	public function parent(): ?static
	{
		$parent = get_term( $this->parent );

		return is_a( $parent, WP_Term::class )
			? static::fromTerm( $parent )
			: null;
	}

	/**
	 * @param int $id
	 * @return static|null
	 * @throws MismatchingTaxonomyException
	 */
	public static function find( int $id ): ?static
	{
		$term = get_term( $id );

		return is_a( $term, WP_Term::class )
			? static::fromTerm( $term )
			: null;
	}

	/**
	 * @param string $slug
	 * @return static|null
	 * @throws MismatchingTaxonomyException
	 */
	public static function findBySlug( string $slug ): ?static
	{
		$term = get_term_by( 'slug', $slug, static::getName() );

		return is_a( $term, WP_Term::class )
			? static::fromTerm( $term )
			: null;
	}

	/**
	 * @param array $args
	 * @return TermCollection
	 */
	public static function query( array $args = [] ): TermCollection
	{
		$query = new WP_Term_Query(
			array_merge(
				[ 'taxonomy' => static::getName() ],
				$args
			)
		);

		return static::fromQuery( $query );
	}

	/**
	 * @param WP_Term_Query $query
	 * @return TermCollection
	 */
	public static function fromQuery( WP_Term_Query $query ): TermCollection
	{
		return TermCollection::fromQuery( $query );
	}

	/**
	 * @param array $args
	 * @return TermCollection
	 */
	public function children( array $args = [] ): TermCollection
	{
		return TermCollection::fromQuery( array_merge( [
			'taxonomy'   => static::getName(),
			'parent'     => $this->term_id,
			'orderby'    => 'term_order',
			'hide_empty' => false,
		], $args ) );
	}

	/**
	 * @return string
	 */
	public function link(): string
	{
		$url = get_term_link( $this->term_id, static::getName() );

		return !is_wp_error( $url ) ? $url : '';
	}

	/**
	 * @param array $attr
	 * @return string
	 */
	public function getArchiveLink( array $attr = [] ): string
	{
		$url = $this->link();
		if ( empty( $url ) ) {
			return '';
		}

		return Helper::buildLink( $url, $this->name, $attr );
	}

	/**
	 * @param $hideEmpty
	 * @return TermCollection
	 */
	public static function getAll( $hideEmpty = false ): TermCollection
	{
		return TermCollection::fromQuery( [
			'taxonomy'   => static::getName(),
			'hide_empty' => $hideEmpty,
		] );
	}

	/**
	 * @return string
	 */
	public static function getAlias(): string
	{
		return str_replace( 'surf-', '', static::getName() );
	}

	/**
	 * @return bool
	 */
	public static function useSlugInFilters(): bool
	{
		return true;
	}

	/**
	 * @param WP_Term $term
	 * @return static
	 * @throws MismatchingTaxonomyException
	 */
	public static function fromTerm( WP_Term $term ): static
	{
		if ( static::class !== Taxonomy::class && $term->taxonomy !== static::getName() ) {
			$class = static::class;
			throw new MismatchingTaxonomyException(
				"Can not create a '{$class}' instance from a term with type '{$term->taxonomy}'."
			);
		}

		$instance             = new static();
		$instance->term       = $term;
		$instance->attributes = (array) $term;

		return $instance;
	}

	/**
	 * @return string
	 */
	public static function getName(): string
	{
		return static::$taxonomy;
	}

	/**
	 * @return array
	 */
	public static function getPostTypes(): array
	{
		return array_map( function ( $postType )
		{
			return is_a( $postType, BasePost::class, true )
				? $postType::getName()
				: $postType;
		}, static::$postTypes );
	}

	/**
	 * @return string
	 */
	public function getMetaType(): string
	{
		return 'term';
	}

	/**
	 * @return int
	 */
	public function getMetaId(): int
	{
		return $this->term_id;
	}

	/**
	 * @return string
	 */
	public static function getQueryKey(): string
	{
		return static::getName();
	}

}
