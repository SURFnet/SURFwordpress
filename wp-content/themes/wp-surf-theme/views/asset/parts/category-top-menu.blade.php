@php

	if ( empty( $menu ) ) {
		return;
	}

@endphp
<div class="asset-top-menu">
	<div class="asset-top-menu__bar">
		{{-- Mobile: same structure as subcategories (assets-navigation--list) --}}
		<nav class="assets-navigation assets-navigation--list hidden">
			<h2 class="h5">{{ __('Main categories', 'wp-surf-theme') }}</h2>
			<input type="checkbox" id="mobile-top-menu">
			<label class="assets-navigation__toggle keep asset-top-menu__toggle" for="mobile-top-menu">
				<span class="asset-top-menu__toggle-icon asset-top-menu__toggle-icon--closed">
					<x-icon icon="hamburger" sprite="global"/>
				</span>
				<span class="asset-top-menu__toggle-icon asset-top-menu__toggle-icon--open" aria-hidden="true">×</span>
				{{ __('Main categories', 'wp-surf-theme') }}
			</label>
			<div class="assets-navigation__list">
				<div class="items-wrapper">
					@foreach( $menu as $item )
						<div class="item">
							<a href="{{ $item['url'] }}" class="item__label" target="_self" id="{{ $item['slug'] }}">
								{{ $item['title'] }}
							</a>
						</div>
					@endforeach
				</div>
			</div>
		</nav>

		{{-- Desktop: horizontal scroll + popover --}}
		<div class="asset-top-menu__scroll-wrapper">
			<ul class="asset-top-menu__list">
				@foreach( $menu as $item )
					<li @class(['asset-top-menu__item', ...$item['classes'] ?? []])>
						<a href="{{ $item['url'] }}" target="_self" id="{{ $item['slug'] }}">
							{{ $item['title'] }}
						</a>

						@include( 'asset.parts.category-top-menu-children', ['children' => $item['children'] ?? [], 'slug' => $item['slug']] )
					</li>
				@endforeach
			</ul>
		</div>
		<div class="asset-top-menu__gradient" aria-hidden="true"></div>
		<div class="asset-top-menu__nav">
			<button type="button" class="asset-top-menu__nav-btn asset-top-menu__nav-btn--prev" aria-label="{{ __('Scroll left', 'wp-surf-theme') }}">
				<x-icon icon="arrow-left" sprite="global"/>
			</button>
			<button type="button" class="asset-top-menu__nav-btn asset-top-menu__nav-btn--next" aria-label="{{ __('Scroll right', 'wp-surf-theme') }}">
				<x-icon icon="arrow-right" sprite="global"/>
			</button>
		</div>
	</div>
</div>
