@php
	use SURF\Core\PostTypes\PostCollection;
	use SURF\PostTypes\Faq;

	/**
	 * @var PostCollection|Faq[] $faqs
	 */

@endphp

@extends( 'layouts.app' )

@section( 'content' )
	<article id="archive-{{ get_queried_object()->name }}" class="archive archive-page container padded">

		<div class="archive-page__header">
			<h1 class="archive-page__title">
				{!! surfGetHeadingIcon('h1') !!}
				{{ single_term_title() }}
			</h1>
			<div class="archive-page__description">
				{{ the_archive_description() }}
			</div>
		</div>

		@include( 'parts.global.separator' )

		<div class="archive__content grid">
			@forelse( $faqs as $faq )
				<div class="column span-4-sm span-4-md span-4-lg">
					@include( 'faq.item', ['faq' => $faq] )
				</div>
			@empty
				<div class="column span-4-sm span-8-md span-12-lg">
					@include( 'faq.not-found' )
				</div>
			@endforelse
		</div><!-- .archive__content -->

		<div class="archive__pagination">
			@include( 'parts.pagination' )
		</div><!-- .archive__pagination -->

	</article><!-- #archive-{{ get_queried_object()->name }} -->

@endsection
