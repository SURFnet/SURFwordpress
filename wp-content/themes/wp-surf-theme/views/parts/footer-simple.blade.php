@php
	$menu_location = 'footer-menu';

@endphp
<section class="footer__sub--simple">
	<div class="container padded">
		<nav class="footer__sub__menu">
			@if( has_nav_menu( $menu_location ) )
				<ul id="footer-menu" class="footer__sub-navigation">
					{{ wp_nav_menu( [
						'theme_location' => $menu_location,
						'container'      => false,
						'items_wrap'     => '%3$s'
					] ) }}
				</ul>
			@endif
		</nav>
		<x-language-switch-simple class="footer__language-switch"/>
		<div>
			@include('parts.footer.copyright')
		</div>
	</div>
</section>
