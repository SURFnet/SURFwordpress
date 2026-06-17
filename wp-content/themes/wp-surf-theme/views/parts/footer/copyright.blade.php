@php
	use SURF\Enums\Theme;

	$copyright = get_option('options_surf_theme_footer_copyright');

@endphp
@if(empty($copyright))
	<div class="footer__sub-column footer__powered-by">
		@if(!Theme::isSURF())
			<span class="footer__powered-by-text">{{ __('Powered by', 'wp-surf-theme') }}</span>
			<x-icon icon="surf-logo" sprite="surf" class="footer__powered-by-logo"/>
		@endif
	</div>
@else
	<div class="footer__sub-column footer__copyright">{{ $copyright }}</div>
@endif
