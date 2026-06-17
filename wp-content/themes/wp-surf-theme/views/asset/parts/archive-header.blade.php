@php

	/**
	 * @var string $title
	 */

@endphp
<x-heading tag="h1" icon="h1" class="archive-page__title">
	{!! $title !!}
</x-heading>
<div class="archive-page__description">
	{!! $content ?? '' !!}
</div>
