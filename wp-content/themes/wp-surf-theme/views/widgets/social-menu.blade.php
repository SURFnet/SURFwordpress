@php
	use SURF\Enums\Theme;
	use SURF\Helpers\SocialHelper;

	/**
	 * @var string $title
	 * @var array $socials
	 */

	if (empty($socials)) {
		return;
	}

	$mainColumnWidth = (Theme::isSURF() ? 'span-4-lg' : 'span-3-lg');
@endphp

<div class="column span-4-sm span-4-md {{ $mainColumnWidth }}">
	<div class="footer__menu-title h5">{{ $title }}</div>
	<div class="button-group">
		@foreach($socials as $key => $link)
			<a href="{{ $link['url'] }}" class="button-group__item" target="_blank"
			   aria-label="{{ esc_attr($link['label']) }}">
				<x-icon :icon="$link['icon']" :sprite="$link['sprite']"/>
			</a>
		@endforeach
	</div>
</div>
