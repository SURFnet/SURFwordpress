<?php

namespace SURF\Admin\MetaBoxes;

use SURF\Admin\MetaBoxes;
use SURF\Admin\Pages;
use SURF\Enums\Theme;
use SURF\Helpers\ACFHelper;
use SURF\Helpers\CspHelper;
use SURF\Services\CspService;

/**
 * Class CspSettings
 * ACF settings page for the site rules: per-site additions to and replacements of
 * the theme's Content-Security-Policy. Also reads and writes the stored rules,
 * from the raw option values, so the policy does not depend on ACF being loaded.
 * @package SURF\Admin\MetaBoxes
 */
class CspSettings
{

	public const FIELD_PREFIX = 'surf_csp_';
	public const SUB_FIELDS   = [ 'directive', 'action', 'sources' ];

	/**
	 * @return void
	 */
	public static function init(): void
	{
		if ( !ACFHelper::allowsRepeater() ) {
			return;
		}

		MetaBoxes::register( [
			'title'    => _x( 'Content Security Policy', 'admin', 'wp-surf-theme' ),
			'key'      => 'group_surf_csp_settings',
			'location' => static::getLocation(),
			'fields'   => static::getFields(),
		] );

		add_action( 'acf/input/admin_head', [ static::class, 'printStyles' ] );
		add_filter( 'acf/prepare_field/key=field_surf_csp_legacy_notice', [ static::class, 'prepareLegacyNotice' ] );
		foreach ( CspHelper::getContexts() as $context ) {
			add_filter( 'acf/prepare_field/key=' . static::getFieldKey( $context, 'preview' ), [
				static::class,
				'preparePreview',
			] );
			add_filter( 'acf/validate_value/key=' . static::getFieldKey( $context, 'sources' ), [
				static::class,
				'validateSources',
			], 10, 2 );
		}
	}

	/**
	 * @return array[]
	 */
	public static function getLocation(): array
	{
		return [
			[
				[
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => Pages::SLUG_CSP_SETTINGS,
				],
			],
		];
	}

	/**
	 * @return array[]
	 */
	public static function getFields(): array
	{
		$fields = [
			[
				'key'       => 'field_surf_csp_legacy_notice',
				'label'     => _x( 'CSP Manager plugin', 'admin', 'wp-surf-theme' ),
				'name'      => '',
				'type'      => 'message',
				'message'   => _x( 'The CSP Manager plugin is active, so the theme does not send its own policy. Deactivate the plugin to use the policy below.', 'admin', 'wp-surf-theme' ),
				'new_lines' => '',
			],
			[
				'key'       => 'field_surf_csp_explanation',
				'label'     => _x( 'How this works', 'admin', 'wp-surf-theme' ),
				'name'      => '',
				'type'      => 'message',
				'message'   => static::getExplanation(),
				'new_lines' => '',
				'esc_html'  => 0,
			],
		];

		foreach ( CspHelper::getContexts() as $context ) {
			$fields = array_merge( $fields, static::getContextFields( $context ) );
		}

		return $fields;
	}

