@php
	use SURF\Core\Taxonomies\TermCollection;
	use SURF\PostTypes\Page;
	use SURF\Taxonomies\AssetCategory;
	use SURF\View\ViewModels\AssetsViewModel;

	/**
	 * Asset category page template
	 * @var Page $page
	 * @var AssetCategory $category
	 * @var TermCollection $mainCategories
	 */

	$use_fancy = AssetsViewModel::hasFancyHeader();
	$classes   = [
		'assets-header',
		'assets-header--fancy' => $use_fancy,
		'assets-header--position-top' => $use_fancy && AssetsViewModel::getHeaderContentPosition() === 'top',
		'assets-header--position-bottom' => $use_fancy && AssetsViewModel::getHeaderContentPosition() === 'bottom',
	];

@endphp

@extends( 'layouts.app' )

@section( 'content' )
	<div class="container padded assets">
		<x-breadcrumb/>
		<header @class( $classes )>
			@if( $use_fancy )
				<div class="assets-header__background"
					 style="--assets-gradient:linear-gradient(180deg,rgba(0, 0, 0, {{AssetsViewModel::getHeaderTopFade()}}) 0%, rgba(0, 0, 0, {{AssetsViewModel::getHeaderBottomFade()}}) 100%);">
					{!! wp_get_attachment_image(AssetsViewModel::getHeaderImage(), 'full') !!}
				</div>
				<div class="assets-header__content">
					<x-heading tag="h1" icon="h1">
						{{ AssetsViewModel::getHeaderTitle() }}
					</x-heading>
					{!! apply_filters('the_content', AssetsViewModel::getHeaderDescription()) !!}
				</div>
			@else
				<x-heading tag="h1" icon="h1">
					{{ $page->title() }}
				</x-heading>
			@endif

			<div class="assets-navigation">
				<nav>
					<ul>
						<li>
							<a class="{{ $category ? null : 'active' }}"
							   href="{{ $page->permalink() }}"
							>{{ __('Introduction', 'wp-surf-theme') }}</a>
						</li>
						@foreach( $mainCategories as $cat )
							<li>
								<a class="{{ $cat->term_id === $category?->term_id ? 'active' : null }}"
								   href="{{ add_query_arg('category', $cat->slug, $page->permalink()) }}"
								>{{ $cat->name }}</a>
							</li>
						@endforeach
					</ul>
				</nav>
				@include( 'asset.parts.search-form' )
			</div>
		</header>

		@if( $category )
			<main class="assets__main">
				<div class="assets__header">
					<x-heading tag="h2" icon="h2" class="assets__main-title">
						{{ $category->name }}
					</x-heading>
					<div class="assets__main-introduction">
						{!! apply_filters('the_content', $category->description) !!}
					</div>
				</div>

				<div class="grid assets__grid">
					@foreach( $subCategories as $subCategory )
						<div class="column span-4-sm span-4-md span-4-lg assets-item">
							<h3 class="assets-item__title">
								{{ $subCategory->name }}
								{!! surfGetHeadingIcon('h3') !!}
							</h3>
							<div class="assets-item__intro">
								{!! apply_filters('the_content', $subCategory->description) !!}
							</div>
							<a href="{{ $subCategory->link() }}" class="assets-item__anchor"><span
										class="sr-only">{{ __('View item', 'wp-surf-theme') }}</span></a>
						</div>
					@endforeach
				</div>

				@include( 'asset.pagination', ['current' => $currentPage, 'total' => $totalPages] )
			</main>
		@else
			<main class="assets__main">
				{!! $page->content() !!}
			</main>
		@endif
	</div>
@endsection
