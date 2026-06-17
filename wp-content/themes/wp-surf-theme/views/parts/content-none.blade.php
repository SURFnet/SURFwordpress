@php
	use SURF\Helpers\SearchHelper;
@endphp

<section class="no-results not-found entry">
	<div class="entry__inner padded container">
		<h1 class="entry__title">
			{!! surfGetHeadingIcon('h1') !!}
			{{ __('Nothing Found', 'wp-surf-theme') }}
		</h1>
		@if(is_search())
			<p>{{__('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'wp-surf-theme')}}</p>
		@else
			<p>{{__("It seems we can't find what you're looking for. Perhaps searching can help.", 'wp-surf-theme')}}</p>
		@endif
		{!! SearchHelper::getForm(placeholder: __('Search in the site', 'wp-surf-theme')) !!}
	</div>
</section>
