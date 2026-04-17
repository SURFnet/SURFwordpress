@php
	/**
	 * @var SURF\View\ViewModels\ErrorPageViewModel $errorView
	 */
@endphp

@extends('layouts.app')

@section('content')
	<article class="entry entry--single">
		<div class="entry__header container padded">
			<h1 class="entry__title">
				{!! surfGetHeadingIcon('h1') !!}
				{!! $errorView->getTitle() !!}
			</h1>
		</div>
		<div class="entry__inner padded container">
			@if(empty($errorView->getContent()))
				<p>{{ __('Something went wrong. What could have happened?', 'wp-surf-theme') }}</p>

				<ul class="surf-block-list">
					<li>{{ __('You clicked on a link with an error in it.', 'wp-surf-theme') }}</li>
					<li>{{ __('We have removed or moved the page.', 'wp-surf-theme') }}</li>
					<li>{{ __('You made a typo.', 'wp-surf-theme') }}</li>
					<li>{{ __('You were looking for an event that has already taken place.', 'wp-surf-theme') }}</li>
				</ul>

				<h4 class="surf-block-heading">
					{!! surfGetHeadingIcon('h4') !!}
					{{ __('And now?', 'wp-surf-theme') }}
				</h4>
				<p>{!! sprintf(__('Use the search function at the top right to find the information. %s Or go to the %s.', 'wp-surf-theme'), '<br>', '<a href="' . esc_url(home_url('/')) . '">' . __('home page', 'wp-surf-theme') . '</a>') !!}</p>
			@else
				{!! apply_filters( 'the_content', $errorView->getContent() ) !!}
			@endif
		</div>
	</article>
@endsection

