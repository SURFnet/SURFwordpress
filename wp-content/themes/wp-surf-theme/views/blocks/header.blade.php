@php
	use SURF\Helpers\SearchHelper;

	/**
	 * @var array $blockAttributes
	 */

	$variation = $blockAttributes['variation'] ?? 'text';
	$autoHeight = $blockAttributes['height'] ?? false;
	$autoHeight = $autoHeight === 'auto';
	$className = $blockAttributes['className'] ?? '';
	$search = $blockAttributes['search'] ?? 'no-search';
	$placeholder = $blockAttributes['placeholder'] ?? __('Search', 'wp-surf-theme');
	$forgroundBackgroundVideoURL = $blockAttributes['videobackgroundurl'] ?? '';
	$backgroundVideoURL = $blockAttributes['videourl'] ?? '';
	$backgroundColor = $blockAttributes['backgroundcolor'] ?? '';
	$backgroundImage = $blockAttributes['backgroundimage'] ?? '';
	$searchForm = SearchHelper::getForm(placeholder: $placeholder);
	$blockMargin = $blockAttributes['blockmargin'] ?? '';
	$backgroundType = $blockAttributes['backgroundtype'] ?? '';
@endphp
<div @class([
        'alignfull surf-block-header__wrapper',
        'surf-block-style-group--no-margin' => $blockMargin === 'none',
        'surf-block-header__wrapper--has-background' => ($backgroundVideoURL || $backgroundColor || $backgroundImage)
    ])
     @if(!empty($backgroundColor) && $backgroundType === 'color')
	     style="background-color: {{ $backgroundColor }};"
		@endif
>
	@if(!empty($backgroundVideoURL) && $backgroundType === 'video')
		<div class="surf-block-header__background">
			<video autoplay loop muted playsinline="" webkit-playsinline="">
				<source src="{{ $backgroundVideoURL }}" type="video/mp4"/>
			</video>
		</div>
	@endif

	@if(!empty($blockAttributes['backgroundimage']) && $backgroundType === 'image')
		<div class="surf-block-header__background surf-block-header__background--image">
			<img src="{{$blockAttributes['backgroundimage']['sizes']['full']['url']}}">
		</div>
	@endif
	<div @class(['wp-block-surf-header surf-block surf-block-header surf-block-header--' . $variation,
        'surf-block-header--auto-height' => $autoHeight,
        $className,
        ])>
		<div class="surf-block-header__content surf-block-header--{{ $search }}">
			@if($search === 'search-top' && $variation === 'background-image' || $search === 'search-top' && $variation === 'background-video' || $search === 'search-top' && $variation === 'background-video-gradient')
				{!! $searchForm !!}
			@endif
			<h1 class="surf-block-header__title">{{$blockAttributes['title']}}</h1>
			@if(!empty($blockAttributes['intro']))
				<p class="surf-block-header__text">{!! $blockAttributes['intro'] !!}</p>
			@endif
			@if($search === 'search-bottom' && $variation === 'background-image' || $search === 'search-bottom' && $variation === 'background-video' || $search === 'search-bottom' && $variation === 'background-video-gradient')
				{!! $searchForm !!}
			@endif
		</div>
		@if(!empty($blockAttributes['image']) && $variation === 'background-image')
			<div class="surf-block-header__image">
				<img src="{{$blockAttributes['image']['sizes']['hero-large']['url']}}">
			</div>
		@endif
		@if(!empty($blockAttributes['videobackgroundurl']) && $variation === 'background-video' || !empty($blockAttributes['videobackgroundurl']) && $variation === 'background-video-gradient')
			<div class="surf-block-header__image">
				<video autoplay loop muted playsinline="" webkit-playsinline="">
					<source src="{{ $forgroundBackgroundVideoURL }}" type="video/mp4"/>
				</video>
			</div>
		@endif
		@if(!empty($blockAttributes['image']) && $variation === 'small-image')
			<div class="surf-block-header__image">
				<img src="{{$blockAttributes['image']['sizes']['hero-small']['url']}}">
			</div>
		@endif
		@if($variation === 'text')
			@include('parts.global.separator')
		@endif
	</div>
</div>
