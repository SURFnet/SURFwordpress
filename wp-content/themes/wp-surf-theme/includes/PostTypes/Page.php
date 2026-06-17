<?php

namespace SURF\PostTypes;

use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\HasFactory;
use SURF\Core\Traits\HasFields;
use SURF\Helpers\ACFHelper;
use SURF\Helpers\NavHelper;

/**
 * Class Page
 * @package SURF\PostTypes
 */
class Page extends BasePost
{

	use HasFactory, HasFields;

	protected static string $postType = 'page';

	/**
	 * @return string
	 */
	public function getHeroSize(): string
	{
		return $this->getMeta( 'hero_size' );
	}

	/**
	 * @return bool
	 */
	public function hideTitle(): bool
	{
		return (bool) $this->getMeta( 'hide_title' );
	}

	/**
	 * @return bool
	 */
	public function hideBreadcrumbs(): bool
	{
		return (bool) $this->getMeta( 'hide_breadcrumbs' );
	}

	/**
	 * @return bool
	 */
	public function showDate(): bool
	{
		return (bool) $this->getMeta( 'show_date' );
	}

	/**
	 * @return bool
	 */
	public function fullWidthTitle(): bool
	{
		return (bool) $this->getMeta( 'full_width_title' );
	}

	/**
	 * @return array
	 */
	public function getTopMenu(): array
	{
		switch ( $this->getMeta( 'top_menu_type', 'disabled' ) ) {
			case 'existing':
				$menuId = $this->getMeta( 'top_menu_existing' );
				if ( !empty( $menuId ) ) {
					return NavHelper::navToArray( $menuId );
				}
				break;

			case 'custom':
				$meta = $this->getRepeaterMeta( 'top_menu_custom', [
					'item_link' => [],
				] );
				if ( empty( $meta ) ) {
					return [];
				}

				foreach ( $meta as $row => $values ) {
					$link = $values['item_link'];
					if ( empty( $link['url'] ) ) {
						unset( $meta[ $row ] );
						continue;
					}

					$meta[ $row ] = [
						'title'  => $link['title'],
						'url'    => $link['url'],
						'target' => !empty( $link['target'] ) ? $link['target'] : '_self',
					];
				}

				return $meta;
		}

		return [];
	}

	/**
	 * @return bool
	 */
	public function hasTopMenu(): bool
	{
		return !empty( $this->getTopMenu() );
	}

	/**
	 * @return string
	 */
	public function getTopMenuLocation(): string
	{
		return (string) $this->getMeta( 'top_menu_location' ) ?: 'top';
	}

	/**
	 * @return string
	 */
	public function getTopMenuAlignment(): string
	{
		return (string) $this->getMeta( 'top_menu_alignment' ) ?: 'left';
	}

