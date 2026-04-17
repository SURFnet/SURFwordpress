@php
	use SURF\Enums\Theme;
	use SURF\PostTypes\Agenda;
	use SURF\Taxonomies\AgendaCategory;

	/**
	 * @var $type
	 * Type: row: Shows image on desktop on the left side, content on the right.
	 * Type: block: Shows image on top, content on the bottom.
	 * type: large: Shows up large
	 * @var Agenda $event
	 */

	$hideImagesOnMobile = (bool) ($hideImagesOnMobile ?? false);

	$type = $type ?? 'block';

	$headingTag = $headingTag ?? 'h3';
	$headingSize['large'] = 'h3';
	$headingSize['row'] = 'h5';

	$meta = [];

	$eventDate = $event->date();
	if( !empty( $eventDate ) ) {
		$meta[] = [
			'icon' => 'calendar',
			'label' => __('Date', 'wp-surf-theme'),
			'value' => $eventDate,
		];
	}

	$eventLocation = $event->location();
	if( !empty( $eventLocation ) ) {
		$meta[] = [
			'icon' => 'marker',
			'label' => __('Location', 'wp-surf-theme'),
			'value' => $eventLocation,
		];
	}

	$eventCategory = $event->getPrimaryCategoryName($event->ID());
	if( !empty( $eventCategory ) ) {
		$meta[] = [
			'class' => 'in-row-only',
			'icon' => 'tag',
			'label' => __('Category', 'wp-surf-theme'),
			'value' => $eventCategory,
		];
	}

	$category = $event->primaryCategory(AgendaCategory::getName());
@endphp

<x-card :layout="$type" :post="$event" :headingTag="$headingTag" :hideImagesOnMobile="$hideImagesOnMobile">
	@if( $category )
		<x-slot name="category">
			<x-badge>{{ $category->name }}</x-badge>
		</x-slot>
	@endif
	@if( !empty( $meta ) )
		<x-slot name="meta">
			<x-meta-list.meta-list :items="$meta"/>
		</x-slot>
	@endif
</x-card>
