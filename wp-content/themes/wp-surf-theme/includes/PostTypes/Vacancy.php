<?php

namespace SURF\PostTypes;

use SURF\Core\Exceptions\MismatchingPostTypesException;
use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\HasFactory;
use SURF\Core\PostTypes\PostCollection;
use SURF\Core\PostTypes\Registers;
use SURF\Core\Taxonomies\TermCollection;
use SURF\Core\Traits\HasFields;
use SURF\Core\Traits\HasLocalizedSettingsPage;
use SURF\Enums\Theme;
use SURF\Helpers\ACFHelper;
use SURF\Helpers\ColorHelper;
use SURF\Helpers\Helper;
use SURF\Helpers\PolylangHelper;
use SURF\Helpers\RoadmapHelper;
use SURF\Plugins\AcfPro\AcfPro;
use SURF\Taxonomies\VacancyCategory;
use SURF\Taxonomies\VacancyHours;
use SURF\Traits\HasArchiveWidgetAreaFilters;
use SURF\Traits\HasManagedTaxonomies;
use WP_Query;
use WP_Term;

/**
 * Class Vacancy
 * @package SURF\PostTypes
 */
class Vacancy extends BasePost
{

	use Registers, HasFields, HasFactory, HasLocalizedSettingsPage, HasArchiveWidgetAreaFilters, HasManagedTaxonomies;

	public const FIELD_HIDE_FROM_ARCHIVE = 'hide_from_archive';
	public const FIELD_SOURCE            = 'source';
	public const FIELD_JOB_ID            = 'job_id';
	public const FIELD_LOCATION          = 'location';
	public const FIELD_DEGREE            = 'degree';
	public const FIELD_EMPLOYMENT        = 'employment';
	public const FIELD_SALARY            = 'salary';
	public const FIELD_DEADLINE          = 'deadline';
	public const FIELD_CONTACT           = 'contact';
	public const FIELD_CONTACT_TITLE     = 'title';
	public const FIELD_CONTACT_PERSON    = 'person';
	public const FIELD_CONTACT_EMAIL     = 'email';
	public const FIELD_CONTACT_PHONE     = 'phone';

	public const SOURCE_EMPLY = 'emply';

	protected static string $postType = 'surf-vacancy';

	/**
	 * @return string[]
	 */
	public static function getArgs(): array
	{
		return [
			'menu_icon' => 'dashicons-nametag',
		];
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'vacancy', 'CPT slug', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Vacancy', 'CPT label singular', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'Vacancies', 'CPT label plural', 'wp-surf-theme' );
	}

	/**
	 * @param $id
	 * @return WP_Term|null
	 */
	public function getPrimaryCategory( $id ): ?WP_Term
	{
		$term = surfGetPrimaryTerm( $id, VacancyCategory::getName() );

		return $term instanceof WP_Term ? $term : null;
	}

	/**
	 * @param $id
	 * @return string|null
	 */
	public function getPrimaryCategoryName( $id ): ?string
	{
		$term = surfGetPrimaryTerm( $id, VacancyCategory::getName() );

		return $term instanceof WP_Term ? ( $term->name ?? null ) : null;
	}

	/**
	 * @param $id
	 * @return int|null
	 */
	public function getPrimaryCategoryId( $id ): ?int
	{
		$term = surfGetPrimaryTerm( $id, VacancyCategory::getName() );

		return $term instanceof WP_Term ? ( $term->term_id ?? null ) : null;
	}

	/**
	 * @param $id
	 * @return string|null
	 */
	public function getPrimaryCategoryColor( $id ): ?string
	{
		$default = ColorHelper::getHexByName();
		$term    = surfGetPrimaryTerm( $id, VacancyCategory::getName() );
		if ( !( $term instanceof WP_Term ) ) {
			return null;
		}

		$color = VacancyCategory::getTermColor( $term->term_id, $default );

		// Add fallback from yellow to orange because yellow is giving bad contrast with white text and backgrounds
		if ( strtoupper( $color ) === Theme::getColorHexBySlug( ColorHelper::COLOR_YELLOW ) ) {
			$color = Theme::getColorHexBySlug( ColorHelper::COLOR_ORANGE );
		}

		return $color;
	}

	/**
	 * @param $id
	 * @return string|null
	 */
	public function getPrimaryHoursName( $id ): ?string
	{
		$term = surfGetPrimaryTerm( $id, VacancyHours::getName() );

		return $term instanceof WP_Term ? ( $term->name ?? null ) : null;
	}

