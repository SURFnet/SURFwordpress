@php
	use SURF\Core\PostTypes\PostCollection;
	use SURF\PostTypes\Agenda;

	/**
	 * @var PostCollection|Agenda[] $events
	 */

@endphp

@extends('layouts.app')

@section('content')
	<article id="archive-{{ get_queried_object()->name }}" class="archive archive-page container padded">

		<div class="archive-page__header">
			<h1 class="archive-page__title">
				{!! surfGetHeadingIcon('h1') !!}
				{{ single_term_title() }}
			</h1>
			<div class="archive-page__description">
				{{ the_archive_description() }}
			</div>
		</div>

		@include('parts.global.separator')

		<div class="archive__content grid">
			@forelse($events as $event)
				<div class="column span-4-sm span-8-md span-6-lg">
					@include('agenda.item', ['event' => $event, 'type' => 'row'])
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

	</article><!-- #archive-{{ get_queried_object()->name }} -->

@endsection