	/**
	 * @return array
	 */
	public static function getFields(): array
	{
		$customSettings = [];
		$menuTypes      = [
			'disabled' => _x( 'Disabled', 'admin', 'wp-surf-theme' ),
			'existing' => _x( 'Existing', 'admin', 'wp-surf-theme' ),
		];
		if ( ACFHelper::allowsRepeater() ) {
			$menuTypes['custom'] = _x( 'Custom', 'admin', 'wp-surf-theme' );
			$customSettings      = [
				'key'               => 'field_top_menu_custom',
				'label'             => _x( 'Set up custom menu', 'admin', 'wp-surf-theme' ),
				'name'              => 'top_menu_custom',
				'type'              => 'repeater',
				'layout'            => 'block',
				'button_label'      => _x( 'Add menu item', 'admin', 'wp-surf-theme' ),
				'sub_fields'        => [
					[
						'key'      => 'field_top_menu_custom_item_link',
						'label'    => _x( 'Item link', 'admin', 'wp-surf-theme' ),
						'name'     => 'item_link',
						'type'     => 'link',
						'required' => true,
					],
				],
				'conditional_logic' => [
					[
						[
							'field'    => 'field_top_menu_type',
							'operator' => '==',
							'value'    => 'custom',
						],
					],
				],
			];
		}

		return [
			[
				'key'    => 'group_page_settings',
				'title'  => _x( 'Settings', 'admin', 'wp-surf-theme' ),
				'fields' => [
					[
						'key'          => 'field_full_width_title',
						'label'        => _x( 'Title - Full width', 'admin', 'wp-surf-theme' ),
						'instructions' => _x( 'Show the title as full width and not centered in grid.', 'admin', 'wp-surf-theme' ),
						'name'         => 'full_width_title',
						'type'         => 'true_false',
						'ui'           => true,
						'wrapper'      => [
							'width' => 50,
						],
					],
					[
						'key'          => 'field_hide_title',
						'label'        => _x( 'Hide title', 'admin', 'wp-surf-theme' ),
						'instructions' => _x( 'Hide the title on this page.', 'admin', 'wp-surf-theme' ),
						'name'         => 'hide_title',
						'type'         => 'true_false',
						'ui'           => true,
						'wrapper'      => [
							'width' => 50,
						],
					],
					[
						'key'          => 'field_hide_breadcrumbs',
						'label'        => _x( 'Hide breadcrumbs', 'admin', 'wp-surf-theme' ),
						'instructions' => _x( 'Hide the breadcrumbs on this page.', 'admin', 'wp-surf-theme' ),
						'name'         => 'hide_breadcrumbs',
						'type'         => 'true_false',
						'ui'           => true,
						'wrapper'      => [
							'width' => 50,
						],
					],
					[
						'key'          => 'field_show_date',
						'label'        => _x( 'Show date', 'admin', 'wp-surf-theme' ),
						'instructions' => _x( 'Show the page date.', 'admin', 'wp-surf-theme' ),
						'name'         => 'show_date',
						'type'         => 'true_false',
						'ui'           => true,
						'wrapper'      => [
							'width' => 50,
						],
					],
					[
						'key'           => 'field_top_menu_type',
						'label'         => _x( 'Top menu', 'admin', 'wp-surf-theme' ),
						'name'          => 'top_menu_type',
						'type'          => 'select',
						'default_value' => 'disabled',
						'choices'       => $menuTypes,
					],
					[
						'key'               => 'field_top_menu_existing',
						'label'             => _x( 'Select existing menu', 'admin', 'wp-surf-theme' ),
						'name'              => 'top_menu_existing',
						'type'              => 'taxonomy',
						'taxonomy'          => 'nav_menu',
						'field_type'        => 'select',
						'return_format'     => 'id',
						'conditional_logic' => [
							[
								[
									'field'    => 'field_top_menu_type',
									'operator' => '==',
									'value'    => 'existing',
								],
							],
						],
					],
					$customSettings,
					[
						'key'               => 'field_top_menu_location',
						'label'             => _x( 'Top menu location', 'admin', 'wp-surf-theme' ),
						'name'              => 'top_menu_location',
						'type'              => 'radio',
						'default_value'     => 'top',
						'choices'           => [
							'top'  => _x( 'Top', 'admin', 'wp-surf-theme' ),
							'left' => _x( 'Left', 'admin', 'wp-surf-theme' ),
						],
						'wrapper'           => [
							'width' => 50,
						],
						'conditional_logic' => [
							[
								[
									'field'    => 'field_top_menu_type',
									'operator' => '!=',
									'value'    => 'disabled',
								],
							],
						],
					],
					[
						'key'               => 'field_top_menu_align_right',
						'label'             => _x( 'Top menu alignment', 'admin', 'wp-surf-theme' ),
						'name'              => 'top_menu_alignment',
						'type'              => 'radio',
						'default_value'     => 'left',
						'choices'           => [
							'left'  => _x( 'Left', 'admin', 'wp-surf-theme' ),
							'right' => _x( 'Right', 'admin', 'wp-surf-theme' ),
						],
						'wrapper'           => [
							'width' => 50,
						],
						'conditional_logic' => [
							[
								[
									'field'    => 'field_top_menu_type',
									'operator' => '!=',
									'value'    => 'disabled',
								],
							],
						],
					],
				],
			],
		];
	}

}