	/**
	 * @return string|null
	 */
	public function getHours(): ?string
	{
		return $this->getPrimaryHoursName( $this->ID() );
	}

	/**
	 * @param array $args
	 * @return TermCollection
	 */
	public function categories( array $args = [] ): TermCollection
	{
		return $this->getTerms( VacancyCategory::getName(), $args );
	}

	// Meta fields

	/**
	 * @return mixed
	 */
	public function getSalary()
	{
		return $this->getMeta( static::FIELD_SALARY );
	}

	/**
	 * @return string|null
	 */
	public function getMinSalary(): ?string
	{
		$salary = $this->getSalary();
		if ( empty( $salary ) ) {
			return null;
		}

		[ $min, ] = explode( '-', $salary );

		return filter_var( $min, FILTER_SANITIZE_NUMBER_INT );
	}

	/**
	 * @return string|null
	 */
	public function getMaxSalary(): ?string
	{
		$salary = $this->getSalary();
		if ( empty( $salary ) ) {
			return null;
		}

		$parts = explode( '-', $salary );
		$max   = $parts[1] ?? null;

		return filter_var( $max, FILTER_SANITIZE_NUMBER_INT );
	}

	/**
	 * @return mixed
	 */
	public function getLocation()
	{
		return $this->getMeta( static::FIELD_LOCATION );
	}

	/**
	 * @return mixed
	 */
	public function getDegree()
	{
		return $this->getMeta( static::FIELD_DEGREE );
	}

	/**
	 * @return mixed
	 */
	public function getEmployment()
	{
		return $this->getMeta( static::FIELD_EMPLOYMENT );
	}

	/**
	 * @param $format
	 * @return mixed|string
	 */
	public function getDeadline( $format = 'd F Y' )
	{
		$deadline = $this->getMeta( static::FIELD_DEADLINE );
		if ( !empty( $deadline ) ) {
			$deadline = date_i18n( $format, strtotime( $deadline ) );
		}

		return $deadline;
	}

	/**
	 * @return array
	 */
	public function getContact(): array
	{
		return $this->getRepeaterMeta( static::FIELD_CONTACT, [
			'title'  => '',
			'person' => '',
			'email'  => '',
			'phone'  => '',
		] );
	}

	/**
	 * @return bool
	 */
	public function shouldShowMeta(): bool
	{
		return $this->getLocation()
		       || $this->getDegree()
		       || $this->getEmployment()
		       || $this->getSalary()
		       || $this->getPrimaryCategoryId( $this->ID() )
		       || $this->getDeadline()
		       || !empty( $this->getContact() );
	}

	/**
	 * @return string
	 */
	public function getEmploymentConditions(): string
	{
		$prefix = PolylangHelper::getCurrentLanguageSlug();

		return get_option( "{$prefix}_employment_conditions", '' ) ?: '';
	}

	/**
	 * @return string
	 */
	public function getEmploymentConditionsDisclaimer(): string
	{
		$prefix = static::getLocalizedSettingsPrefix();

		return get_option( "{$prefix}_employment_conditions_disclaimer", '' ) ?: '';
	}

	/**
	 * @return BasePost|null
	 * @throws MismatchingPostTypesException
	 */
	protected static function getEditedPost(): ?BasePost
	{
		$id     = Helper::getSanitizedRequest( 'post' );
		$action = Helper::getSanitizedRequest( 'action' );
		if ( !is_admin() || !$id || !$action ) {
			return null;
		}

		$post = get_post( $id );
		if ( $post->post_type !== static::getName() ) {
			return null;
		}

		return static::fromPost( $post );
	}

	/**
	 * @param array $args
	 * @param bool $withoutHidden
	 * @return PostCollection
	 */
	public static function query( array $args = [], bool $withoutHidden = false ): PostCollection
	{
		$baseArgs = [ 'post_type' => static::getName() ];
		if ( $withoutHidden ) {
			$baseArgs['meta_query'] = [
				[
					'relation' => 'OR',
					[
						'key'     => static::FIELD_HIDE_FROM_ARCHIVE,
						'compare' => 'NOT EXISTS',
					],
					[
						'key'     => static::FIELD_HIDE_FROM_ARCHIVE,
						'compare' => '==',
						'value'   => '',
					],
					[
						'key'     => static::FIELD_HIDE_FROM_ARCHIVE,
						'compare' => '==',
						'value'   => 0,
					],
				],
			];
		}
		$query = new WP_Query( array_merge( $baseArgs, $args ) );

		return static::fromQuery( $query );
	}

