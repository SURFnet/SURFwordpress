@php
	use SURF\Core\Blocks\Block;
	use SURF\Core\PostTypes\PostCollection;

	/**
	 * @var array $blockAttributes
	 * @var string $content
	 * @var boolean $hideImagesOnMobile
	 * @var string $blockName
	 * @var PostCollection $vacancies
	 * @var Block $block
	 */

	$i = 0;
	if ( !$block->inEditor() && $vacancies->isEmpty() ) {
		return;
	}
@endphp

<section class="{{ $block->inEditor() ? 'pointer-events-none' : 'surf-block surf-block-latest-vacancies alignfull' }}">
	<div class="container padded">
		@if(!$block->inEditor())
			<div class="block-header">
				<h2 class="surf-block-latest-vacancies__title block-header__title">{!! $blockAttributes['title'] ?? '' !!}</h2>
				<p class="surf-block-latest-vacancies__intro block-header__intro">{!! $blockAttributes['intro'] ?? '' !!}</p>
			</div>
		@endif

		<div class="surf-block-latest-vacancies__grid">
			@foreach( $vacancies as $vacancy )
				@include( 'post.item', [
					'post' => $vacancy,
					'label' => __('View vacancy', 'wp-surf-theme'),
					'type' => ($i == 0 ? 'large' : 'row'), '
					hideImagesOnMobile' => $hideImagesOnMobile]
				)
				@include( 'vacancy.schema', compact('vacancy') )
				@php $i++; @endphp
			@endforeach

			<div class="surf-block-latest-vacancies__bottom">
				{!! $content !!}
			</div>
		</div>
	</div>
</section>

