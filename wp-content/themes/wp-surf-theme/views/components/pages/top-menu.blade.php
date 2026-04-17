@php
	use SURF\PostTypes\Page;

	/**
	 * @var Page $page
	 * @var string $location
	 */

	if (!$page->hasTopMenu()) {
		return;
	}

	$topMenuLocation = $page->getTopMenuLocation(); // 'top' or 'left'
	$topMenuAlignment = $page->getTopMenuAlignment(); // 'left' or 'right'

	$location = $location ?? 'top';
	if ($location !== $topMenuLocation) {
		return;
	}

@endphp
<div class="page-top-menu header__primary-navigation">
	<input type="checkbox" id="mobile-top-menu" class="page-top-menu__checkbox">
	<label for="mobile-top-menu" class="page-top-menu__toggle">
		{{ __('Navigate', 'wp-surf-theme') }}
		<div class="hamburger">
			<span class="hamburger__part"></span>
			<span class="hamburger__part"></span>
			<span class="hamburger__part"></span>
		</div>
	</label>
	<ul class="page-top-menu__list header__primary-navigation-menu page-top-menu__list--pos-{{ $topMenuLocation }} page-top-menu__list--align-{{ $topMenuAlignment }}">
		@foreach($page->getTopMenu() as $item)
			<li @class(['page-top-menu__item', ...$item['classes'] ?? []])>
				<a href="{{ $item['url'] }}" target="{{ $item['target'] }}">
					{{ $item['title'] }}
				</a>

				@if(!empty($item['children']))
					<x-pages.top-menu-sub-toggle/>
					<x-pages.top-menu-sub :children="$item['children']"/>
				@endif
			</li>
		@endforeach
	</ul>
</div>
