@php

	use SURF\Taxonomies\AssetCategory;if ( empty( $menu['items'] ) ) {
		return;
	}

@endphp
<div class="column span-4-lg span-3-md span-4-sm">
	<nav class="assets-navigation assets-navigation--list">
		<h2 class="h5 heading">{{ $menu['heading'] ?? __('Categories', 'wp-surf-theme') }}</h2>
		<input type="checkbox" id="show-sub-menu">
		<label class="assets-navigation__toggle keep asset-sub-menu__toggle" for="show-sub-menu">
			<span class="asset-sub-menu__toggle-icon asset-sub-menu__toggle-icon--closed">
				<x-icon icon="hamburger" sprite="global"/>
			</span>
			<span class="asset-sub-menu__toggle-icon asset-sub-menu__toggle-icon--open" aria-hidden="true">×</span>
			{{ $menu['heading'] ?? __('Categories', 'wp-surf-theme') }}
		</label>
		<div class="assets-navigation__list">
			<div class="items-wrapper">
				@foreach( $menu['items'] as $item )
					@if( !empty( $item['back_link'] ) )
						<div class="item item-up">
							<a href="{{ $item['url'] }}" target="_self" id="{{ $item['slug'] }}"
							   class="item__label">
								{{ $item['title'] }}
							</a>
						</div>
					@else
						<div class="item">
							@if( !empty($item['selected']) )
								<div id="{{ $item['slug'] }}" class="item__label active">
									{{ $item['title'] }}
								</div>
							@else
								<a href="{{ $item['url'] }}" target="_self" id="{{ $item['slug'] }}"
								   class="item__label">
									{{ $item['title'] }}
								</a>
							@endif
						</div>
					@endif
				@endforeach
			</div>
		</div>
	</nav>
</div>