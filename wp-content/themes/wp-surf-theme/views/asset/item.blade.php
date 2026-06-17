@php
	use SURF\PostTypes\Asset;

	/**
	 * @var Asset $asset
	 * @var Boolean $hideExcerpt
	 */

	$categories = $asset->getCategoriesBySecondNiveau();
	$headingTag = $headingTag ?? 'h3';
	$hideExcerpt = $hideExcerpt ?? false;

@endphp
<article class="assets-item-full">
	<header class="assetss-item-full__header">
		<x-heading :tag="$headingTag" class="assets-item-full__title">
			<a href="{!! $asset->permalink() !!}">
				@if( $categories )
					<x-icon class="assets-item-full__document-icon" icon="document" sprite="global"/>
				@endif
				{!! $asset->title() !!}
				@if( !$categories )
					<x-icon class="assets-item-full__arrow-icon" :icon="'arrow-right'" :sprite="'global'"/>
				@endif
			</a>
		</x-heading>
		<div>
			@if( $categories )
				<div class="assets-item-full__categories">
					@foreach( $categories->take(4) as $category )
						<x-badge :secondary="!$loop->index==0">{{ $category->name }}</x-badge>
					@endforeach
					@if( $categories->count() > 4 )
						<x-badge :clear="true">+{{ $categories->count() - 4  }}</x-badge>
					@endif
				</div>
			@endif
			<x-icon class="assets-item-full__arrow-icon" :icon="'arrow-right'" :sprite="'global'"/>
		</div>
	</header>
	@if(!$hideExcerpt)
		<p class="assets-item-full__excerpt">
			{!! $asset->excerpt() !!}
		</p>
	@endif

	<dl class="meta-list">
		<div>
			<dt class="sr-only">{{__('Updated on', 'wp-surf-theme')}}</dt>
			<dd>
				<x-icon icon="calendar-checked" sprite="global"/> {{ $asset->reviewDate('d F Y') }}</dd>
		</div>
		<x-tag-list :list="$asset->getTags()"/>
	</dl>

	{{--<a class="assets-item-full__anchor" href="{!! $asset->permalink() !!}" aria-hidden="true"></a>--}}
</article>
