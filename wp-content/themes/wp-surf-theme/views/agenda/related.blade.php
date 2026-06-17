@php
	use SURF\Core\PostTypes\PostCollection;

	/**
	 * @var PostCollection $relatedEvents
	 */

	if ( $relatedEvents->isEmpty() ) {
		return;
	}

@endphp
<div class="related-posts container padded">
	<div class="related-posts__header">
		<h2 class="related-posts__title">
			{!! surfGetHeadingIcon('h2') !!}
			{{ __('Related events', 'wp-surf-theme') }}
		</h2>
	</div>
	<div data-mobile-slider>
		@foreach( $relatedEvents as $event )
			<div class="slider-item">
				@include( 'agenda.item', ['event' => $event, 'type' => 'block'] )
			</div>
		@endforeach
	</div>
</div>
