@php
	use SURF\Core\PostTypes\BasePost;

	/**
	 * @var BasePost $post
	 * @var $relatedPosts
	 */
@endphp

@extends('layouts.app')

@section('content')
	@while(have_posts())
		{{ the_post() }}
		@include('parts.content', compact('post'))
		<div class="container padded">
			@include('components.share')
		</div>
	@endwhile
@endsection

