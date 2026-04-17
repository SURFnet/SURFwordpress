@php
	use SURF\Enums\Theme;
	use SURF\PostTypes\Agenda;
	use SURF\Taxonomies\AgendaLocation;

	/**
	 * @var Agenda $event
	 * @var $relatedEvents
	 */

	if($event->shouldShowLocation()) {
		$location = AgendaLocation::getPrimaryLocationInfo($event->ID());
	}
@endphp

@extends('layouts.app')

@section('content')
	<article id="post-{{$event->ID()}}"
	         {!! $event->postClass('entry '.(!$event->shouldShowLocation() ? 'entry--single' : '')) !!} @if(Theme::isSURF() && $event->getPrimaryCategoryColor($event->ID()))style='--surf-color-category: {{ $event->getPrimaryCategoryColor($event->ID()) }};'@endif>
		<div class="entry__header container padded {{ ($event->shouldShowLocation() ? 'entry__header--full-with' : '') }}">
			<x-breadcrumb/>
			<h1 class="entry__title">
				{!! surfGetHeadingIcon('h1') !!}
				{!! $event->title() !!}
			</h1>
			<ul class="entry__meta">
				<li class="entry__meta-category">
					<x-icon icon="calendar" sprite="global"/>
					@if($termId = $event->getPrimaryCategoryId($event->ID()))
						@php $showClosingATag = true; @endphp
						<a href="{{ get_term_link($termId) }}">
							@endif
							{{ $event->getPrimaryCategoryName($event->ID()) }}
							@if(isset($showClosingATag) && $showClosingATag)
						</a>
					@endif
				</li>
				@if($event->date())
					<li>
						{{ $event->date() }}
					</li>
				@endif
				@if(!empty($location['name']))
					<li class="entry__meta-location">
						<a href="{{ get_term_link($location['id']) }}">
							<x-icon icon="marker" sprite="global"/> {{ $location['name'] }}
						</a>
					</li>
				@elseif($event->location())
					<x-icon icon="marker" sprite="global"/>
					{{ $event->location() }}
				@endif
			</ul>
			@if(has_post_thumbnail() && !$event->shouldHideFeaturedImage() && !$event->shouldShowLocation())
				<div class="entry__figure">{!! $event->postThumbnail('post-image-full') !!}</div>
			@endif
		</div>

		@if(!$event->shouldShowLocation())
			<div class="entry__inner padded container">
				{!! $event->content() !!}
				@include('parts.single-contact-persons')
			</div>
		@else
			<div class="padded container grid">
				<div class="entry__inner column span-8-lg span-4-md span-4-sm">
					@if(has_post_thumbnail() && !$event->shouldHideFeaturedImage())
						<div class="entry__figure">{!! $event->postThumbnail('post-image-full') !!}</div>
					@endif
					{!! $event->content() !!}
				</div>
				<div class="entry__inner column span-4-lg span-4-md span-4-sm">
					<div class="location">
						<div class="location__figure">
							@if(!empty($location['image']))
								{!! wp_get_attachment_image($location['image'], 'post-image') !!}
							@endif
						</div>
						<div class="location__inner">
							@if(!empty($location['name']))
								<a href="{{ get_term_link($location['id']) }}"
								   class="location__title h4">{{ $location['name'] }}</a>
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
				@include('parts.single-contact-persons', ['class' => 'column span-4-sm span-8-md span-12-lg'])
			</div>
		@endif
	</article><!-- #post-{{ $event->ID() }} ?> -->

	{!! $event->editPostLink(text: _x('Edit', 'admin', 'wp-surf-theme'), class: 'post-edit-link button') !!}

	<div class="container padded">
		@include('components.share')
		@include('agenda.related', compact('event', 'relatedEvents'))
	</div>
@endsection

@push('head')
	<script type="application/ld+json">
		{!! json_encode([
			'@context' => 'https://schema.org',
			'@type' => 'Event',
			'name' => $event->title(),
			'location' => $event->location(),
			'startDate' => $event->startDate(),
			'endDate' => $event->endDate(),
			'image' => wp_get_attachment_image_url($event->postThumbnailId(), 'large')
		]) !!}
	</script>
@endpush
