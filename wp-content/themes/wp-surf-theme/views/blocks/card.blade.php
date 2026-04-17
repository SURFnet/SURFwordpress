@php
	use SURF\Blocks\Card;

	/**
	 * @var array $blockAttributes
	 * @var string $blockName
	 * @var string $content
	 * @var Card $block
	 * @var array $context
	 */

	$display = $context['display'];
	$title = $blockAttributes['title'] ?? '';
	$subtitle = $blockAttributes['subtitle'] ?? '';
	$icon = $blockAttributes['icon'] ?? 'file';
	$link = $blockAttributes['link'] ?? '';
	$imageUrl = $blockAttributes['imageUrl'] ?? '';
	$imageId = $blockAttributes['imageId'] ?? '';
	$hideImagesOnMobile = $blockAttributes['hideImagesOnMobile'] ?? false;
@endphp

<div class="slider-item">
	<article class="post-item post-item--block">
		<div class="post-item__inner">
			@if($imageUrl)
				<div class="post-item__figure @if($hideImagesOnMobile) post-item__figure--hidden-on-mobile @endif">
					<img src="{{ $imageUrl }}" alt="{{ $title }}"/>
				</div>
			@endif
			<div class="post-item__content">
				<h3 class="post-item__title h4">{!! $title !!}</h3>
				<p class="post-item__excerpt">{!! $subtitle !!}</p>
			</div>
		</div>
	</article>
</div>