	/**
	 * @param string $context
	 * @return array[]
	 */
	public static function getContextFields( string $context ): array
	{
		$label = _x( 'Front', 'admin', 'wp-surf-theme' );
		if ( $context === CspHelper::CONTEXT_ADMIN ) {
			$label = _x( 'Admin', 'admin', 'wp-surf-theme' );
		}

		return [
			[
				'key'       => static::getFieldKey( $context, 'tab' ),
				'label'     => $label,
				'name'      => '',
				'placement' => 'top',
				'type'      => 'tab',
			],
			[
				'key'          => static::getFieldKey( $context ),
				'label'        => sprintf( _x( 'Rules for %s', 'admin', 'wp-surf-theme' ), $label ),
				'name'         => static::getFieldName( $context ),
				'type'         => 'repeater',
				'instructions' => _x( 'Add sources to a directive of the theme policy, or replace its sources entirely. Replacing without sources removes the directive.', 'admin', 'wp-surf-theme' ),
				'layout'       => 'table',
				'button_label' => _x( 'Add rule', 'admin', 'wp-surf-theme' ),
				'sub_fields'   => [
					[
						'key'      => static::getFieldKey( $context, 'directive' ),
						'label'    => _x( 'Directive', 'admin', 'wp-surf-theme' ),
						'name'     => 'directive',
						'type'     => 'select',
						'required' => 1,
						'choices'  => array_combine( CspHelper::DIRECTIVES, CspHelper::DIRECTIVES ),
						'wrapper'  => [ 'width' => '25' ],
					],
					[
						'key'           => static::getFieldKey( $context, 'action' ),
						'label'         => _x( 'Action', 'admin', 'wp-surf-theme' ),
						'name'          => 'action',
						'type'          => 'select',
						'required'      => 1,
						'default_value' => CspHelper::ACTION_ADD,
						'choices'       => [
							CspHelper::ACTION_ADD     => _x( 'Add', 'admin', 'wp-surf-theme' ),
							CspHelper::ACTION_REPLACE => _x( 'Replace', 'admin', 'wp-surf-theme' ),
						],
						'wrapper'       => [ 'width' => '15' ],
					],
					[
						'key'       => static::getFieldKey( $context, 'sources' ),
						'label'     => _x( 'Sources', 'admin', 'wp-surf-theme' ),
						'name'      => 'sources',
						'type'      => 'textarea',
						'rows'      => 3,
						'new_lines' => '',
					],
				],
			],
			[
				'key'       => static::getFieldKey( $context, 'hint' ),
				'label'     => '',
				'name'      => '',
				'type'      => 'message',
				'message'   => static::getSourcesHint(),
				'new_lines' => '',
				'esc_html'  => 0,
			],
			[
				'key'         => static::getFieldKey( $context, 'preview' ),
				'label'       => _x( 'Resulting header', 'admin', 'wp-surf-theme' ),
				'name'        => '',
				'type'        => 'message',
				'message'     => '',
				'new_lines'   => '',
				'esc_html'    => 0,
				'csp_context' => $context,
			],
		];
	}

	/**
	 * @return string
	 */
	public static function getExplanation(): string
	{
		$paragraphs = [
			_x( 'The theme sends a default Content Security Policy (CSP) to every visitor. It tells the browser from which domains scripts, styles, images, embeds and other content may be loaded; anything else is blocked. The default policy is maintained in the theme and is updated together with it.', 'admin', 'wp-surf-theme' ),
			_x( 'Only when this site needs something the default policy does not allow, for example an embed from another domain, add a rule below. Rules apply to the tab they are in: "Front" for the website and the login page, "Admin" for the WordPress dashboard.', 'admin', 'wp-surf-theme' ),
		];

		$actions = [
			_x( '<strong>Add</strong> keeps the sources of the default policy and adds the sources of the rule. This is what you need in most cases.', 'admin', 'wp-surf-theme' ),
			_x( '<strong>Replace</strong> discards the sources of the default policy for that directive and uses only the sources of the rule. Replacing without sources removes the directive from the policy.', 'admin', 'wp-surf-theme' ),
		];

		$html = '';
		foreach ( $paragraphs as $paragraph ) {
			$html .= '<p>' . esc_html( $paragraph ) . '</p>';
		}

		$html .= '<ul style="list-style: disc; padding-left: 1.5em;">';
		foreach ( $actions as $action ) {
			$html .= '<li>' . wp_kses( $action, [ 'strong' => [] ] ) . '</li>';
		}
		$html .= '</ul>';

		$html .= '<p>' . esc_html_x( 'The resulting policy, including the rules of this site, is shown at the bottom of each tab after saving.', 'admin', 'wp-surf-theme' ) . '</p>';

		return $html;
	}

	/**
	 * @return string
	 */
	public static function getSourcesHint(): string
	{
		return '<p class="description">' . esc_html_x( 'Sources: one per line, for example player.example.com, *.example.com or https://example.com. Keywords such as \'self\' and \'unsafe-inline\' need the single quotes.', 'admin', 'wp-surf-theme' ) . '</p>';
	}

	/**
	 * Hides the sources hint while a repeater has no rows. ACF toggles the
	 * -empty class on the repeater when rows are added or removed.
	 * @return void
	 */
	public static function printStyles(): void
	{
		$screen = get_current_screen();
		if ( !$screen || !str_ends_with( $screen->id, '_page_' . Pages::SLUG_CSP_SETTINGS ) ) {
			return;
		}

		$selectors = [];
		foreach ( CspHelper::getContexts() as $context ) {
			$selectors[] = sprintf(
				'.acf-field[data-key="%s"]:has(.acf-repeater.-empty) + .acf-field[data-key="%s"]',
				esc_attr( static::getFieldKey( $context ) ),
				esc_attr( static::getFieldKey( $context, 'hint' ) )
			);
		}

		echo '<style>' . implode( ', ', $selectors ) . ' { display: none; }</style>';
	}

