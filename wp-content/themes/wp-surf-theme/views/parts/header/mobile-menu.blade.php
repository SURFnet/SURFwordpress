@php
	use SURF\Components\WalkerPrimaryMenu;

@endphp
<nav class="navigation-mobile" aria-expanded="false" aria-disabled="true">
	@if(has_nav_menu('primary-menu'))
		<ul id="mobile-menu" class="navigation-mobile__menu">
			{{
				wp_nav_menu([
					'theme_location' => 'primary-menu',
					'container' => false,
					'items_wrap' => '%3$s',
					'walker'     => new WalkerPrimaryMenu()
				])
			}}
		</ul>
	@endif
	@if(has_nav_menu('top-menu'))
		<ul id="mobile-menu-bottom" class="navigation-mobile__submenu">
			{{
				wp_nav_menu([
					'theme_location' => 'top-menu',
					'container' => false,
					'items_wrap' => '%3$s'
				])
			}}
		</ul>
	@endif
	<x-language-switch dropdown="false"/>
</nav>
