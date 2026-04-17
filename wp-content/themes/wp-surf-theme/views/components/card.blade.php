@props([
    'post' => null,
    'layout' => 'block', // block, row
    'headingTag' => 'h3',
    'hideImagesOnMobile' => false
])

@php
	use SURF\Core\PostTypes\BasePost;

	/**
	 * @var BasePost $post
	 */

	if (empty($post)) return;
@endphp

<article
		@class([
			'card',
			'card--' . $layout,
			$attributes->get('class'),
		])
		{{ $attributes->except('class') }}
>
	<div @class(['card__main'])>
		<header @class(['card__header'])>
			<x-heading :tag="$headingTag" class="card__title h3">
				<a @class(['card__anchor']) href="{{ $post->permalink() }}">
					{!! $post->title() !!}
					@if(empty($category) || $layout === 'row')
						<x-icon class="card__arrow-icon" :icon="'arrow-right'" :sprite="'global'"/>
					@endif
				</a>
			</x-heading>
			@if(!empty($category) && ((!$post->hasPostThumbnail() && $layout === 'block') || ($post->hasPostThumbnail() && $hideImagesOnMobile && $layout === 'block')))
				<div @class(['card__category-in-main', 'card__category-in-main--visibile-on-mobile' => $post->hasPostThumbnail() && $hideImagesOnMobile])>
					{!! $category !!}
					<x-icon class="card__arrow-icon" :icon="'arrow-right'" :sprite="'global'"/>
				</div>
			@endif
		</header>
		<div @class(['card__excerpt'])>{!! $post->excerpt() !!}</div>
		@isset($meta)
			{!! $meta !!}
		@endisset
	</div>
	@if($post->hasPostThumbnail())
		<figure @class(['card__figure', 'card__figure--hidden-on-mobile' => $hideImagesOnMobile])>
			{!! $post->postThumbnail('post-image', [
				'class' => 'card__image',
			]) !!}
			@if(!empty($category) && $layout === 'block')
				{{ $category }}
			@endif
		</figure>
	@endif
</article>
