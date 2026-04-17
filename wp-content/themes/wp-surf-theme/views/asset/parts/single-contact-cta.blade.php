@php

	use SURF\PostTypes\Asset;

	/**
	 * @var Asset $asset
	 */

	$title = $asset->getContactTitle();
	if ( empty( $title ) ) {
		return;
	}

@endphp
<div class="assets-single__sidebar assets-single__sidebar--contact">
	<div class="assets-single__contact-text">
		<h2 class="h4 assets-single__sidebar-title">
			{!! surfGetHeadingIcon('h4') !!}
			{{ $title }}
		</h2>
		<p>{{ $asset->getContactDescription() }}</p>
	</div>
	@if( !empty( $asset->getContactButton() ) )
		<div class="assets-single__contact-actions">
			<a href="{{ $asset->getContactButton()['url'] }}"
			   class="button">{{ $asset->getContactButton()['title'] }}</a>
		</div>
	@endif
</div>