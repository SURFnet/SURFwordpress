@php
	use SURF\PostTypes\Faq;

	/**
	 * @var Faq $faq
	 */

	$showParentCategories = (bool) get_option('options_faq_show_parent_categories', false);
	$normalCategories = $faq->categories();
	$formattedCategories = surfFormatTermsWithParents($normalCategories);
@endphp

<article class="faq-item is-open is-export">
	@if(!get_option('options_faq_archive_hide_labels', false))
		<div class="faq-item__main-category">
			{{ $faq->getPrimaryParentCategory()->name }}
		</div>
	@endif

	<div class="faq-item__title h4">
		{{ $faq->fullTitle() }}
	</div>

	<table>
		@if($showParentCategories)
			<thead>
			<tr>
				@foreach(array_keys($formattedCategories) as $parent)
					<th>{{ html_entity_decode($parent) }}</th>
				@endforeach
			</tr>
			</thead>
		@endif
		<tbody>
		<tr>
			@if($showParentCategories)
				@foreach($formattedCategories as $categories)
					<td>{{ html_entity_decode($categories) }}</td>
				@endforeach
			@else
				@foreach($normalCategories as $category)
					<td>{{ html_entity_decode($category->name) }}</td>
				@endforeach
			@endif
		</tr>
		</tbody>
	</table>

	<div class="faq-item__content" id="faq-item-{{ $faq->ID() }}">
		{!! $faq->fullContent() !!}
	</div>
</article>
