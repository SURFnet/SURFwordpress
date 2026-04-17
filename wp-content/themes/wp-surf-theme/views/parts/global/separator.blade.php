@php
	use SURF\Enums\Theme;
	use SURF\View\ViewModels\SeparatorViewModel;

@endphp
@if(Theme::isSURF() || Theme::isPoweredBy() && !SeparatorViewModel::hasGlobalSeparator())
	<div class="separator">
		<div class="separator__left">
			<span></span>
			<span></span>
		</div>

		<div class="separator__right">
		</div>
	</div>
@else
	@php
		$image = SeparatorViewModel::getGlobalSeparatorImage();
		$margins = SeparatorViewModel::getGlobalSeparatorMargins();
	@endphp
	@unless(empty($image))
		<div class="surf-block surf-block-custom-separator separator separator--custom" style="
            @foreach($margins as $var => $margin)
                --{{$var}}: -{{$margin}}px;
            @endforeach
            --sm-height: {{ ($image['height'] * 0.5) }}px;
            --md-height: {{ ($image['height']) * 0.75 }}px;
            --sm-width: {{ ($image['width'] * 0.5) }}px;
            --md-width: {{ ($image['width'] * 0.75) }}px;
        ">
			<img src="{{ $image['url'] }}" height="{{ $image['height'] }}" width="{{ $image['width'] }}"/>
		</div>
	@endunless
@endif
