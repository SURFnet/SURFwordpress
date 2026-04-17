@php
	use SURF\ArchiveSURFFaqController;
	use SURF\Core\PostTypes\PostCollection;
	use SURF\Enums\Theme;
	use SURF\Helpers\Helper;
	use SURF\PostTypes\Faq;
	use SURF\PostTypes\Page;
	use SURF\Taxonomies\FaqCategory;
	use SURF\View\ViewModels\SeparatorViewModel;

	/**
	 * @var PostCollection $faqs
	 * @var Page|null $page
	 * @var string $categoryName
	 * @var array $categoryList
	 * @var array $categoryCounts
	 * @var array $sortList
	 */
@endphp

@extends('layouts.app')

@section('content')
	<section id="archive-{{ get_queried_object()->name }}" class="archive archive-page">
		<form
				class="archive__form"
				action="{{ $page?->permalink() ?? get_post_type_archive_link(get_queried_object()->name) }}"
		>
			<div class="container padded">
				<header class="archive-page__header page-header--centered">
					<h1 class="archive-page__title">
						{!! surfGetHeadingIcon('h1') !!}
						{{ $page ? $page->title() : __('Frequently asked questions', 'wp-surf-theme') }}
					</h1>
					@if($page)
						<p>{!! $page->content() !!}</p>
					@endif
					<div class="archive-page__search-form">
						<x-search-filter placeholder="{{ __('Search for an answer', 'wp-surf-theme') }}"/>
						<button type="submit" class="search-submit">
							<span class="sr-only">{{ esc_attr_x( 'Search', 'submit button', 'wp-surf-theme' ) }}</span>
							<x-icon icon="search" sprite="global" class=""/>
						</button>
					</div>

				</header><!-- .archive__header -->
				<div
						class="archive__filters {{ (Theme::isPoweredBy() && SeparatorViewModel::hasGlobalSeparator() ? 'archive__filters--custom-separator' : '' ) }}">
					<div class="archive__filter-item">
						<button class="archive__filter-item-title" type="button">{{ __('Sort', 'wp-surf-theme') }}
							<x-icon icon="chevron-down" sprite="global"/>
						</button>
						<div class="archive__filter-item-list">
							<div class="top-border-left"></div>
							<div class="top-border-right"></div>
							<x-radio-filter name="orderby" :list="$sortList"/>
						</div>
					</div>

					<x-archive-export-button post-type="surf-faq" :taxonomy="FaqCategory::getName()"/>
				</div>
			</div>

			@include('parts.global.separator')

			<div class="grid archive__grid container padded">
				<div
						class="column span-12-lg span-8-md span-4-sm archive__search-header {{ (!empty(Helper::getGet('search')) ? 'show' : '') }}">
					<a class="archive__back"
					   href="{{ get_post_type_archive_link(Faq::getName()) }}">{{ __('Cancel search', 'wp-surf-theme') }}</a>
					<div class="archive__search-title h1">
						{{ __('Search results on', 'wp-surf-theme') }} '<span
								id="search-title">{{ Helper::getSanitizedGet('search') }}</span>'
					</div>
				</div>
				<aside class="column span-4-lg span-3-md span-4-sm">
					<div class="archive__filter">
						<x-child-terms-filter
								name="{{ FaqCategory::getQueryKey() }}"
								taxonomy="{{ FaqCategory::getName() }}"
								:counts="$categoryCounts"
						/>
					</div>
				</aside>
				<div class="column span-8-lg span-5-md span-4-sm">
					<div class="archive__content">
						@include('faq.list-items', ['faqs' => $faqs, 'isSearching' => ArchiveSURFFaqController::isSearching()])
						@if( $faqs->isEmpty() )
							@include('faq.not-found')
						@endif
					</div><!-- .archive__content -->

					<div class="archive__pagination">
						@include( 'parts.pagination' )
					</div><!-- .archive__pagination -->
				</div>
			</div>
		</form>
	</section>

	@if(is_post_type_archive())
		<input type="hidden" name="post_type_archive" value="{{ get_queried_object()->name }}">
	@endif
@endsection
