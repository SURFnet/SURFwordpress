@php
	use SURF\Blocks\Sitemap;

	/**
	 * @var array $blockAttributes
	 * @var string $blockName
	 * @var string $content
	 * @var Sitemap $block
	 */

	$title        = $blockAttributes['title'] ?? __('Content', 'wp-surf-theme');
	$post_type    = $blockAttributes['postType'] ?? null;
	$hide_empty   = (bool) ($blockAttributes['hideEmpty'] ?? true);
	$primary_only = (bool) ($blockAttributes['primaryOnly'] ?? false);

	$tree    = Sitemap::getTree( $post_type, $hide_empty, $primary_only );
	$message = is_admin() ? _x('No items found for this post type.', 'admin', 'wp-surf-theme') : '';

@endphp
@if( empty( $tree ) && !empty( $message ) )
	<p class="surf-sitemap-empty">{{ $message }}</p>
@endif
<x-sitemap.list
	:title="$title"
	:sitemap="$tree"
	:inEditor="$block->inEditor()"
/>