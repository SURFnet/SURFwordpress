@php

	$sitemap = $sitemap ?? [];
	if ( empty( $sitemap ) ) {
		return;
	}

	$inEditor = !empty( $inEditor );
	$title    = $title ?? __('Content', 'wp-surf-theme');
	$class    = trim( $class . ( $inEditor ? ' editor-preview' : '' ) );

@endphp
<section class="surf-block surf-block-sitemap {{ $class }}">
	@if ( !$inEditor )
		<h2 class="surf-block-sitemap__title">
			<x-icon icon="list-tree" sprite="global"/>
			{{ $title }}
		</h2>
	@endif
	<div class="surf-block-sitemap__list">
		<ul class="">
			@foreach( $sitemap as $slug => $item )
				<x-sitemap.item-node :slug="$slug" :item="$item" :level="0"/>
			@endforeach
		</ul>
	</div>
</section>
