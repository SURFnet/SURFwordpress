@php
	use SURF\ArchiveSURFFaqController;
	use SURF\Core\PostTypes\PostCollection;

	/**
	 * @var PostCollection $faqs
	 */
@endphp

@unless(empty($isSearching))
	<div class="archive__search-found-title h4">{!! __('Found items', 'wp-surf-theme') !!}</div>
@endunless
@if($faqs->isNotEmpty())
	@foreach($faqs as $faq)
		@include('faq.item', ['faq' => $faq, 'headingTag' => 'h2'])
	@endforeach
@endif
