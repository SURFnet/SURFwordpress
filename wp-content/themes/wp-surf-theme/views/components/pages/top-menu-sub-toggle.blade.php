@props(['label' => ''])
@php
	// The name of the parent item is added, so every toggle has its own name for screen readers
	$buttonText = __('Toggle submenu', 'wp-surf-theme');
	if ( !empty( $label ) ) {
		$buttonText .= ': ' . $label;
	}
@endphp
<button class="menu-item-button" aria-expanded="false">
	<x-icon icon="chevron-down" sprite="global"/>
	<span class="sr-only">{{ $buttonText }}</span>
</button>
