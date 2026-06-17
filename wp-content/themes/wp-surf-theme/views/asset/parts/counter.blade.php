@php

	/**
	 * @var int $count
	 */

@endphp
<div class="assets-navigation__assets">
	<x-icon icon="document" sprite="global"/>
	{!! sprintf( _n( '%1$s asset', '%1$s assets', $count, 'wp-surf-theme'), '<span class="found_item_count">' . $count . '</span>') !!}
</div>