@php
	use SURF\Core\PostTypes\PostCollection;
	use SURF\Core\PostTypes\BasePost;

	/**
	 * @var PostCollection|BasePost[] $posts
	 */
@endphp

@extends('layouts.app')

@section('content')
	<article id="archive-{{ get_queried_object()->name }}" class="archive archive-page container padded">

		<div class="archive-page__header padded container">
			<h1 class="archive-page__title">
				{!! surfGetHeadingIcon('h1') !!}
				{{ single_term_title() }}
			</h1>
			<div class="archive-page__description">
				{{ the_archive_description() }}
			</div>
		</div>
		@include('parts.global.separator')
		@if($posts->isNotEmpty())
			<div class="archive__content grid">
				@foreach($posts as $post)
					<div class="column span-4-sm span-4-md span-4-lg">
						@include('post.item', ['post' => $post, 'type' => 'block'])
					</div>
				@endforeach
			</div>
		@else
			<div class="archive__content">
				@include('parts.content-none')
			</div>
		@endif

		<div class="archive-page__pagination-container">
			{{ the_posts_pagination(['mid_size' => 2]) }}
		</div>

	</article><!-- #archive-{{ get_queried_object()->name }} -->

	@if(is_post_type_archive())
		<input type="hidden" name="post_type_archive" value="{{ get_queried_object()->name }}">
	@endif

@endsection
