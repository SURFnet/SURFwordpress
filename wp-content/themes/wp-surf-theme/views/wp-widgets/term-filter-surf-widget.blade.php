@php
	use SURF\Core\Taxonomies\TaxonomyRepository;

	/**
	 * @var array $args
	 * @var array $instance
	 * @var array $settings
	 * @var array $taxonomyFields
	 */

@endphp
<h2 class="h3">
	{!! surfGetHeadingIcon('h3') !!}
	{{ $settings['title'] ?? '' }}
</h2>
@foreach($taxonomyFields as $taxonomy => $data)
	@php
		$groupData = $settings[$data['field_name']] ?? [];
		if (empty($groupData)) {
			continue;
		}

		$terms = $groupData['terms'] ?? [];
		if (empty($terms)) {
			continue;
		}

		$taxonomy = get_taxonomy($taxonomy);
		if (empty($taxonomy)) {
			continue;
		}

		$taxName  = $taxonomy->name;
		$taxClass = surfApp(TaxonomyRepository::class)->all()[$taxName] ?? null;
		if (empty($taxClass)) {
			continue;
		}

		$id      = uniqid();
		$taxList = [];
		foreach ($terms as $term) {
			$termKey = $taxClass::useSlugInFilters() ? $term->slug : $term->term_id;
			$taxList[$termKey] = $term->name;
		}
	@endphp
	<div class="archive-filter__group" data-accordion-item>
		<div class="archive-filter__header desktop-accepted" aria-controls="accordion-{{ $id }}" aria-expanded="true"
		     aria-label="{{ __('Toggle', 'wp-surf-theme') }} {{ $groupData['title'] ?? $taxonomy->label }}"
		     data-accordion-button>
			<span class="h4">{{ $groupData['title'] ?? $taxonomy->label }}</span>
			@if(!empty($taxonomy->description))
				<span data-toggle-popup>
                    <x-icon icon="info-circle" sprite="global" class="archive-filter__info-icon"/>
                </span>
				<div class="archive-filter__popup">
					<div class="archive-filter__popup-header">
						<div
								class="h4">{{ __('Category:', 'wp-surf-theme') }} {{ $groupData['title'] ?? $taxonomy->label }}</div>
						<span class="archive-filter__popup-close">{{ __('Close', 'wp-surf-theme') }}</span>
					</div>
					<div class="archive-filter__popup-text">
						{{ $taxonomy->description }}
					</div>
				</div>
			@endif
			<div class="archive-filter__toggle desktop-accepted ">
				<x-icon icon="chevron-down" sprite="global"/>
			</div>
		</div>
		<div class="archive-filter__list desktop-accepted" id="accordion-{{ $id }}" data-accordion-target>
			<div class="archive-filter__item item">
				<x-checkbox-filter :name="$taxClass::getQueryKey()" :list="$taxList"/>
			</div>
		</div>
	</div>
@endforeach
