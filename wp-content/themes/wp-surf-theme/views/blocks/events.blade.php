@php
	use SURF\Core\PostTypes\PostCollection;
	use SURF\Core\Blocks\Block;
	use SURF\PostTypes\Agenda;

	/**
	 * @var array $blockAttributes
	 * @var string $content
	 * @var boolean $hideImagesOnMobile
	 * @var string $blockName
	 * @var PostCollection $posts
	 * @var Block $block
	 */

@endphp

<section class="{{ $block->inEditor() ? 'pointer-events-none' : 'surf-block surf-block-events alignfull' }}">
	<div class="surf-block-downloads__inner outer-block">
		<div class="container padded">
			@if(!$block->inEditor())
				<div class="block-header">
					<h2 class="surf-block-events__title block-header__title">{!! $blockAttributes['title'] ?? '' !!}</h2>
					<p class="surf-block-events__intro block-header__intro">{!! $blockAttributes['intro'] ?? '' !!}</p>
				</div>
			@endif

			<div class="surf-block-events__items grid">
				@foreach($events as $event)
					<div class="column span-4-sm span-4-md span-4-lg">
						@include('agenda.item', ['event' => $event, 'hideImagesOnMobile' => $hideImagesOnMobile])
					</div>
				@endforeach
			</div>

			@if(!$block->inEditor())
				<div class="surf-block-events__bottom block-footer">
					<a href="{{ Agenda::getArchiveLink() }}" class="button has-red-background-color">
						{{ $blockAttributes['buttonText'] ?? __('View more events', 'wp-surf-theme') }}
					</a>
				</div>
			@endif
		</div>
	</div>
</section>
