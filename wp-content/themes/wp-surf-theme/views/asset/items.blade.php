@php
	use SURF\Core\PostTypes\PostCollection;
	use SURF\Taxonomies\AssetCategory;

	/**
	 * @var PostCollection $assets
	 * @var AssetCategory $cateogry
	* @var Boolean $hideExcerpt
	 */

	$hideExcerpt = $hideExcerpt ?? false;

@endphp
<div>
	@isset( $category )
		<div class="assets__header">
			<h3 class="assets__main-title">
				{!! surfGetHeadingIcon('h3') !!}
				{{ $category->name }}
			</h3>
			<p class="assets__main-introduction">{!! $category->description !!}</p>
		</div>
	@endisset

	@if( $assets->isEmpty() )
		@include( 'asset.not-found' )
	@else
		<div>
			@foreach( $assets as $asset )
				@include( 'asset.item', ['asset' => $asset, 'hideExcerpt' => $hideExcerpt] )
			@endforeach
		</div>
	@endif
</div>

