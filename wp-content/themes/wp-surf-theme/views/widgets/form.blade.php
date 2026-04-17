@php
	use SURF\Enums\Theme;

	/**
	 * @var string $title
	 * @var string $form
	 * @var bool $showTitle
	 */

	$mainColumnWidth = (Theme::isSURF() ? 'span-4-lg' : 'span-3-lg');
@endphp

<div class="column span-4-sm span-4-md {{ $mainColumnWidth }}">
	{!! gravity_form($form, $showTitle, false, ajax: true, echo: false) !!}
</div>
