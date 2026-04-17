@php
	use SURF\PostTypes\Page;

	/**
	 * @var Page $page
	 */
@endphp

@extends('layouts.app')

@section('content')
	@include('parts.content-page', compact('page'))
@endsection

