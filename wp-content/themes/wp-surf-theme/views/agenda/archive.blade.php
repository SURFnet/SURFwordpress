@php
	use SURF\Core\PostTypes\PostCollection;
	use SURF\Enums\Theme;
	use SURF\PostTypes\Agenda;
	use SURF\PostTypes\Page;
	use SURF\View\ViewModels\SeparatorViewModel;

	/**
	 * @var PostCollection|Agenda[] $events
	 * @var Page|null $page
	 * @var string $categoryName
	 * @var array $categoryList
	 * @var array $pastEventsFilters
	 * @var string $widgetAreaPosition
	 * @var string $widgetAreaId
	 * @var string $postItemType
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
		</header><!-- .archive-page__header -->

		@if( $widgetAreaPosition === 'top' )
			<form class="archive__form"
			      action="{{ $page?->permalink() ?? get_post_type_archive_link(get_queried_object()->name) }}">
				<div
						class="archive__filters {{ ( Theme::isPoweredBy() && SeparatorViewModel::hasGlobalSeparator() ? 'archive__filters--custom-separator' : '' ) }}">
					@unless(empty($categoryList))
						<div class="archive__filter-item">
							<button class="archive__filter-item-title"
							        type="button">{{ __('Category', 'wp-surf-theme') }}
								<x-icon icon="chevron-down" sprite="global"/>
							</button>
							<div class="archive__filter-item-list">
								<div class="top-border-left"></div>
								<div class="top-border-right"></div>
								<x-checkbox-filter :name="$categoryName" :list="$categoryList"/>
							</div>
						</div>
					@endunless
					@unless(empty($locationList))
						<div class="archive__filter-item">
							<button class="archive__filter-item-title"
							        type="button">{{ __('Location', 'wp-surf-theme') }}
								<x-icon icon="chevron-down" sprite="global"/>
							</button>
							<div class="archive__filter-item-list">
								<div class="top-border-left"></div>
								<div class="top-border-right"></div>
								<x-checkbox-filter :name="$locationName" :list="$locationList"/>
							</div>
						</div>
					@endunless
					@unless(empty($pastEventsFilters))
						<div class="archive__filter-item">
							<button class="archive__filter-item-title"
							        type="button">{{ __('Upcoming events', 'wp-surf-theme') }}
								<x-icon icon="chevron-down" sprite="global"/>
							</button>
							<div class="archive__filter-item-list">
								<div class="top-border-left"></div>
								<div class="top-border-right"></div>
								<x-checkbox-filter name="past-events" :list="$pastEventsFilters"/>
							</div>
						</div>
					@endunless
				</div>
			</form>
		@endif

		@include('parts.global.separator')
		<div class="grid archive__grid container padded">
			@if($widgetAreaPosition === 'left')
				@include('agenda.archive-aside', ['widgetAreaId' => $widgetAreaId, 'page' => $page, 'pastEventsFilters' => $pastEventsFilters])
			@endif

			<div @class(['column',
            'span-12-lg span-8-md span-4-sm' => in_array($widgetAreaPosition, ['hidden', 'top']),
            'span-8-lg span-5-md span-4-sm' => !in_array($widgetAreaPosition, ['hidden', 'top']),
            ])>
				<div class="archive__content grid">
					@forelse($events as $event)
						<div @class([$columnSpanClass])>
							@include('agenda.item', ['event' => $event, 'type' => $postItemType, 'headingTag' => 'h2'])
						</div>
					@empty
						<div class="column span-4-sm span-8-md span-12-lg">
							@include('agenda.not-found')
						</div>
					@endforelse
				</div><!-- .archive__content -->

				<div class="archive__pagination">
					@include('parts.pagination')
				</div><!-- .archive__pagination -->
			</div>

			@if($widgetAreaPosition === 'right')
				@include('agenda.archive-aside', ['widgetAreaId' => $widgetAreaId, 'page' => $page, 'pastEventsFilters' => $pastEventsFilters])
			@endif
		</div>
	</article><!-- #archive-{{ get_queried_object()->name }} -->

	@if(is_post_type_archive())
		<input type="hidden" name="post_type_archive" value="{{ get_queried_object()->name }}">
	@endif

@endsection
