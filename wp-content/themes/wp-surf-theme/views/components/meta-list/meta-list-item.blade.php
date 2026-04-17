@props([
    'class' => null,
    'icon' => null,
    'label' => null,
    'value' => null,
])

<div @class([$class ?: ''])>
	<dt @class(['sr-only'])>{{ $label }}</dt>
	<dd>
		@if($icon)
			<x-icon :icon="$icon" :sprite="'global'"/>
		@endif
		{{ $value }}
	</dd>
</div>
