@php
	use SURF\Blocks\Step;

	/**
	 * @var array $blockAttributes
	 * @var string $blockName
	 * @var string $content
	 * @var Step $block
	 * @var array $context
	 */

	// context from parent roadmap block
	$display = $context['display'];
	$icons = $context['icons'];

	$title = $blockAttributes['title'] ?? '';
	$subtitle = $blockAttributes['subtitle'] ?? '';
	$icon = $blockAttributes['icon'] ?? 'file';
	$order = $blockAttributes['order'] ?? 1;

@endphp
<article class="surf-block surf-block-step">
	@if($icons && $icon)
		<div class="surf-block-step__icon">
			<x-icon :icon="$icon" sprite="global"/>
		</div>
	@else
		<div class="surf-block-step__number">
			{{ $order }}
		</div>
	@endif
	<div class="surf-block-step__content">
		<div class="surf-block-step__text">
			<h3 class="surf-block-step__title">{!! $title !!}</h3>
			<p class="surf-block-step__subtitle">{!! $subtitle !!}</p>
		</div>
	</div>
</article>
