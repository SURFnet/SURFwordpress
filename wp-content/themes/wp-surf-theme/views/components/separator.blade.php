@php
	use SURF\Enums\Theme;
	use SURF\View\ViewModels\SeparatorViewModel;

@endphp

@if(Theme::isSURF() || Theme::isPoweredBy() && !SeparatorViewModel::hasGlobalSeparator())
	<div class="surf-block surf-block-separator">
		<div class="surf-block-separator__left">
			<span></span>
			<span></span>
		</div>
		<div class="surf-block-separator__right">
			<span></span>
			<span></span>
		</div>
	</div>

@else
	@php
		$image = SeparatorViewModel::getGlobalSeparatorImage();
		$margins = SeparatorViewModel::getGlobalSeparatorMargins();
		$nomarge = SeparatorViewModel::hasGlobalSeparatorNoMargin();
	@endphp
	@unless(empty($image))
		<div @class([
            'surf-block surf-block-custom-separator alignfull',
            'surf-block-custom-separator--nomarge' => $nomarge,
        ]) style="
            @foreach($margins as $var => $margin)
                @if($nomarge)
                    --{{$var}}: 0px;
                @else
                    --{{$var}}: -{{$margin}}px;
                @endif
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
