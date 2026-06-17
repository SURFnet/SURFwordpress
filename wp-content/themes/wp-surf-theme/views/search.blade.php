@php
	use SURF\Core\PostTypes\PostCollection;
	use SURF\Helpers\PolylangHelper;

	/**
	 * @var WP_Query $query
	 * @var PostCollection $posts
	 * @var string $postTypeName
	 * @var array $postTypeList
	 * @var false|string $selectedPostType
	 * @var array $taxonomies
	 * @var bool $filterSearch
	 * @var string $typeLabel
	 * @var string $widgetAreaPosition
	 * @var string $widgetAreaId
	 * @var string $columnSpanClass
	 * @var string $postItemType
	 */
@endphp

@extends('layouts.app')

@section('content')
	<article data-archive-loaded="true" data-archive="search" id="archive-search"
	         class="archive archive-page container padded">

		<header class="archive-page__header">
			<h1 class="archive-page__title">
				{!! surfGetHeadingIcon('h1') !!}

				{{ PolylangHelper::getThemeOption("search_page_title") ?: __('Search results', 'wp-surf-theme')}}
			</h1>
			<div class="archive-page__description">
				{!! sprintf(__('We found %s on the searchterm %s', 'wp-surf-theme'), '<strong>' . ($query->found_posts == 0 ? __('no results', 'wp-surf-theme') : ($query->found_posts == 1 ? $query->found_posts . __(' result', 'wp-surf-theme') : $query->found_posts . __(' results', 'wp-surf-theme'))) . '</strong>', '\'<strong>' . get_search_query()) . '</strong>\'' !!}
			</div>

			@if(in_array($widgetAreaPosition, ['hidden', 'top']))
				{{ get_search_form() }}
			@endif
		</header>

		@include('parts.global.separator')
		<section class="search__grid grid container padded">
			@if( $widgetAreaPosition === 'top' )
				<div class="column span-4-lg span-3-md span-4-sm">
					@if( $postTypes = get_post_types( ['exclude_from_search' => false], 'objects' ) )
						@php
							$id = uniqid()
						@endphp
						<form method="get" action="{{ home_url('/') }}" class="search-filter">
							<input type="hidden" value="{{ get_search_query() }}" name="s">
							<div class="search-filter__group" data-accordion-item>
								<div class="h4 search-filter__group__heading"
								     aria-expanded="true"
								     aria-label="{{ __('Toggle', 'wp-surf-theme') }} {{ __('Type') }}"
								     data-accordion-button>
									{{ __('Type', 'wp-surf-theme') }}
									<x-icon icon="chevron-down" sprite="global"/>
								</div>

								<ul class="search-filter__group__list"
								    id="search-type-{{ $id }}"
								    data-accordion-target>
									@foreach($postTypes as $postType)
										<input name="post_type" onchange="this.form.submit()" type="radio"
										       value="{{$postType->name}}"
										       id="{{$postType->name}}" @checked($selectedPostType === $postType->name)>
										<label for="{{$postType->name}}">
											{{ PolylangHelper::getThemeOption("search_name_$postType->name") ?: $postType->label }}
										</label>
									@endforeach
								</ul>
							</div>

							@if($taxonomies)
								@foreach($taxonomies as $taxonomy)
									@php
										$id = uniqid()
									@endphp

									<div class="search-filter__group" data-accordion-item>
										<div class="h4 search-filter__group__heading"
										     aria-expanded="true"
										     aria-label="{{ __('Toggle', 'wp-surf-theme') }} {{ $taxonomy->label }}"
										     data-accordion-button>
											{{ $taxonomy->label }}
											<x-icon icon="chevron-down" sprite="global"/>
										</div>
										<ul class="search-filter__group__list"
										    id="search-category-{{ $id }}"
										    data-accordion-target>
											@foreach($terms as $term )
												@include('search.category-input', ['term' => $term, 'taxonomy' => $taxonomy])
											@endforeach
										</ul>
									</div>
								@endforeach
							@endif
						</form>
					@endif
				</div>
			@endif

			@if($widgetAreaPosition === 'left')
				<aside class="column span-4-lg span-2-md span-4-sm">
					<form class="archive__form"
					      action="{{ home_url( '/' ) }}">

						<div class="archive__widget-area">
							<x-search-filter class="archive__widget-area__search"/>
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
					@forelse($posts as $post)
						<div @class([$columnSpanClass])>
							@include('search.item', ['post' => $post, 'type' => $postItemType])
						</div>
					@empty
						<div class="column span-4-sm span-8-md span-12-lg">
							@include('search.not-found')
						</div>
					@endforelse
					{{--                    @each('search.item', $posts, 'post', 'search.not-found')--}}

				</div>
				<div class="archive-page__pagination-container archive__pagination">
					{{ the_posts_pagination(['mid_size' => 2]) }}
				</div>
			</div>

			@if($widgetAreaPosition === 'right')
				<aside class="column span-4-lg span-3-md span-4-sm">
					<form class="archive__form"
					      action="{{ home_url( '/' ) }}">

						<div class="archive__widget-area">
							<x-search-filter/>
							@php(dynamic_sidebar($widgetAreaId))
						</div>
					</form>
				</aside>
			@endif
		</section>
	</article>

@endsection
