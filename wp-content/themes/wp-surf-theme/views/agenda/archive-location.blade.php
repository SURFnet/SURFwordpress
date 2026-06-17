@php
	use SURF\Core\PostTypes\PostCollection;
	use SURF\PostTypes\Agenda;
	use SURF\Taxonomies\AgendaLocation;

	/**
	 * @var PostCollection|Agenda[] $events
	 */

	$location = AgendaLocation::getPrimaryLocationInfoArchive();

@endphp

@extends('layouts.app')

@section('content')
	<article id="archive-{{ get_queried_object()->name }}" class="archive archive-page container padded">

		<div class="archive-page__header">
			<h1 class="archive-page__title">
				{!! surfGetHeadingIcon('h1') !!}
				{{ single_term_title() }}
			</h1>
			<div class="grid">
				<div class="column span-4-sm span-8-md span-6-lg">
					<div class="archive-page__description">
						{{ the_archive_description() }}
					</div>
				</div>
				<div class="column span-4-sm span-8-md span-6-lg">
					<div class="location location--archive">
						<div class="location__figure">
							@if(!empty($location['image']))
								{!! wp_get_attachment_image($location['image'], 'post-image') !!}
							@endif
						</div>
						<div class="location__inner">
							@if(!empty($location['name']))
								<div class="location__title h4">{{ $location['name'] }}</div>
							@endif
							<div class="location__columns">
								<div class="location__column">
									@if(!empty($location['street']))
										<div class="location__address">{{ $location['street'] }}
											<br>{{ $location['zipcode'] }} {{ $location['city'] }}
											<br>{{ $location['country'] }}</div>
									@endif
								</div>
								<div class="location__column">
									@if(!empty($location['openstreetmap_url']))
										<a href="{{ $location['openstreetmap_url'] }}" target="_blank">
											<x-icon icon="marker"
											        sprite="global"/> {{ __('View on map', 'wp-surf-theme') }}
										</a>
									@endif
									@if(!empty($location['url']))
										<a href="{{ $location['url'] }}" target="_blank">
											<x-icon icon="anchor" sprite="global"/> {{ __('Website', 'wp-surf-theme') }}
										</a>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
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
