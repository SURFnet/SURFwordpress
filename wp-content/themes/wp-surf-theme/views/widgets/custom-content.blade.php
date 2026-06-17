@php
	use SURF\Enums\Theme;

	/**
	 * @var string $title
	 * @var string $text
	 */

	$mainColumnWidth = (Theme::isSURF() ? 'span-4-lg' : 'span-3-lg');
@endphp

<div class="column span-4-sm span-4-md {{ $mainColumnWidth }}">
	@if($title)
		<div class="footer__menu-title h5">{{ $title }}</div>
	@endif

	<div>{!! $text !!}</div>
</div>
