@php
	use SURF\Core\PostTypes\PostCollection;
	use SURF\PostTypes\Faq;
	use SURF\Taxonomies\FaqCategory;

	/**
	 * @var Faq $faq
	 * @var PostCollection $related
	 */

	$prefix = Faq::getLocalizedSettingsPrefix();
	$singleTitle = get_option("{$prefix}_faq_field_single_title", null);
	$singleRelatedQuestionsTitle = get_option("{$prefix}_faq_field_single_related_questions_title", null);

	$parentCategory  = $faq->getPrimaryParentCategory();
	$primaryCategory = $faq->getPrimaryTerm( FaqCategory::getName() );
	if ( empty( $parentCategory ) ) {
		$parentCategory  = $primaryCategory;
		$primaryCategory = null;
	}

@endphp

@extends('layouts.app')

@section('content')
	<form
			class="archive__form container padded"
			action="{{ get_post_type_archive_link(Faq::getName()) }}"
	>
		<header class="archive-page__header page-header--centered">
			<h2 class="archive-page__title">
				{!! surfGetHeadingIcon('h2') !!}
				{!! $singleTitle ?: __('Frequently asked questions', 'wp-surf-theme') !!}
			</h2>

			<div class="archive-page__search-form">
				<x-search-filter placeholder="{{ __('Search for an answer', 'wp-surf-theme') }}"/>
				<button type="submit" class="search-submit">
					<span class="sr-only">{{ esc_attr_x( 'Search', 'submit button', 'wp-surf-theme' ) }}</span>
					<x-icon icon="search" sprite="global" class=""/>
				</button>
			</div>

		</header><!-- .archive__header -->
	</form>
	@include( 'parts.global.separator' )
	<article class="entry entry--single faq-single">
		<div class="padded container">
			<div class="entry__inner">
				<div class="faq-single__header">
					<a class="faq-single__back"
					   href="{{ Faq::getArchiveLink() }}">{{ __('Back to overview', 'wp-surf-theme') }}</a>
					@if( $parentCategory )
						<div class="faq-item__categories">
							<div class="faq-item__main-category">
								{{ $parentCategory->name }}
							</div>
							@if( $primaryCategory && ( $primaryCategory !== $parentCategory ) )
								<div class="faq-item__sub-category">
									{{ $primaryCategory->name }}
								</div>
							@endif
						</div>
					@endif
					<h1 class="faq-single__title">
						{!! surfGetHeadingIcon('h1') !!}
						{!! $faq->fullTitle() !!}
					</h1>
				</div>

				{!! $faq->fullContent() !!}
				<x-tag-list :list="$faq->faqTags()" class="faq-single__tags"/>
			</div>
			@include( 'parts.single-contact-persons', ['class' => 'container'] )
		</div>
	</article>

	@if( $related->isNotEmpty() )
		<section class="container padded">
			<div class="faq-single__related">
				<h2 class="faq-single__related-title h4">
					{{ $singleRelatedQuestionsTitle ?: __('Other questions', 'wp-surf-theme') }}
				</h2>
				@foreach( $related as $question )
					@include( 'faq.item', ['faq' => $question] )
				@endforeach
			</div>
		</section>
	@endif
@endsection
