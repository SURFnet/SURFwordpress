@php
	use SURF\Core\PostTypes\BasePost;
	use SURF\Enums\Theme;
	use SURF\Taxonomies\Category;

	/**
	 * @var BasePost $post
	 * @var $relatedPosts
	 */

	$category = $post->getPrimaryTerm( Category::getName() );
	if ( !empty( $category ) ) {
		$catColor = Category::getTermColor( $category->term_id );
		$catName  = $category->name;
		$termUrl  = $category->link();
	} else {
		$catColor = $post->getCategoryColor();
		$catName  = $post->getCategoryName();
		$termUrl  = $post->getCategoryUrl();
	}

@endphp

@extends('layouts.app', ['disableSeparator' => true])

@section('content')
	<article id="post-{{$post->ID()}}"
			 {!! $post->postClass('blog-content container padded entry entry--single' . ($post->getContentOverImage() ? ' content-over-image' : '')) !!} @if(Theme::isSURF() && !empty($catColor))style="--surf-color-category: {{ $catColor }};"@endif>
		@if(has_post_thumbnail() && !$post->shouldHideFeaturedImage())
			<div class="blog-content__figure entry__figure ">{!! $post->postThumbnail('post-image-full') !!}</div>
		@endif

		<div class="blog-content__decoration"></div>

		<div class="blog-content__inner entry__inner container">
			<div class="blog-content__meta">
				@if( !empty($termUrl) && $catName !== __('Uncategorized') )
					<div class="post-category {{ Theme::isPoweredBy() ? 'powered-by-category' : '' }}">
						<x-icon icon="newspaper" sprite="global"/>
						<a href="{{ $termUrl }}">
							{{ $catName }}
						</a>
						@if(Theme::isSURF())
							<svg width="87" height="46" viewBox="0 0 87 46" fill="none"
								 xmlns="http://www.w3.org/2000/svg" class="post-category__decoration">
								<path fill-rule="evenodd" clip-rule="evenodd"
									  d="M6.61205 28.0731C2.95798 28.0731 0 31.1019 0 34.8231V39.15C0 42.8711 2.95798 45.9 6.61205 45.9H16.6169C20.271 45.9 23.229 42.8711 23.229 39.15V36.4673C23.229 31.7942 26.9699 28.0731 31.494 28.0731H78.7349C83.346 28.0731 87 24.3519 87 19.6789V9.29421C87 4.62114 83.259 0.900024 78.7349 0.900024H31.494C26.883 0.900024 23.229 4.62114 23.229 9.29421V19.6789C23.229 24.3519 19.4879 28.0731 14.964 28.0731H6.61205Z"
									  fill="currentColor"/>
							</svg>
						@endif
					</div>
				@endif

				<x-breadcrumb/>

				<x-post-date postId="{{ $post->ID() }}"/>
			</div>

			<h1 class="blog-content__title">
				{!! surfGetHeadingIcon('h1') !!}
				{!! $post->title() !!}
			</h1>

			@if( Theme::tagsLocation() === 'top' )
				<div class="entry__tags tags-list">
					{!! $post->tags(' ') !!}
				</div>
			@endif

			{!! $post->content(sprintf(__('Continue reading %s', 'wp-surf-theme'), "<span class='screen-reader-text'>{$post->title()}</span>")) !!}

			@include('parts.single-contact-persons')

			@if( Theme::tagsLocation() === 'bottom' )
				<div class="entry__tags tags-list">
					{!! $post->tags(' ') !!}
				</div>
			@endif
		</div>
	</article>

	{!! $post->editPostLink(text: _x('Edit', 'admin', 'wp-surf-theme'), class: 'post-edit-link button') !!}

	<div class="container padded">
		@include('components.share')
		@if(comments_open() || get_comments_number())
			{{ comments_template() }}
		@endif
		@include('post.related', compact('post', 'relatedPosts'))
	</div>
@endsection
