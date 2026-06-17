@php
	use SURF\Core\Taxonomies\TaxonomyRepository;
	use SURF\Helpers\PolylangHelper;

	/**
	 * @var array $args
	 * @var array $instance
	 * @var array $settings
	 */

@endphp
<h2 class="h3">
	{!! surfGetHeadingIcon('h3') !!}
	{{ $settings['title'] ?? '' }}
</h2>
@foreach($settings['taxonomies'] ?? [] as $taxonomy)
	@php
		$taxonomy = get_taxonomy($taxonomy);
		if (empty($taxonomy)) {
			continue;
		}

		$id      = uniqid();
		$taxName = $taxonomy->name;
		$label   = PolylangHelper::getThemeOption('search_taxonomy_' . $taxName) ?: $taxonomy->label;
		$terms   = get_terms(['taxonomy' => $taxName, 'hide_empty' => false]);
		if (is_wp_error($terms) || empty($terms)) {
			continue;
		}

		$taxClass = surfApp(TaxonomyRepository::class)->all()[$taxName] ?? null;
		if (empty($taxClass)) {
			continue;
		}

		$taxList = [];
		foreach ($terms as $term) {
			$termKey = $taxClass::useSlugInFilters() ? $term->slug : $term->term_id;
			$taxList[$termKey] = $term->name;
		}
	@endphp
	<div class="archive-filter__group" data-accordion-item>
		<div class="archive-filter__header desktop-accepted" aria-controls="accordion-{{ $id }}" aria-expanded="true"
		     aria-label="{{ __('Toggle', 'wp-surf-theme') }} {{ $label }}" data-accordion-button>
			<h3 class="h4">{{ $label }}</h3>
			@if(!empty($taxonomy->description))
				<span data-toggle-popup>
                    <x-icon icon="info-circle" sprite="global" class="archive-filter__info-icon"/>
                </span>
				<div class="archive-filter__popup">
					<div class="archive-filter__popup-header">
						<div class="h4">{{ __('Category:', 'wp-surf-theme') }} {{ $label }}</div>
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
		<div class="archive-filter__list   desktop-accepted " id="accordion-{{ $id }}" data-accordion-target>

			<div class="archive-filter__item item">
				<x-checkbox-filter :name="$taxClass::getQueryKey()" :list="$taxList"/>
			</div>
		</div>
	</div>
@endforeach
