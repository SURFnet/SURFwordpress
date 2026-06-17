@php
	/**
	 * @var ?string $type
	 * @var string $message
	 * @var string $class
	 */
@endphp

<div class="notice {{$type ? "notice-{$type}" : ''}} {{$class ?? ''}}">
	<p>{!! $message !!}</p>
</div>
