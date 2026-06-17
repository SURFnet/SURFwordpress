@php
	use SURF\Core\PostTypes\BasePost;

	/**
	 * @var BasePost $post
	 */
@endphp

<article id="post-{{$post->ID()}}" {!! $post->postClass('entry entry--single') !!}>
	<div class="entry__header container padded">
		<h1 class="entry__title">
			{!! surfGetHeadingIcon('h1') !!}
			{!! $post->title() !!}
		</h1>
		<ul class="entry__meta">
			@if(method_exists($post, 'categories'))
				<li class="entry__meta-category">
					<x-icon icon="newspaper" sprite="global"/>
					{{ $post->categories()->pluck('name')->join(', ') }}
				</li>
			@endif
			<li>
				<x-post-date postId="{{ $post->ID() }}"/>
			</li>
		</ul>
		<div class="entry__figure">{!! $post->postThumbnail('post-image-full') !!}</div>
	</div>
	<div class="entry__inner padded container">
		{!! $post->content(sprintf(__('Continue reading %s', 'wp-surf-theme'), "<span class='screen-reader-text'>{$post->title()}</span>")) !!}
		<div class="entry__tags tags-list">
			{!! $post->tags(' ') !!}
		</div>
	</div>
</article><!-- #post-{{ $post->ID() }} ?> -->

@if(is_singular())
	{!! $post->editPostLink(text: _x('Edit', 'admin', 'wp-surf-theme'), class: 'post-edit-link button') !!}
@endif
