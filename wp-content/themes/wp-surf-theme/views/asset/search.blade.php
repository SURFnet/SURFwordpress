@php
	use SURF\Core\Taxonomies\TermCollection;
	use SURF\Helpers\Helper;
	use SURF\PostTypes\Page;

	/**
	 * @var Page $page
	 * @var TermCollection $mainCategories
	 * @var array $queries
	 */
@endphp

@extends( 'layouts.app' )

@section( 'content' )
	<div class="container padded assets">
		<x-breadcrumb/>
		<header>
			<x-heading tag="h1" icon="h1">
				{{ $page->title() }}
			</x-heading>
			<div class="assets-navigation">
				<nav>
					<ul>
						<li>
							<a class="{{ $category ? null : 'active' }}" href="{{ $page->permalink() }}"
							>{{ __('Introduction', 'wp-surf-theme') }}</a>
						</li>
						@foreach( $mainCategories as $cat )
							<li>
								<a class="{{ $cat->term_id === $category?->term_id ? 'active' : null }}"
								   href="{{ add_query_arg('category', $cat->slug, $page->permalink()) }}"
								>
									{{ $cat->name }}
								</a>
							</li>
						@endforeach
					</ul>
				</nav>
				@include( 'asset.parts.search-form' )
			</div>
		</header>
		<main>
			<div class="archive__search-title assets__search-title h1">
				{{ __('Search results on', 'wp-surf-theme') }} '<span
						id="search-title">{{ Helper::getSanitizedGet('search') }}</span>'
			</div>

			<div class="assets__search-results">
				@foreach( $queries as $query )
					<div class="assets-item__category-group">
						<x-heading tag="h2" icon="h3" class="h3">
							{{ $query['label'] }}
						</x-heading>
						@if( $query['assets']->isEmpty() )
							@include( 'asset.not-found' )
						@else
							<div class="assets-item__group">
								@foreach( $query['assets'] as $asset )
									@include( 'asset.item', compact('asset') )
								@endforeach
							</div>
						@endempty
						<a href="{{ $query['link'] }}"
						   class="assets__more-link">{{ __('View more', 'wp-surf-theme') }}</a>
					</div>
				@endforeach
			</div>
		</main>
	</div>
@endsection
