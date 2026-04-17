@php
	use SURF\PostTypes\Page;

	/**
	 * @var Page $page
	 */
@endphp

@extends('layouts.app')

@section('content')
	@include('parts.content-page', compact('page'))

	@if($page->commentsOpen() || $page->getCommentsNumber())
		{!! $page->commentsTemplate() !!}
	@endif
@endsection
