@php
	use SURF\Core\PostTypes\PostCollection;
	use SURF\Core\PostTypes\BasePost;

	/**
	 * @var WP_Query $query
	 * @var PostCollection|BasePost[] $posts
	 */
@endphp

@extends('layouts.app')

@section('content')
	<x-dev-notice>
		Fallback template index.php is being used. This probably means that you are missing a template file.
		Please refer to the <a target="_blank" href="https://developer.wordpress.org/themes/basics/template-hierarchy/">Template
			Hierarchy</a>
		to find out which template you should be using.
	</x-dev-notice>

	@if(!$posts->isEmpty())
		@foreach($posts as $post)
			@include('parts.content', compact('post'))
		@endforeach

		<div class="container">
			{{ the_posts_navigation() }}
		</div>
	@else
		@include('parts.content-none')
	@endif
@endsection

