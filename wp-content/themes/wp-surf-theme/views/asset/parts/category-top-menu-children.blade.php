@php

	/**
	 * @var array $children
	 * @var string $slug
	 * @var string $title
	 */

	if ( empty($children) ) {
		return;
	}

	$popoverId = 'asset-submenu-' . $slug;

	// The name of the parent item is added, so every toggle has its own name for screen readers
	$buttonText = __('Toggle submenu', 'wp-surf-theme');
	if ( !empty( $title ) ) {
		$buttonText .= ': ' . $title;
	}

@endphp
<button type="button" class="menu-item-button" aria-expanded="false"
	popovertarget="{{ $popoverId }}"
	popovertargetaction="toggle">
	<x-icon icon="chevron-down" sprite="global"/>
	<span class="sr-only">{{ $buttonText }}</span>
</button>
<ul class="sub-menu" id="{{ $popoverId }}" popover="true">
	<span class="top-border-left" aria-hidden="true"></span>
	<span class="top-border-right" aria-hidden="true"></span>
	@foreach( $children as $child )
		<li @class(['asset-top-menu__item', ...$child['classes'] ?? []])>
			<a href="{{ $child['url'] }}" target="_self">
				{{ $child['title'] }}
			</a>
		</li>
	@endforeach
</ul>