	/**
	 * @param array $field
	 * @return array|false
	 */
	public static function prepareLegacyNotice( array $field ): array|false
	{
		if ( !CspService::isLegacyPluginActive() ) {
			return false;
		}

		return $field;
	}

	/**
	 * Shows the saved policy; changes are visible after saving.
	 * @param array $field
	 * @return array
	 */
	public static function preparePreview( array $field ): array
	{
		$context = (string) ( $field['csp_context'] ?? '' );
		if ( !in_array( $context, CspHelper::getContexts(), true ) ) {
			return $field;
		}

		$name = CspHelper::getHeaderName( $context );

		if ( $name === null ) {
			$field['message'] = esc_html_x( 'Disabled', 'admin', 'wp-surf-theme' );

			return $field;
		}

		$field['message'] = sprintf(
			'<p><code>%s</code></p><pre style="white-space: pre-wrap;">%s</pre>',
			esc_html( $name ),
			esc_html( CspHelper::formatDirectives( CspService::getDirectives( $context ), ";\n" ) )
		);

		$field['message'] .= sprintf(
			'<details><summary>%s</summary><pre style="white-space: pre-wrap;">%s</pre></details>',
			esc_html_x( 'Show the default theme policy', 'admin', 'wp-surf-theme' ),
			esc_html( CspHelper::formatDirectives( CspService::getThemeDirectives( $context ), ";\n" ) )
		);

		return $field;
	}

	/**
	 * @param bool|string $valid
	 * @param mixed $value
	 * @return bool|string
	 */
	public static function validateSources( bool|string $valid, mixed $value ): bool|string
	{
		if ( $valid !== true ) {
			return $valid;
		}

		foreach ( CspHelper::splitSources( (string) $value ) as $source ) {
			if ( !CspHelper::isValidSource( $source ) ) {
				return sprintf( _x( 'Invalid source "%s".', 'admin', 'wp-surf-theme' ), $source );
			}
		}

		return true;
	}

	/**
	 * Name of the ACF repeater that holds the site rules for a context.
	 * @param string $context
	 * @return string
	 */
	public static function getFieldName( string $context ): string
	{
		return static::FIELD_PREFIX . $context;
	}

	/**
	 * ACF field key of the rules repeater, or of one of its sub fields or the
	 * other fields of that context (tab, hint, preview).
	 * @param string $context
	 * @param string $subField
	 * @return string
	 */
	public static function getFieldKey( string $context, string $subField = '' ): string
	{
		$key = 'field_' . static::getFieldName( $context );
		if ( $subField !== '' ) {
			$key .= '_' . $subField;
		}

		return $key;
	}

	/**
	 * The stored site rules for a context, grouped by action. Rows with an
	 * unknown directive or action are ignored.
	 * @param string $context
	 * @return array{add: array<string, string[]>, replace: array<string, string[]>}
	 */
	public static function getRules( string $context ): array
	{
		$rules = [
			CspHelper::ACTION_ADD     => [],
			CspHelper::ACTION_REPLACE => [],
		];

		$rows = Theme::getGlobalRepeaterOption( static::getFieldName( $context ), static::SUB_FIELDS );
		foreach ( $rows as $row ) {
			$directive = (string) $row['directive'] ?? '';
			$action    = (string) $row['action'] ?? '';
			if ( !in_array( $directive, CspHelper::DIRECTIVES, true ) || !array_key_exists( $action, $rules ) ) {
				continue;
			}

			$current                        = $rules[ $action ][ $directive ] ?? [];
			$rules[ $action ][ $directive ] = array_merge( $current, CspHelper::splitSources( (string) $row['sources'] ) );
		}

		return $rules;
	}

	/**
	 * Appends rows to the rules repeater by writing the option values the way
	 * ACF stores them, so this also works when ACF is not loaded.
	 * @param string $context
	 * @param string $action
	 * @param array<string, string[]> $directives
	 * @return void
	 */
	public static function appendRules( string $context, string $action, array $directives ): void
	{
		$name  = static::getFieldName( $context );
		$count = (int) get_option( 'options_' . $name, 0 );

		foreach ( $directives as $directive => $sources ) {
			$values = [
				'directive' => $directive,
				'action'    => $action,
				'sources'   => implode( "\n", $sources ),
			];

			foreach ( $values as $subField => $value ) {
				$option = 'options_' . $name . '_' . $count . '_' . $subField;
				update_option( $option, $value );
			}

			$count++;
		}

		update_option( 'options_' . $name, $count );
	}

}
