@php
	use SURF\Core\Taxonomies\TermCollection;

	/**
	* @var TermCollection $list
 	**/

	if ( $list->isEmpty() ) {
		return;
	}

	$tags = $list->pluck('name')->join(', ');

@endphp
<div class="{{ $class ?? '' }}">
	<dt class="sr-only">{{ __('Tags', 'wp-surf-theme') }}</dt>
	<dd>
		<x-icon icon="tag" sprite="global"/>
		<span>{{ $tags }}</span></dd>
</div>
