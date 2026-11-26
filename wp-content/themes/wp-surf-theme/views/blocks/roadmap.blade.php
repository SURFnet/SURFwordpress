@php
	use SURF\Blocks\Roadmap;

	/**
	 * @var array $blockAttributes
	 * @var string $blockName
	 * @var string $content
	 * @var Roadmap $block
	 */

	$title = $blockAttributes['title'] ?? '';
	$subtitle = $blockAttributes['subtitle'] ?? '';
	$icons = $blockAttributes['icons'] ?? false;
	$display = $blockAttributes['display'] ?? 'flow';
	$wide = $blockAttributes['wide'] ?? false;
@endphp

<section @class(["surf-block surf-block-roadmap",
        "surf-block-roadmap--icons-hidden" => !$icons,
        "surf-block-roadmap--flow" => $display === 'flow',
        "surf-block-roadmap--slider" => $display === 'slider',
        ])>
	@if( !empty( $title ) || !empty( $subtitle ) )
		<div class="surf-block-roadmap__header">
			@if( !empty( $title ) )
				<h2 class="surf-block-roadmap__title">{{ $title }}</h2>
			@endif
			@if( !empty( $subtitle ) )
				<p class="surf-block-roadmap__subtitle">{{ $subtitle }}</p>
			@endif
		</div>
	@endif
	<div class="surf-block-roadmap-wrapper">
		<div class="surf-block-roadmap__items"
		     @if($blockAttributes['display'] === 'slider') data-vacancy-slider @endif
		     @if($wide) data-vacancy-wide="true" @endif
		>
			{!! $content !!}
		</div>
		{{-- A class instead of an ID, as a page can have more than one roadmap --}}
		<div class="surf-block-roadmap__controls" data-slider-controls>
			<button type="button" class="previous" aria-label="{{ __('Previous', 'wp-surf-theme') }}">
				<x-icon icon="arrow-left" sprite="global"/>
			</button>
			<button type="button" class="next" aria-label="{{ __('Next', 'wp-surf-theme') }}">
				<x-icon icon="arrow-right" sprite="global"/>
			</button>
		</div>
	</div>
</section>
