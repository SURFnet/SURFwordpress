@props([
    'items' => [],
])

@php
	if (empty($items)) return;
@endphp

<dl @class(['meta-list'])>
	@foreach($items as $item)
		<x-meta-list.meta-list-item :class="$item['class'] ?? null" :icon="$item['icon'] ?? null"
		                            :label="$item['label'] ?? null" :value="$item['value'] ?? null"/>
	@endforeach
</dl>
