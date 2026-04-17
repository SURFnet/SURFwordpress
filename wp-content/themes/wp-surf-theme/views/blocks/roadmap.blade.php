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
	<div class="surf-block-roadmap__header">
		<h2 class="surf-block-roadmap__title">{{ $title }}</h2>
		<p class="surf-block-roadmap__subtitle">{{ $subtitle }}</p>
	</div>
	<div class="surf-block-roadmap-wrapper">
		<div class="surf-block-roadmap__items"
		     @if($blockAttributes['display'] === 'slider') data-vacancy-slider @endif
		     @if($wide) data-vacancy-wide="true" @endif
		>
			{!! $content !!}
		</div>
		<div id="controls">
			<button class="previous">
				<x-icon icon="arrow-left" sprite="global"/>
			</button>
			<button class="next">
				<x-icon icon="arrow-right" sprite="global"/>
			</button>
		</div>
	</div>
</section>
