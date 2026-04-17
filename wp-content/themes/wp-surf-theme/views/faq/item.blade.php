@php
	use SURF\PostTypes\Faq;
	use SURF\Taxonomies\FaqCategory;
	use SURF\Taxonomies\FaqTag;

	/**
	 * @var Faq $faq
	 */

	$headingTag = $headingTag ?? 'h3';
	$hideLabels = get_option('options_faq_archive_hide_labels', false);

	$parentCategory  = $faq->getPrimaryParentCategory();
	$primaryCategory = $faq->getPrimaryTerm( FaqCategory::getName() );
	if ( empty( $parentCategory ) ) {
		$parentCategory  = $primaryCategory;
		$primaryCategory = null;
	}

@endphp
<article class="faq-item" data-accordion-item>
	<button type="button" class="faq-item__toggle" aria-controls="faq-item-{{$faq->ID()}}" aria-expanded="false"
			aria-label="{{ __('Toggle', 'wp-surf-theme') }} {{ $faq->fullTitle() }}" data-accordion-button>
		<x-icon icon="arrow-down" sprite="global" class="faq-item__arrow"/>
	</button>
	@if( !$hideLabels && $parentCategory )
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
	<x-heading :tag="$headingTag" class="faq-item__title h3">
		<a href="{{ $faq->permalink() }}">{!! $faq->fullTitle() !!}</a>
	</x-heading>
	<x-category-list prefix="faq"
					 :list="$faq->getTags()"/>
	<div class="faq-item__content" id="faq-item-{{ $faq->ID() }}" data-accordion-target>
		<p>{!! surfGetMyExcerpt(100, $faq->ID(), '...') !!}</p>

		<a class="faq-item__link" href="{{ $faq->permalink() }}"
		   class="arrow">{{ __('Read more', 'wp-surf-theme') }}</a>
	</div>
</article>
