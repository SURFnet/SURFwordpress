@php
	/**
	 * @var $relatedPosts
	 */
@endphp
<div class="related-posts container padded">
	<div class="related-posts__header">
		<h2 class="related-posts__title">
			{!! surfGetHeadingIcon('h2') !!}
			{{ __('Related posts', 'wp-surf-theme') }}
		</h2>
	</div>
	<div data-mobile-slider>
		@foreach($relatedPosts as $post)
			<div class="slider-item">
				@include('post.item', ['post' => $post, 'type' => 'block'])
			</div>
		@endforeach
	</div>
</div>
