@php
	use SURF\Core\Taxonomies\TaxonomyRepository;
	use SURF\Taxonomies\AssetCategory;

	$taxonomy = get_taxonomy( AssetCategory::getName() );
	$terms    = get_terms( ['taxonomy' => $taxonomy->name, 'hide_empty' => false] );
	if ( is_wp_error( $terms ) ) {
		return;
	}

	$taxList = [];
	foreach ( $terms as $term ) {
		$taxList[ $term->slug ] = $term->name;
	}

	$taxonomyClass = surfApp(TaxonomyRepository::class)->all()[$taxonomy->name];
@endphp

<div class="archive-filter__group" data-accordion-item>
	<div class="archive-filter__header desktop-accepted" aria-controls="accordion-{{ $id }}" aria-expanded="true"
	     aria-label="{{ __('Toggle', 'wp-surf-theme') }} {{ $taxonomy->label }}" data-accordion-button>
		<span class="h4">{{ $taxonomy->label }}</span>
		@if( !empty( $taxonomy->description ) )
			<span data-toggle-popup>
				<x-icon icon="info-circle" sprite="global" class="archive-filter__info-icon"/>
                </span>
			<div class="archive-filter__popup">
				<div class="archive-filter__popup-header">
					<div class="h4">{{ __('Category:', 'wp-surf-theme') }} {{ $taxonomy->label }}</div>
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
			<x-checkbox-filter :name="$taxonomyClass::getQueryKey()" :list="$taxList"/>
		</div>
	</div>
</div>
