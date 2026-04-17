@php
	use SURF\Core\PostTypes\PostCollection;
	use SURF\PostTypes\Asset;

	/**
	 * @var PostCollection|Asset[] $assets
	 * @var string $section_id
	 * @var string $title
	 * @var string $content
	 * @var int $total
	 */

	$has_sidebar = !empty( $aside_menu );

@endphp

@extends( 'layouts.app' )

@section( 'content' )
	<section id="{{ $section_id }}" @class( [
		'archive archive-page container padded',
		'medium-narrow' => !$has_sidebar,
		] )>

		<form class="archive__form" data-term="{{ $selected ?? null }}">
			<x-breadcrumb/>
			<header class="archive-page__header">
				@include( 'asset.parts.archive-header', [
					'title'   => $title,
					'content' => $content,
				] )
			</header><!-- .archive__header -->

			@include( 'asset.parts.category-top-menu', [
				'menu' => $top_menu ?? null,
			] )

			@include( 'parts.global.separator' )

			<div class="grid">
				@if( $has_sidebar )
					@include( 'asset.parts.category-aside-menu', [
						'menu'     => $aside_menu,
						'selected' => $selected ?? null,
					] )
				@endif
				<div @class( [
					'column span-12-lg span-8-md span-4-sm' => !$has_sidebar,
					'column span-8-lg span-5-md span-4-sm'  => $has_sidebar,
				] )>
					<div class="">
						@include( 'asset.parts.archive-filtering', ['count' => $total] )
						<div class="archive__content">
							@include( 'asset.items', [
								'assets'      => $assets,
								'hideExcerpt' => true,
							] )
						</div><!-- .archive__content -->
					</div>
					<div class="archive__pagination">
						@include( 'parts.pagination' )
					</div><!-- .archive__pagination -->
				</div>
			</div>
		</form>
	</section>
@endsection
