@php
	use SURF\Enums\Theme;

	$widthClass = Theme::isPoweredBy() ? 'span-4-lg' : 'span-6-lg';
	$columns    = [
		'first'  => 'footer-first-column-menu',
		'second' => 'footer-second-column-menu',
	];
	if ( Theme::isPoweredBy() ) {
		$columns['third'] = 'footer-third-column-menu';
	}

@endphp
@foreach ( $columns as $number => $menuLocation)
	<div @class([
		'footer__menu-wrapper column',
		'span-8-sm span-4-md',
		$widthClass,
		'empty' => !has_nav_menu( $menuLocation )
	])>
		@if( has_nav_menu( $menuLocation ) )
			<div class="footer__menu-title h5">{!! wp_get_nav_menu_name( $menuLocation ) !!}</div>
			<nav class="footer-{{ $number }}-column-menu">
				<ul id="footer-{{ $number }}-column-menu" class="footer__menu">
					{{ wp_nav_menu( [
						'theme_location' => $menuLocation,
						'container'      => false,
						'items_wrap'     => '%3$s'
					] ) }}
				</ul>
			</nav>
		@endif
	</div>
@endforeach
