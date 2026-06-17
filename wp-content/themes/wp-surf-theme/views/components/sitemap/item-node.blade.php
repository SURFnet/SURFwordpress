@php
	/**
	 * @var string $slug
	 * @var array $item
	 * @var int $level
	 */

	$children  = $item['children'] ?? [];
	$post_list = $item['posts'] ?? [];
	if ( empty( $children ) && empty( $post_list ) ) {
		return;
	}

	if ( empty( $level ) ) {
		$level = 0;
	}

	$icon = !empty( $children ) ? 'folders' : 'folder';
	if ( $level === 0 ) {
		$icon = 'layer-group';
	}

@endphp
<li class="surf-block-sitemap__item">
	<details id="{{ $slug }}" data-level="{{ $level }}" {{ $level === 0 ? 'open' : '' }}>
		<summary>
			<x-icon icon="{{ $icon }}" sprite="global"/>
			{{ $item['title'] ?? $item['term']->name }}
		</summary>
		<ul class="">
			@foreach( $children as $child_slug => $child_item )
				<x-sitemap.item-node :slug="$child_slug" :item="$child_item" :level="$level + 1"/>
			@endforeach
			@foreach( $post_list as $post )
				<x-sitemap.item-link :post="$post"/>
			@endforeach
		</ul>
	</details>
</li>