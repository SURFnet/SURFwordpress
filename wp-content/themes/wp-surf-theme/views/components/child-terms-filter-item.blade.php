@php
	use SURF\Repositories\TaxonomyRepository;

	/**
	 * @var string $name
	 * @var string $taxonomy
	 * @var array|null $counts
	 * @var array $selected
	 * @var WP_Term $term
	 * @var bool $dynamicCountsEnabled
	 * @var string $dynamicCountsSeparator
	 */

	$childTerms = TaxonomyRepository::orderedByPriority($taxonomy, $term->term_id);
	$activateToggle = !empty($childTerms) && !empty(get_term_meta($term->term_id, 'activate_toggle', true));

@endphp
<div class="archive-filter__group" data-accordion-item>
	<div class="archive-filter__header @if($activateToggle) desktop-accepted @endif"
	     aria-controls="faq-category-{{ $term->term_id }}" aria-expanded="false"
	     aria-label="{{ __('Toggle', 'wp-surf-theme') }} {{ $term->name }}" data-accordion-button>
		<span class="h4">{{ $term->name }}</span>
		@if(!empty($term->description))
			<span data-toggle-popup>
                <x-icon icon="info-circle" sprite="global" class="archive-filter__info-icon"/>
            </span>
			<div class="archive-filter__popup">
				<div class="archive-filter__popup-header">
					<div class="h4">{{ sprintf(__('Category: %s', 'wp-surf-theme'), $term->name) }}</div>
					<button class="archive-filter__popup-close">{{ __('Close', 'wp-surf-theme') }}</button>
				</div>
				<div class="archive-filter__popup-text">
					{{ $term->description }}
				</div>
			</div>
		@endif
		<div class="archive-filter__toggle @if($activateToggle) desktop-accepted @endif">
			<x-icon icon="chevron-down" sprite="global"/>
		</div>
	</div>
	<div class="archive-filter__list  @if($activateToggle) desktop-accepted @endif"
	     id="faq-category-{{ $term->term_id }}" data-accordion-target>
		@foreach($childTerms as $child)
			@php($count = is_array($counts) ? ($counts[$child->slug] ?? 0) : $child->count)
			<div class="archive-filter__item item">
				<input class="hidden" type="checkbox" name="{{ $name }}[]" value="{{ $child->slug }}"
				       id="{{ $child->slug }}" {{ checked(in_array($child->slug, $selected)) }}>

				<label
						for="{{ $child->slug }}" @class(['archive-filter__count' => $dynamicCountsEnabled])>
					{{ html_entity_decode($child->name) }}

					@if($dynamicCountsEnabled)
						<span>
                            <span class="archive-filter__count__current">{{ $count }}</span>
                            <span class="archive-filter__count__separator">{{ $dynamicCountsSeparator }}</span>
                            <span class="archive-filter__count__total">{{ $child->count }}</span>
                        </span>
					@endif
				</label>
			</div>
		@endforeach
	</div>
</div>
