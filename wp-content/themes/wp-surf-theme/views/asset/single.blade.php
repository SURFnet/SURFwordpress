@php
	use SURF\Helpers\BreadcrumbsHelper;
	use SURF\Helpers\Helper;
	use SURF\PostTypes\Asset;
	use SURF\Taxonomies\AssetCategory;

	/**
	 * @var Asset $asset
	 */

	$share = false; // hardcoded for now. Make into setting (under Assets > Settings) later if needed.

	$showTags = false; // hardcoded for now.
	$tags     = $asset->getTags();

	$slug = Helper::getSanitizedGet('category', '');
	$term = !empty($slug) && is_string($slug) ? AssetCategory::findBySlug($slug) : null;
	if ( empty( $term ) ) {
		$wp_term = $asset->primaryCategory(AssetCategory::getName(), true);
		if ( !empty( $wp_term ) ) {
			$term = AssetCategory::find($wp_term->term_id);
		}
	}
@endphp

@extends( 'layouts.app' )

@section( 'content' )
	<article id="post-{{ $asset->ID() }}" {!! $asset->postClass('entry') !!}>
		<div class="grid container padded">
			<div class="column span-8-lg span-5-md span-4-sm">
				@if( BreadcrumbsHelper::shouldShow() )
					<x-breadcrumb/>
				@elseif( !empty( $term ) )
					<a href="{{ $term->link() }}" class="assets-single__back-to-asset-category">
						<x-icon icon="chevron-left-regular" sprite="global"/>
						{{ sprintf( __('Back to %s', 'wp-surf-theme'), $term->name ) }}
					</a>
				@endif
				<h1 class="assets-single__title">
					{!! surfGetHeadingIcon('h1') !!}
					{!! $asset->title() !!}
				</h1>
				<x-category-list prefix="assets"
								 :list="$asset->categories()"
								 :primaryName="$term?->name"
								 :withParents="AssetCategory::shouldShowParents()"
								 :hasArchive="AssetCategory::hasArchive()"/>
				<div class="assets-single__introduction">
					{!! $asset->content() !!}
				</div>

				@if( !has_block( 'surf/asset-examples' ) )
					@include( 'asset.blocks.example-items', ['asset' => $asset] )
				@endif

				@if( !has_block( 'surf/related-assets' ) )
					@include( 'asset.blocks.related-items', ['asset' => $asset] )
				@endif

				@include( 'parts.single-contact-persons' )
			</div>
			<div class="column span-4-lg span-3-md span-4-sm">
				<div class="assets-single__sidebar-sections">
					<div class="assets-single__sidebar-meta">
						@include( 'asset.parts.single-download-cta', ['asset' => $asset] )
						@include( 'asset.parts.single-contact-cta', ['asset' => $asset] )
					</div>

					@if( $share )
						<div class="assets-single__sidebar-share">
							@include('components.share', [
								'title' => __('Share this', 'wp-surf-theme'),
								'clear' => true,
							])
						</div>
					@endif

					@if( $showTags && $tags->isNotEmpty() )
						<div class="assets-single__sidebar-tags">
							<x-tag-list class="assets-single__tags"
										:list="$tags"
							/>
						</div>
					@endif
				</div>
			</div>
		</div>
	</article><!-- #post-{{ $asset->ID() }} -->

	{!! $asset->editPostLink(text: _x('Edit', 'admin', 'wp-surf-theme'), class: 'post-edit-link button') !!}
@endsection
