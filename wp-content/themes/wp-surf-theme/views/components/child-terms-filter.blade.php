@php
	use SURF\Repositories\TaxonomyRepository;

	/**
	 * @var string $name
	 * @var string $taxonomy
	 * @var array $counts
	 */

	$counts = (isset($counts) && is_array($counts)) ? $counts : null;
	$selectedValues = surfGetSelectedCheckboxValues($name);

	$dynamicCountsEnabled = (bool) get_option('options_faq_archive_dynamic_filter_counts', false);
	$dynamicCountsSeparator = get_option('options_faq_archive_dynamic_filter_counts_separator', '');

@endphp
<fieldset data-name="{{$name}}">
	@foreach(TaxonomyRepository::orderedByPriority($taxonomy) as $term)
		<x-child-terms-filter-item
				:name="$name"
				:taxonomy="$taxonomy"
				:counts="$counts"
				:selected="$selectedValues"
				:term="$term"
				:dynamicCountsEnabled="$dynamicCountsEnabled"
				:dynamicCountsSeparator="$dynamicCountsSeparator"
		/>
	@endforeach
</fieldset>