	/**
	 * @return array
	 * @throws MismatchingPostTypesException
	 */
	public static function getFields(): array
	{
		$post     = static::getEditedPost();
		$source   = $post?->getMeta( static::FIELD_SOURCE );
		$readOnly = $source === static::SOURCE_EMPLY;
		$contacts = $post?->getMeta( static::FIELD_CONTACT );
		$fields   = [
			$source === static::SOURCE_EMPLY ? [
				'key'          => 'field_vacancy_source',
				'label'        => _x( 'Source', 'admin', 'wp-surf-theme' ),
				'instructions' => _x( 'This vacancy cannot be edited since it has been imported from an external source.', 'admin', 'wp-surf-theme' ),
				'name'         => static::FIELD_SOURCE,
				'type'         => 'text',
				'readonly'     => true,
				'wrapper'      => [
					'width' => 50,
				],
			] : [],
			$source === static::SOURCE_EMPLY ? [
				'key'          => 'field_vacancy_job_id',
				'label'        => _x( 'Emply ID', 'admin', 'wp-surf-theme' ),
				'instructions' => _x( 'The job id this vacancy has in Emply.', 'admin', 'wp-surf-theme' ),
				'name'         => static::FIELD_JOB_ID,
				'type'         => 'text',
				'readonly'     => true,
				'wrapper'      => [
					'width' => 50,
				],
			] : [],
			[
				'key'      => 'field_vacancy_location',
				'label'    => _x( 'Location', 'admin', 'wp-surf-theme' ),
				'name'     => static::FIELD_LOCATION,
				'type'     => 'text',
				'readonly' => $readOnly,
				'wrapper'  => [
					'width' => 50,
				],
			],
			[
				'key'      => 'field_vacancy_degree',
				'label'    => _x( 'Degree', 'admin', 'wp-surf-theme' ),
				'name'     => static::FIELD_DEGREE,
				'type'     => 'text',
				'readonly' => $readOnly,
				'wrapper'  => [
					'width' => 50,
				],
			],
			[
				'key'      => 'field_vacancy_employment',
				'label'    => _x( 'Employment', 'admin', 'wp-surf-theme' ),
				'name'     => static::FIELD_EMPLOYMENT,
				'type'     => 'text',
				'readonly' => $readOnly,
				'wrapper'  => [
					'width' => 50,
				],
			],
			[
				'key'      => 'field_vacancy_salary',
				'label'    => _x( 'Salary', 'admin', 'wp-surf-theme' ),
				'name'     => static::FIELD_SALARY,
				'type'     => 'text',
				'readonly' => $readOnly,
				'wrapper'  => [
					'width' => 50,
				],
			],
			[
				'key'      => 'field_vacancy_deadline',
				'label'    => _x( 'Deadline', 'admin', 'wp-surf-theme' ),
				'name'     => static::FIELD_DEADLINE,
				'type'     => 'date_picker',
				'readonly' => $readOnly,
				'wrapper'  => [
					'width' => 50,
				],
			],
		];

		if ( ACFHelper::allowsRepeater() ) {
			$fields[] = [
				'key'          => 'field_vacancy_contact',
				'label'        => _x( 'Contact', 'admin', 'wp-surf-theme' ),
				'name'         => static::FIELD_CONTACT,
				'type'         => 'repeater',
				'button_label' => _x( 'Add Contact', 'admin', 'wp-surf-theme' ),
				'min'          => $readOnly ? $contacts : null,
				'max'          => $readOnly ? $contacts : null,
				'sub_fields'   => [
					[
						'key'      => 'field_vacancy_contact_title',
						'label'    => _x( 'Title', 'admin', 'wp-surf-theme' ),
						'name'     => static::FIELD_CONTACT_TITLE,
						'type'     => 'text',
						'readonly' => $readOnly,
					],
					[
						'key'      => 'field_vacancy_contact_person',
						'label'    => _x( 'Person', 'admin', 'wp-surf-theme' ),
						'name'     => static::FIELD_CONTACT_PERSON,
						'type'     => 'text',
						'readonly' => $readOnly,
					],
					[
						'key'      => 'field_vacancy_contact_email',
						'label'    => _x( 'Email', 'admin', 'wp-surf-theme' ),
						'name'     => static::FIELD_CONTACT_EMAIL,
						'type'     => 'text',
						'readonly' => $readOnly,
					],
					[
						'key'      => 'field_vacancy_contact_phone',
						'label'    => _x( 'Phone', 'admin', 'wp-surf-theme' ),
						'name'     => static::FIELD_CONTACT_PHONE,
						'type'     => 'text',
						'readonly' => $readOnly,
					],
				],
			];
		}

		return [
			[
				'key'    => 'group_vacancies_settings',
				'title'  => _x( 'Settings', 'admin', 'wp-surf-theme' ),
				'fields' => $fields,
			],
			static::getImageGroup( static::getName() ),
		];
	}

