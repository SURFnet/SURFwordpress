@php
	use SURF\Enums\Theme;
	use SURF\View\ViewModels\SeparatorViewModel;

	/**
	 * @var array $blockAttributes
	 */

	$blockID =  uniqid();

	// Separator
	$separator = $blockAttributes['separator'] ?? '';
	$separatorPosition = $blockAttributes['separatorposition'] ?? '';

	// Styling
	$bgColor = $blockAttributes['bgcolor'] ?? '';
	$titleColor = $blockAttributes['titlecolor'] ?? '';
	$textColor = $blockAttributes['textcolor'] ?? '';
	$linkColor = $blockAttributes['linkcolor'] ?? '';
	$linkColorHover = $blockAttributes['linkcolorhover'] ?? '';

	// Buttons
	$buttonBgColor = $blockAttributes['buttonbgcolor'] ?? '';
	$buttonTextColor = $blockAttributes['buttontextcolor'] ?? '';
	$buttonBgColorHover = $blockAttributes['buttonbgcolorhover'] ?? '';
	$buttonTextColorHover = $blockAttributes['buttontextcolorhover'] ?? '';

	// Margin
	$blockMargin = $blockAttributes['blockmargin'] ?? '';

	$style = [];
	if ($bgColor) {
		$style[] = "background: $bgColor;";
		$style[] = "--surf-color-articles-background: $bgColor;";
		$style[] = "--surf-color-background: $bgColor;";
		$style[] = "--surf-color-outer-block-background: $bgColor;";
	}
	if ($textColor) {
		$style[] = "--surf-color-text: $textColor;";
		$style[] = "--surf-color-primary: $textColor;";
	}
	if ($linkColor) {
		$style[] = "--surf-color-link: $linkColor;";
		$style[] = "--surf-color-link-hover: $linkColorHover;";
	}
	if ($titleColor) {
		$style[] = "--surf-color-headings: $titleColor;";
		$style[] = "--surf-color-post-meta: $titleColor;";
	}
	if ($buttonBgColor) {
		$style[] = "--surf-color-button: $buttonBgColor;";
	}
	if ($buttonTextColor) {
		$style[] = "--surf-color-button-text: $buttonTextColor;";
	}
	if ($buttonBgColorHover) {
		$style[] = "--surf-color-button-hover: $buttonBgColorHover;";
	}
	if ($buttonTextColorHover) {
		$style[] = "--surf-color-button-hover-text: $buttonTextColorHover;";
	}
@endphp


<div
		@class([
			'alignfull surf-block-style-group style-group-' . $blockID,
			'surf-block-style-group--no-margin' => $blockMargin === 'none',
		])
		style="{{ implode(' ', $style) }}"
>

	@if($separator === 'standard-separator')
		@if($separatorPosition === 'top' || $separatorPosition === 'both')
			<div class="surf-block-style-group__sep">
				<x-separator/>
			</div>
		@endif
	@endif

	<div class="surf-block-style-group__inner">
		{!! $content !!}
	</div>

	@if($separator === 'standard-separator')
		@if($separatorPosition === 'bottom' || $separatorPosition === 'both')
			<div class="surf-block-style-group__sep">
				<x-separator/>
			</div>
		@endif
	@endif
</div>
