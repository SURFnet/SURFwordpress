@php
	use SURF\Core\PostTypes\PostCollection;
	use SURF\Enums\Theme;
	use SURF\PostTypes\Download;
	use SURF\PostTypes\Page;
	use SURF\View\ViewModels\SeparatorViewModel;

	/**
	 * @var PostCollection|Download[] $downloads
	 * @var Page|null                 $page
	 * @var string                    $categoryName
	 * @var array                     $categoryList
	 * @var string                    $widgetAreaPosition
	 * @var string                    $widgetAreaId
	 */

@endphp

@extends('layouts.app')

@section('content')
	<article id="archive-{{ get_queried_object()->name }}" class="archive archive-page container padded">
		<header class="archive-page__header">
			<h1 class="archive-page__title">
				{!! surfGetHeadingIcon('h1') !!}
				{!! $page ? $page->title() : get_the_archive_title() !!}
			</h1>
			<div class="archive-page__description">
				{!! $page ? $page->content() : get_the_archive_description() !!}
			</div>
		</header><!-- .archive__header -->

		@if( $widgetAreaPosition === 'top' )
			<form class="archive__form"
			      action="{{ $page?->permalink() ?? get_post_type_archive_link(get_queried_object()->name) }}">
				<div
						class="archive__filters {{ (Theme::isPoweredBy() && SeparatorViewModel::hasGlobalSeparator() ? 'archive__filters--custom-separator' : '' ) }}">
					<div class="archive__filter-item">
						<button class="archive__filter-item-title" type="button">{{ __('Category', 'wp-surf-theme') }}
							<x-icon icon="chevron-down" sprite="global"/>
						</button>
						<div class="archive__filter-item-list">
							<div class="top-border-left"></div>
							<div class="top-border-right"></div>
							<x-checkbox-filter :name="$categoryName" :list="$categoryList"/>
						</div>
					</div>
				</div>
			</form>
		@endif

		@include('parts.global.separator')
		<div class="grid archive__grid container padded">
			@if(is_active_sidebar($widgetAreaId) && $widgetAreaPosition === 'left')
				<aside class="column span-4-lg span-3-md span-4-sm">
					<form class="archive__form"
					      action="{{ $page?->permalink() ?? get_post_type_archive_link(get_queried_object()->name) }}">
						<div class="archive__widget-area">
							@php(dynamic_sidebar($widgetAreaId))
						</div>
					</form>
				</aside>
			@endif

			<div @class(['column',
                'span-12-lg span-8-md span-4-sm' => in_array($widgetAreaPosition, ['hidden', 'top']),
                'span-8-lg span-5-md span-4-sm' => !in_array($widgetAreaPosition, ['hidden', 'top']),
                ])>
				<div class="archive__content grid">
					@forelse($downloads as $download)
						<div @class([$columnSpanClass])>
							@include('download.item', ['download' => $download, 'type' => $postItemType, 'headingTag' => 'h2'])
						</div>
					@empty
						<div class="column span-4-sm span-8-md span-12-lg">
							@include('download.not-found')
						</div>
					@endforelse
				</div><!-- .archive__content -->

				<div class="archive__pagination">
					@include('parts.pagination')
				</div><!-- .archive__pagination -->
			</div>

			@if(is_active_sidebar($widgetAreaId) && $widgetAreaPosition === 'right')
				<aside class="column span-4-lg span-3-md span-4-sm">
					<form class="archive__form"
					      action="{{ $page?->permalink() ?? get_post_type_archive_link(get_queried_object()->name) }}">
						<div class="archive__widget-area">
							@php(dynamic_sidebar($widgetAreaId))
						</div>
					</form>
				</aside>
			@endif
		</div>
	</article>
@endsection
