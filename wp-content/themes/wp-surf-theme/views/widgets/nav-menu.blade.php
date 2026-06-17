@php
	use SURF\Enums\Theme;

	/**
	 * @var string $title
	 * @var string $menu
	 */

	$menu_obj = wp_get_nav_menu_object($menu);
	$mainColumnWidth = (Theme::isSURF() ? 'span-4-lg' : 'span-3-lg');
@endphp

<div class="column span-4-sm span-4-md {{ $mainColumnWidth }}">
	@if($menu_obj && $menu_obj->count > 0)
		<div class="footer__menu-title h5">{!! wp_get_nav_menu_name($menu) !!}</div>
		<nav class="footer-first-column-menu">
			<ul id="footer-first-column-menu" class="footer__menu">
				{{
					wp_nav_menu([
						// no theme_location here, as the menu is selected in the widget settings
						'menu' => $menu_obj,
						'container' => false,
						'items_wrap' => '%3$s'
					])
				}}
			</ul>
		</nav>
	@endif
</div>
