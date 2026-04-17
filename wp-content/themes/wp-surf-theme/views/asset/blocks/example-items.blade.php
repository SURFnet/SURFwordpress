@php
	use SURF\PostTypes\Asset;

	/**
	 * @var Asset $asset
	 */

	$examples = $asset->examples();
	if ( empty( $examples ) ) {
		return;
	}

@endphp
<div class="assets-single__downloads-secondary assets-single__group">
	<h2 class="h3 assets-single__group-title">
		{!! surfGetHeadingIcon('h3') !!}
		{{ $asset->examplesTitle() }}
	</h2>
	<p>{!! $asset->examplesDescription() !!}</p>
	<div class="assets-single__download-secondary-list">
		@foreach( $examples as $example )
			@if( $example['file'] )
				@include(' asset.file', [
					'file' => $example['file'],
					'description' => $example['file_description']
				] )
			@endif
		@endforeach
	</div>
</div>