@php
	/**
	 * @var string $icon
	 * @var string $sprite
	 * @var string $class
	 */
@endphp

<svg class="icon icon--{{ $icon }} {{ $class ?? ''}}" aria-hidden="true">
	<use xlink:href="#{{ "$sprite--$icon" }}"/>
</svg>
