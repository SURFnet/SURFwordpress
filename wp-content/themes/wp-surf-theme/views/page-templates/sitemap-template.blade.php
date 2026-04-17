@php
	use SURF\PostTypes\Asset;
	use SURF\PostTypes\Page;
	use SURF\Services\PostTypeSitemapService;

	/**
	 * Sitemap page template
	 * @var Page $page
	 */

	$level = 0;

@endphp

@extends( 'layouts.app' )

@section( 'content' )
	<div class="container padded medium-narrow">
		<x-breadcrumb/>
		<header class="sitemap__header">
			<x-heading tag="h1" icon="h1">
				{{ $page->title() }}
			</x-heading>
			<div class="sitemap__description">
				{!! $page->content() !!}
			</div>
		</header>

		@include( 'parts.global.separator' )

		<main class="sitemap__main">
			<x-sitemap.list
					:title="__('Content', 'wp-surf-theme')"
					:sitemap="($sitemap ?? [])"
			/>
		</main>
	</div>
@endsection
