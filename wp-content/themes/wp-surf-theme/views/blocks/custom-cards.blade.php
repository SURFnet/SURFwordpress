@php
	use SURF\Blocks\CustomCards;

	/**
	 * @var array $blockAttributes
	 * @var string $blockName
	 * @var string $content
	 * @var CustomCards $block
	 */

	$title = $blockAttributes['title'] ?? '';
	$subtitle = $blockAttributes['subtitle'] ?? '';
	$display = $blockAttributes['display'] ?? 'grid';
	$wide = $blockAttributes['wide'] ?? false;
	$hideImagesOnMobile = $blockAttributes['hideImagesOnMobile'] ?? false;
	$backgroundColor = $blockAttributes['backgroundColor'] ?? '#ffffff';
	$textColor = $blockAttributes['textColor'] ?? '#000000';
@endphp

<section class="{{ $block->inEditor() ? 'pointer-events-none' : 'surf-block surf-block-custom-cards alignfull' }}">
	<div class="surf-block-custom-cards__inner outer-block"
	     style="background-color: {{ $backgroundColor }}; overflow: {{ $backgroundColor !== '#FFFFFF' ? 'hidden' : 'visible' }} ; color: {{ $textColor }} !important;">
		<div class="container padded">
			@if(!$block->inEditor())
				<div class="block-header">
					<h2 class="surf-block-custom-cards__title block-header__title"
					    style="color: {{ $textColor }} !important;">{!! $title !!}</h2>
					<p class="surf-block-custom-cards__subtitle block-header__intro"
					   style="color: {{ $textColor }} !important;">{!! $subtitle !!}</p>
				</div>
			@endif

			<div
					class="surf-block-custom-cards__items {{ $hideImagesOnMobile ? 'surf-block-custom-cards--hide-images-mobile' : '' }}"
					data-mobile-slider>
				{!! $content !!}
			</div>
		</div>
	</div>
</section>
