@php

	use SURF\PostTypes\Asset;

	/**
	 * @var Asset $asset
	 */
	$assetsFile   = $asset->file();
	$propertyList = $asset->getDownloadPropertyList();
	if ( empty( $assetsFile ) && empty( $propertyList ) ) {
		return;
	}

@endphp
<div class="assets-single__group">
	<div @class([
		'assets-single__sidebar',
		'assets-single__sidebar--up-to-date' => $asset->isUpToDate(),
		'assets-single__sidebar--out-of-date' => $asset->isOutOfDate(),
	])>
		<h2 class="h3 assets-single__group-title sr-only">
			{{ __('Download', 'wp-surf-theme') }}
		</h2>
		@if( !empty($assetsFile) )
			<div class="assets-single__group-data">
				<div class="assets-file__content">
					<h3 class="h5 assets-file__title">
						<x-icon icon="document" sprite="global"/>
						{!! surfGetHeadingIcon('h5') !!}
						{{ $assetsFile->title() }}
					</h3>
					<p>{{ $asset->fileDescription() }}</p>
				</div>
			</div>
			<div class="assets-single__group-actions">
				<a class="button" id="page-download" href="{{ $assetsFile->url() }}" download>
					{{ __('Download', 'wp-surf-theme') }}
				</a>
			</div>
		@endif
		@if( !empty($propertyList) )
			<div class="assets-single__group-list">
				@foreach( $propertyList as $item )
					<div>
						<x-icon icon="{{ $item['icon'] ?? 'check' }}" sprite="global"/> {{ $item['label'] }}
					</div>
				@endforeach
			</div>
		@endif
	</div>
</div>