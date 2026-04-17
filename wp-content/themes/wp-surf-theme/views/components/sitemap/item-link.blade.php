@php
	use SURF\Core\PostTypes\BasePost;

	/**
	 * @var BasePost $post
	 */

	$icon = $icon ?? 'file';

@endphp
<li class="surf-block-sitemap__item">
	<div class="surf-block-sitemap__post">
		<x-icon icon="{{ $icon }}" sprite="global"/>
		<a href="{{ $post->permalink() }}">{{ $post->title() }}</a>
	</div>
</li>