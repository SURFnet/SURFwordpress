@php
	use SURF\Core\Blocks\Block;
	use SURF\Core\PostTypes\PostCollection;

	/**
	 * @var array $blockAttributes
	 * @var string $content
	 * @var string $blockName
	 * @var PostCollection $posts
	 * @var Block $block
	 */

	$contactPerson = $posts->first();
	if ( !$block->inEditor() && empty($contactPerson) ) {
		return;
	}
@endphp

<div @class([
    'pointer-events-none' => $block->inEditor(),
    'surf-block surf-block-contact-person alignfull' => !$block->inEditor(),
])>
	<div class="container padded surf-block-contact-person__inner">
		<div class="surf-block-contact-person__person">
			@if(!empty($contactPerson))
				@if($contactPerson->has('pictureId'))
					<figure>
						{!! $contactPerson->pictureMarkup('avatar', ['alt' => sprintf(__('Photo of %s', 'wp-surf-theme'), $contactPerson->fullName())]) !!}
					</figure>
				@endif
				<p>{{ $contactPerson->fullName() }}</p>
			@endif
		</div>
		@if(!$block->inEditor())
			<div class="surf-block-contact-person__content">
				<h3 class="h4">{!! $blockAttributes['title'] ?? '' !!}</h3>
				<p>{!! $blockAttributes['intro'] ?? '' !!}</p>
			</div>
		@endif
	</div>
</div>
