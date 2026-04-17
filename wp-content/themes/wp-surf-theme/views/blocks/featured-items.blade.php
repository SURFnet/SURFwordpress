@php
	use SURF\Core\Blocks\Block;
	use SURF\Core\PostTypes\PostCollection;

	/**
	 * @var array $blockAttributes
	 * @var string $content
	 * @var boolean $hideImagesOnMobile
	 * @var string $blockName
	 * @var PostCollection $posts
	 * @var Block $block
	 */

	$layout = ($blockAttributes['layout'] ?? '') === 'auto' ? 'auto' : 'simple';
	$i = 0;
	$count = count($posts);
	$hideCategories = $blockAttributes['hideCategories'] ?? false;
@endphp

<section @class([
    'pointer-events-none' => $block->inEditor(),
    'surf-block surf-block-featured-items alignfull' => !$block->inEditor(),
    "layout--$layout",
    'hide-dates' => $blockAttributes['hideDates'] ?? false,
    'hide-categories' => $hideCategories,
])>
	<div class="container padded">
		@if(!$block->inEditor())
			<div class="block-header">
				<h2 class="surf-block-featured-items__title block-header__title">{!! $blockAttributes['title'] ?? '' !!}</h2>
				<p class="surf-block-featured-items__intro block-header__intro">{!! $blockAttributes['intro'] ?? '' !!}</p>
			</div>
		@endif

		@if($layout === 'simple')
			<div class="surf-block-featured-items__items" data-mobile-slider>
				@foreach($posts as $post)
					<div class="slider-item">
						@include('post.item', ['post' => $post, 'type' => 'block', 'hideCategories' => $hideCategories, 'hideImagesOnMobile' => $hideImagesOnMobile])
					</div>
				@endforeach
			</div>
		@endif

		@if($layout === 'auto')
			<div class="surf-block-articles__grid" data-posts="{{ $count }}">
				@foreach($posts as $post)
					@if($count == 1 || $count == 2)
						@include('post.item', ['post' => $post, 'type' => 'large', 'hideCategories' => $hideCategories, 'hideImagesOnMobile' => $hideImagesOnMobile])
					@endif
					@if($count == 3)
						@include('post.item', ['post' => $post, 'type' => ($i == 0 ? 'large' : 'row'), 'hideCategories' => $hideCategories, 'hideImagesOnMobile' => $hideImagesOnMobile])
					@endif
					@if($count > 3)
						@include('post.item', ['post' => $post, 'type' => 'block', 'hideCategories' => $hideCategories, 'hideImagesOnMobile' => $hideImagesOnMobile])
					@endif
					@php $i++; @endphp
				@endforeach
			</div>
		@endif

		<div class="surf-block-featured-items__bottom block-footer">
			{!! $content !!}
		</div>
	</div>
</section>
