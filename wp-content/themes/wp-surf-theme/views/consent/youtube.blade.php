@php
	/**
	 * @var string $id
	 * @var string $content
	 * @var WP_Block $block
	*/
@endphp

<template id="{{$id}}" class="youtube-consent-wrapper__template">
	{!! $content !!}
</template>

<div class="youtube-consent-wrapper__placeholder">
	<div class="youtube-consent-wrapper__notice hidden">
		<p>{{ __('This content is blocked because you do not allow third-party cookies.', 'wp-surf-theme') }}</p>
		<button class="button youtube-consent-wrapper__open">{{ __('Open cookie preferences', 'wp-surf-theme') }}</button>
	</div>
</div>
