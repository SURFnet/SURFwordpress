<?php

namespace SURF\PostTypes;

use Illuminate\Support\Str;
use SURF\Core\Exceptions\MismatchingTaxonomyException;
use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\HasFactory;
use SURF\Core\PostTypes\PostCollection;
use SURF\Core\PostTypes\Registers;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\Core\Taxonomies\TermCollection;
use SURF\Core\Traits\HasFields;
use SURF\Core\Traits\HasLocalizedSettingsPage;
use SURF\Core\Traits\HasSettingsPage;
use SURF\Taxonomies\FaqCategory;
use SURF\Taxonomies\FaqTag;
use SURF\Traits\HasManagedTaxonomies;

/**
 * Class Faq
 * @package SURF\PostTypes
 */
class Faq extends BasePost
{

	use Registers, HasFields, HasFactory, HasSettingsPage, HasLocalizedSettingsPage, HasManagedTaxonomies;

	protected static string $postType = 'surf-faq';

	/**
	 * @return string[]
	 */
	public static function getArgs(): array
	{
		return [
			'menu_icon' => 'dashicons-editor-help',
		];
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'faqs', 'CPT slug', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'FAQ', 'CPT label singular', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'FAQs', 'CPT label plural', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getCategoryTaxonomy(): string
	{
		return FaqCategory::getName();
	}

	/**
	 * @return string
	 */
	public static function getTagTaxonomy(): string
	{
		return FaqTag::getName();
	}

	/**
	 * @param array $args
	 * @return TermCollection
	 */
	public function categories( array $args = [] ): TermCollection
	{
		return $this->getTerms( static::getCategoryTaxonomy(), $args );
	}

	/**
	 * @return Taxonomy|null
	 * @throws MismatchingTaxonomyException
	 */
	public function getPrimaryParentCategory(): Taxonomy|null
	{
		$term = $this->getPrimaryTerm( static::getCategoryTaxonomy() );
		if ( !$term ) {
			return null;
		}

		while ( $term->parent() !== null ) {
			$term = $term->parent();
		}

		return $term;
	}

	/**
	 * @param array $args
	 * @return TermCollection
	 */
	public function faqTags( array $args = [] ): TermCollection
	{
		return $this->getTerms( static::getTagTaxonomy(), $args );
	}

	/**
	 * @return PostCollection
	 */
	public function relatedQuestions(): PostCollection
	{
		return static::query( [ 'post__in' => $this->getMeta( 'related_questions' ) ?: [ 0 ] ] );
	}

	/**
	 * @return int|null
	 */
	public function customId(): ?int
	{
		return $this->getMeta( 'ID' );
	}

	/**
	 * @return bool
	 */
	public static function useExtraFields(): bool
	{
		return get_option( 'options_faq_single_use_extra_fields', false );
	}

	/**
	 * @return string
	 */
	public function getTitlePrefix(): string
	{
		if ( !static::useExtraFields() ) {
			return '';
		}

		return $this->getMeta( 'title_prefix', '' );
	}

	/**
	 * @param bool $snake
	 * @return array
	 */
	public static function getContentBlockLabels( bool $snake = false ): array
	{
		if ( !static::useExtraFields() ) {
			return [];
		}

		$total = (int) get_option( 'options_faq_single_content_blocks', 0 );
		if ( $total < 1 ) {
			return [];
		}

		$blocks = [];
		for ( $i = 0; $i < $total; $i++ ) {
			$enabled = (bool) get_option( 'options_faq_single_content_blocks_' . $i . '_enabled', true );
			if ( !$enabled ) {
				continue;
			}

			$value = get_option( 'options_faq_single_content_blocks_' . $i . '_label' ) ?: sprintf(
				_x( 'Content block %s', 'admin', 'wp-surf-theme' ),
				$i + 1
			);

			if ( $snake ) {
				$value = Str::snake( $value );
			}

			$blocks[ $i ] = $value;
		}

		return $blocks;
	}

	/**
	 * @param bool $snake
	 * @return array
	 */
	public function getContentBlocks( bool $snake = false ): array
	{
		if ( !static::useExtraFields() ) {
			return [];
		}

		$blocks = [];
		$labels = static::getContentBlockLabels( $snake );
		foreach ( $labels as $i => $label ) {
			$blocks[ $i ] = [
				'label' => $label,
				'value' => $this->getMeta( "content_block_$i" ),
			];
		}

		return $blocks;
	}

	/**
	 * @return string
	 */
	public function fullTitle(): string
	{
		return trim( implode( ' ', [ $this->getTitlePrefix(), $this->title() ] ) );
	}

	/**
	 * @return string
	 */
	public function fullContent(): string
	{
		$content = $this->content();
		foreach ( $this->getContentBlocks() as $block ) {
			$content .= sprintf( "\n<p><strong>%s</strong></p>", $block['label'] ?? '' );

			// Apply same functionality as applied in the_content().
			$value = apply_filters( 'the_content', $block['value'] ?? '' );
			$value = str_replace( ']]>', ']]&gt;', $value );

			$content .= "\n$value";
		}

		return $content;
	}

	/**
	 * @return array
	 */
	public static function getFields(): array
	{
		$fields = [];
		if ( static::useExtraFields() ) {
			$blockFields = [
				[
					'key'   => 'field_faq_title_prefix',
					'label' => get_option( 'options_faq_single_title_prefix_label' ) ?: _x(
						'Title prefix',
						'admin',
						'wp-surf-theme'
					),
					'name'  => 'title_prefix',
					'type'  => 'text',
				],
			];

			$labels = static::getContentBlockLabels();

			foreach ( $labels as $i => $label ) {
				$blockFields[] = [
					'key'   => "field_faq_content_block_$i",
					'label' => $label,
					'name'  => "content_block_$i",
					'type'  => 'wysiwyg',
				];
			}

			$fields[] = [
				'key'    => 'group_faq_content_settings',
				'title'  => _x( 'Content settings', 'admin', 'wp-surf-theme' ),
				'fields' => $blockFields,
			];
		}

		$fields[] = [
			'key'    => 'group_faq_settings',
			'title'  => _x( 'Settings', 'admin', 'wp-surf-theme' ),
			'fields' => [
				get_option( 'options_faq_single_disable_id' ) === '1' ? [] : [
					'key'   => 'field_faq_ID',
					'label' => _x( 'ID', 'admin', 'wp-surf-theme' ),
					'name'  => 'ID',
					'type'  => 'text',
				],
				[
					'key'       => 'field_faq_related_questions',
					'label'     => _x( 'Related questions', 'admin', 'wp-surf-theme' ),
					'name'      => 'related_questions',
					'type'      => 'relationship',
					'filters'   => [ 'search', 'taxonomy' ],
					'post_type' => static::getName(),
				],
			],
		];

		return $fields;
	}

	/**
	 * @return array
	 */
	public static function getLocalizedSettingsFields(): array
	{
		return [
			'key'    => 'faq_group_localized_settings',
			'title'  => _x( 'FAQ settings', 'admin', 'wp-surf-theme' ),
			'fields' => [
				[
					'key'       => 'faq_field_faq_single_localized_settings_tab',
					'type'      => 'tab',
					'label'     => _x( 'Single settings', 'admin', 'wp-surf-theme' ),
					'placement' => 'left',
				],
				[
					'key'          => 'faq_field_faq_single_title',
					'label'        => _x( 'Title', 'admin', 'wp-surf-theme' ),
					'name'         => 'faq_field_single_title',
					'type'         => 'text',
					'required'     => true,
					'instructions' => _x( 'The title shown on top of the FAQ single.', 'admin', 'wp-surf-theme' ),
				],
				[
					'key'          => 'faq_field__faq_single_related_questions_title',
					'label'        => _x( 'Related questions title', 'admin', 'wp-surf-theme' ),
					'name'         => 'faq_field_single_related_questions_title',
					'type'         => 'text',
					'required'     => true,
					'instructions' => _x(
						'The title shown on top of the "related questions" block on the FAQ single.',
						'admin',
						'wp-surf-theme'
					),
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
			'key'    => 'faq_group_settings',
			'title'  => _x( 'FAQ settings', 'admin', 'wp-surf-theme' ),
			'fields' => [
				[
					'key'       => 'faq_field_faq_archive_settings_tab',
					'type'      => 'tab',
					'label'     => _x( 'Archive settings', 'admin', 'wp-surf-theme' ),
					'placement' => 'left',
				],
				[
					'key'          => 'faq_field_faq_archive_hide_labels',
					'name'         => 'faq_archive_hide_labels',
					'label'        => _x( 'Hide archive labels', 'admin', 'wp-surf-theme' ),
					'type'         => 'true_false',
					'ui'           => true,
					'instructions' => _x( 'Hide the main category label on the archive page.', 'admin', 'wp-surf-theme' ),
				],
				[
					'key'           => 'faq_field_faq_archive_posts_per_page',
					'name'          => 'faq_archive_posts_per_page',
					'label'         => _x( 'FAQs per page', 'admin', 'wp-surf-theme' ),
					'type'          => 'number',
					'default_value' => 8,
					'instructions'  => _x( 'The amount of items to show on one page. Keep in mind that setting this to high numbers (100+) can negatively affect performance.', 'admin', 'wp-surf-theme' ),
				],
				[
					'key'          => 'faq_field_faq_show_parent_categories',
					'name'         => 'faq_show_parent_categories',
					'label'        => _x( 'Show parent categories', 'admin', 'wp-surf-theme' ),
					'type'         => 'true_false',
					'ui'           => true,
					'instructions' => _x( 'Show the parent category above each category on a FAQ item.', 'admin', 'wp-surf-theme' ),
				],
				[
					'key'          => 'faq_field_faq_archive_show_export_button',
					'name'         => 'faq_archive_show_export_button',
					'label'        => _x( 'Show export button', 'admin', 'wp-surf-theme' ),
					'type'         => 'true_false',
					'ui'           => true,
					'instructions' => _x( 'Show the export button that allows the user to generate an export of the items while respecting the selected filters and sorting.', 'admin', 'wp-surf-theme' ),
				],
				[
					'key'          => 'faq_field_faq_archive_dynamic_filter_counts',
					'name'         => 'faq_archive_dynamic_filter_counts',
					'label'        => _x( 'Show dynamic filter counts', 'admin', 'wp-surf-theme' ),
					'type'         => 'true_false',
					'ui'           => true,
					'instructions' => _x( 'Show post counts behind each filter that update when changing filters.', 'admin', 'wp-surf-theme' ),
				],
				[
					'key'               => 'faq_field_faq_archive_dynamic_filter_counts_separator',
					'name'              => 'faq_archive_dynamic_filter_counts_separator',
					'label'             => _x( 'Dynamic filter counts separator', 'admin', 'wp-surf-theme' ),
					'type'              => 'text',
					'instructions'      => _x( 'The separator that is shown between the current and total counts. For example: "/" would result in "15/30".<br>There is no default value, meaning that by default it would be shown as "1530".', 'admin', 'wp-surf-theme' ),
					'conditional_logic' => [
						[
							[
								'field'    => 'faq_field_faq_archive_dynamic_filter_counts',
								'operator' => '==',
								'value'    => '1',
							],
						],
					],
				],
				[
					'key'       => 'faq_field_faq_single_settings_tab',
					'type'      => 'tab',
					'label'     => _x( 'Single settings', 'admin', 'wp-surf-theme' ),
					'placement' => 'left',
				],
				[
					'key'          => 'faq_field_faq_single_use_extra_fields',
					'name'         => 'faq_single_use_extra_fields',
					'instructions' => _x( 'Enable this setting to unlock the title prefix and content blocks.', 'admin', 'wp-surf-theme' ),
					'label'        => _x( 'Use extra fields', 'admin', 'wp-surf-theme' ),
					'type'         => 'true_false',
					'ui'           => true,
				],
				[
					'key'               => 'faq_field_faq_single_title_prefix_label',
					'name'              => 'faq_single_title_prefix_label',
					'instructions'      => _x( 'The label for the title prefix.', 'admin', 'wp-surf-theme' ),
					'label'             => _x( 'Title prefix label', 'admin', 'wp-surf-theme' ),
					'type'              => 'text',
					'conditional_logic' => [
						[
							[
								'field'    => 'faq_field_faq_single_use_extra_fields',
								'operator' => '==',
								'value'    => '1',
							],
						],
					],
				],
				[
					'key'               => 'faq_field_faq_single_content_blocks',
					'name'              => 'faq_single_content_blocks',
					'label'             => _x( 'Content blocks', 'admin', 'wp-surf-theme' ),
					'type'              => 'repeater',
					'max'               => 8,
					'sub_fields'        => [
						[
							'key'           => 'faq_field_faq_single_content_block_enabled',
							'name'          => 'enabled',
							'instructions'  => _x( 'Toggle the visibility of each content block.', 'admin', 'wp-surf-theme' ),
							'label'         => _x( 'Enabled', 'admin', 'wp-surf-theme' ),
							'type'          => 'true_false',
							'ui'            => true,
							'default_value' => true,
						],
						[
							'key'          => 'faq_field_faq_single_content_block_label',
							'name'         => 'label',
							'instructions' => _x( 'The label that will be displayed above each content block.', 'admin', 'wp-surf-theme' ),
							'label'        => _x( 'Label', 'admin', 'wp-surf-theme' ),
							'type'         => 'text',
						],
					],
					'conditional_logic' => [
						[
							[
								'field'    => 'faq_field_faq_single_use_extra_fields',
								'operator' => '==',
								'value'    => '1',
							],
						],
					],
				],
				[
					'key'   => 'faq_field_faq_single_disable_id',
					'name'  => 'faq_single_disable_id',
					'label' => _x( 'Disable ID', 'admin', 'wp-surf-theme' ),
					'type'  => 'true_false',
					'ui'    => true,
				],
			],
		];
	}

	/**
	 * @return void
	 */
	public static function registered()
	{
		add_action( 'acf/init', [ static::class, 'registerSettingsPage' ], 9 );
		add_filter( 'wpseo_schema_webpage_type', function ( $types )
		{
			if ( is_single() && get_post_type() === static::$postType && !in_array( 'FAQPage', $types ) ) {
				$types[] = 'FAQPage';
			}

			return $types;
		} );
	}

}
