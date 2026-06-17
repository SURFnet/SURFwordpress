@php
	use SURF\PostTypes\Asset;

	/**
	 * @var Asset $asset
	 */

	$related = $asset->related();
	if ( empty( $related ) ) {
		return;
	}

@endphp
<div class="assets-single__related assets-single__group">
	<h2 class="h3 assets-single__group-title">
		{!! surfGetHeadingIcon('h3') !!}
		{{ $asset->relatedTitle() }}
	</h2>
	<p>{!! $asset->relatedDescription() !!}</p>
	<div class="assets-single__related-list">
		@foreach( $related as $item )
			@include( 'asset.item', ['asset' => $item, 'hideExcerpt' => true] )
		@endforeach
	</div>
</div>