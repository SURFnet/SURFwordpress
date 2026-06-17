@php
	use SURF\Core\Blocks\Block;
	use SURF\Core\PostTypes\PostCollection;

	/**
	 * @var array $blockAttributes
	 * @var string $content
	 * @var boolean $hideImagesOnMobile
	 * @var string $blockName
	 * @var PostCollection $posts
	 * @var bool $hasMorePosts
	 * @var Block $block
	 * @var string $archiveLink
	 * @var string $dateDisplay
	 */

	$i = 0;
	$count = $blockAttributes['count'];
	$layout = ($blockAttributes['layout'] ?? '') === 'auto' ? 'auto' : 'simple';
	$dateDisplay = $blockAttributes['dateDisplay'] ?? 'default';
@endphp

<section class="{{ $block->inEditor() ? 'pointer-events-none' : 'surf-block surf-block-articles alignfull' }}">
	<div class="container padded">
		@if(!$block->inEditor())
			<div class="block-header">
				<h2 class="surf-block-articles__title block-header__title">{!! $blockAttributes['title'] ?? '' !!}</h2>
				<p class="surf-block-articles__intro block-header__intro">{!! $blockAttributes['intro'] ?? '' !!}</p>
			</div>
		@endif
		@if($layout === 'simple')
			<div class="surf-block-articles__grid" data-posts="6">
				@foreach($posts as $post)
					@include('post.item', [
						'post' => $post,
						'type' => 'block',
						'dateDisplay' => $dateDisplay
					])
				@endforeach

				@if(!$block->inEditor())
					<div class="surf-block-articles__bottom">
						<a href="{{ $archiveLink }}" class="button">
							{{ $blockAttributes['buttonText'] ?? __('Read more articles', 'wp-surf-theme') }}
						</a>
					</div>
				@endif
			</div>
		@endif
		@if($layout === 'auto')
			<div class="surf-block-articles__grid" data-posts="3">
				@foreach($posts as $post)
					@if($i < 3)
						@include('post.item', [
							'post' => $post,
							'type' => ($i == 0 ? 'large' : 'row'),
							'dateDisplay' => $dateDisplay
						])
					@endif
					@php $i++; @endphp
				@endforeach

				@if($hasMorePosts && !$block->inEditor() && $blockAttributes['count'] <= 3)
					<div class="surf-block-articles__bottom">
						<a href="{{ $archiveLink }}" class="button">
							{{ $blockAttributes['buttonText'] ?? __('Read more articles', 'wp-surf-theme') }}
						</a>
					</div>
				@endif
			</div>
			<div class="surf-block-articles__grid surf-block-articles__grid--offset" data-posts="4">
				@if($blockAttributes['count'] > 3)
					@php $i = 0; @endphp
					@foreach($posts as $post)
						@if($i >= 3)
							@include('post.item', [
								'post' => $post,
								'type' => 'block',
								'hideImagesOnMobile' => $blockAttributes['hideImagesOnMobile'],
								'dateDisplay' => $dateDisplay
							])
						@endif
						@php $i++; @endphp
					@endforeach

					@if($hasMorePosts && !$block->inEditor())
						<div class="surf-block-articles__bottom">
							<a href="{{ $archiveLink }}" class="button">
								{{ $blockAttributes['buttonText'] ?? __('Read more articles', 'wp-surf-theme') }}
							</a>
						</div>
					@endif
				@endif
			</div>
		@endif
	</div>
</section>
