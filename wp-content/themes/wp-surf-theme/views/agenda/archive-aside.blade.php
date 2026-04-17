@php
	use SURF\Helpers\Helper;
	use SURF\PostTypes\Page;

	/**
	 * @var Page|null $page
	 * @var string $widgetAreaId
	 */

	$showWidgets = !empty( $widgetAreaId ) && is_active_sidebar( $widgetAreaId );
	if( !$showWidgets && empty( $pastEventsFilters ) ) {
		return;
	}

	$id = uniqid();
	$name = 'past-events';
	$expanded = !empty( Helper::getGet($name)) ? 'true' : 'false';
	$view = 'top'; //'bottom';
@endphp

<aside class="column span-4-lg span-3-md span-4-sm">
	<form class="archive__form"
	      action="{{ $page?->permalink() ?? get_post_type_archive_link(get_queried_object()->name) }}">
		<div class="archive__widget-area">

			@if(!empty($pastEventsFilters) && $view === 'top')
				<div class="archive-filter__group" data-accordion-item>
					<div class="archive-filter__header desktop-accepted" aria-controls="accordion-{{ $id }}"
					     aria-expanded="{{ $expanded }}"
					     aria-label="{{ __('Toggle "Upcoming events"', 'wp-surf-theme') }}" data-accordion-button>
						<h2 class="h3">{{ __('Upcoming events', 'wp-surf-theme') }}</h2>

						<div class="archive-filter__toggle desktop-accepted">
							<x-icon icon="chevron-down" sprite="global"/>
						</div>
					</div>
					<div class="archive-filter__list desktop-accepted " id="accordion-{{ $id }}" data-accordion-target>
						<div class="archive-filter__item item">
							<x-checkbox-filter :name="$name" :list="$pastEventsFilters"/>
						</div>
					</div>
				</div>
			@endif

			@php(dynamic_sidebar($widgetAreaId))

			@if(!empty($pastEventsFilters) && $view === 'bottom')
				<div class="archive-filter__group" data-accordion-item>
					<div class="archive-filter__header desktop-accepted" aria-controls="accordion-{{ $id }}"
					     aria-expanded="true"
					     aria-label="{{ __('Toggle "Upcoming events"', 'wp-surf-theme') }}" data-accordion-button>
						<h2 class="h3 sr-only">{{ __('Upcoming events', 'wp-surf-theme') }}</h2>
					</div>
					<div class="archive-filter__list desktop-accepted" id="accordion-{{ $id }}" data-accordion-target>
						<div class="archive-filter__item item">
							<x-checkbox-filter :name="$name" :list="$pastEventsFilters"/>
						</div>
					</div>
				</div>
			@endif

		</div>
	</form>
</aside>