	/**
	 * @return array
	 */
	public static function getLocalizedSettingsFields(): array
	{
		return [
			'key'    => 'group_vacancy_settings',
			'title'  => _x( 'Vacancy settings', 'admin', 'wp-surf-theme' ),
			'fields' => [
				[
					'key'       => 'field_vacancy_settings_roadmap_tab',
					'label'     => _x( 'Roadmap default settings', 'admin', 'wp-surf-theme' ),
					'type'      => 'tab',
					'placement' => 'left',
				],
				[
					'key'   => 'field_vacancy_settings_roadmap_title',
					'label' => _x( 'Roadmap Title', 'admin', 'wp-surf-theme' ),
					'name'  => 'roadmap_default_title',
					'type'  => 'text',
				],
				[
					'key'   => 'field_vacancy_settings_roadmap_subtitle',
					'label' => _x( 'Roadmap Subtitle', 'admin', 'wp-surf-theme' ),
					'name'  => 'roadmap_default_subtitle',
					'type'  => 'text',
				],
				[
					'key'     => 'field_vacancy_settings_roadmap_dislpay',
					'label'   => _x( 'Roadmap display', 'admin', 'wp-surf-theme' ),
					'name'    => 'roadmap_default_display',
					'type'    => 'select',
					'choices' => [
						'flow'   => _x( 'Flow', 'admin', 'wp-surf-theme' ),
						'slider' => _x( 'Slider', 'admin', 'wp-surf-theme' ),
					],
				],
				[
					'key'   => 'field_vacancy_settings_roadmap_icons',
					'label' => _x( 'Roadmap icons', 'admin', 'wp-surf-theme' ),
					'name'  => 'roadmap_default_icons',
					'type'  => 'true_false',
				],
				[
					'key'        => 'field_vacancy_settings_roadmap_steps',
					'label'      => _x( 'Steps', 'admin', 'wp-surf-theme' ),
					'type'       => 'repeater',
					'name'       => 'roadmap_default_steps',
					'sub_fields' => [
						[
							'key'               => 'field_vacancy_settings_roadmap_step_icon',
							'label'             => _x( 'Icon', 'admin', 'wp-surf-theme' ),
							'name'              => 'icon',
							'type'              => 'select',
							'choices'           => RoadmapHelper::listIcons(),
							'conditional_logic' => [
								[
									[
										'field'    => 'field_vacancy_settings_roadmap_icons',
										'operator' => '==',
										'value'    => '1',
									],
								],
							],
						],
						[
							'key'   => 'field_vacancy_settings_roadmap_step_title',
							'label' => _x( 'Title', 'admin', 'wp-surf-theme' ),
							'name'  => 'title',
							'type'  => 'text',
						],
						[
							'key'   => 'field_vacancy_settings_roadmap_step_subtitle',
							'label' => _x( 'Subtitle', 'admin', 'wp-surf-theme' ),
							'name'  => 'subtitle',
							'type'  => 'text',
						],
					],
				],
				[
					'key'       => 'field_vacancy_settings_employment_conditions_tab',
					'label'     => _x( 'Employment conditions settings', 'admin', 'wp-surf-theme' ),
					'type'      => 'tab',
					'placement' => 'left',
				],
				[
					'key'          => 'field_vacancy_settings_employment_conditions',
					'label'        => _x( 'Employment conditions', 'admin', 'wp-surf-theme' ),
					'name'         => 'employment_conditions',
					'type'         => 'wysiwyg',
					'toolbar'      => 'basic',
					'media_upload' => 0,
				],
				[
					'key'          => 'field_vacancy_settings_employment_conditions_disclaimer',
					'label'        => _x( 'Disclaimer', 'admin', 'wp-surf-theme' ),
					'instructions' => _x(
						'This disclaimer will be shown on the vacancy page underneath the employment conditions.',
						'admin',
						'wp-surf-theme'
					),
					'name'         => 'employment_conditions_disclaimer',
					'type'         => 'wysiwyg',
					'toolbar'      => 'basic',
					'media_upload' => 0,
				],
			],
		];
	}

}
