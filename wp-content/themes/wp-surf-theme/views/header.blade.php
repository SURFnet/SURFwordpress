@php
	use SURF\Components\WalkerPrimaryMenu;
	use SURF\Enums\Theme;
	use SURF\Helpers\SearchHelper;
	use SURF\View\ViewModels\SeparatorViewModel;
@endphp

		<!doctype html>
<html {{ language_attributes() }}>
<head>
	<meta charset="{{ bloginfo('charset') }}"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	{{ wp_head() }}

	@stack('head')
</head>

<body {{ body_class( Theme::bodyClass() ) }}>
{{ wp_body_open() }}

<a class="skip-link screen-reader-text" href="#main-content">{{ __('Skip to content', 'wp-surf-theme') }}</a>
<div class="site-main-frame">
	<header id="site-header" class="header">
		<div class="header__inner padded container">
			<div class="header__section-branding">
				<a class="header__branding" href="{{ esc_url(home_url('/')) }}" rel="home">
					<span class="sr-only">{{ bloginfo('name') }}</span>
					@empty(Theme::logos())
						<x-icon icon="surf-logo" sprite="surf" class="header__branding-logo"/>
					@else
						@foreach(Theme::logos() as $logo)
							{!! wp_get_attachment_image($logo['logo_id'], 'full', false, ['class' => 'header__custom-branding-logo header__custom-branding-logo--size-'.$logo['logo_size']]) !!}
						@endforeach
					@endif
				</a>
			</div>
			<div class="header__navigation">
				<nav class="header__top-navigation menu">
					@if(has_nav_menu('top-menu'))
						<ul id="top-menu" class="header__top-navigation-menu">
							{{
								wp_nav_menu([
									'theme_location' => 'top-menu',
									'container' => false,
									'items_wrap' => '%3$s'
								])
							}}
						</ul>
					@endif
					<x-language-switch dropdown="true"/>
					<div class="header__search">
						{!! SearchHelper::getForm(placeholder: __('Search in the site', 'wp-surf-theme')) !!}
					</div>
				</nav>
				@if(has_nav_menu('primary-menu'))
					<nav id="site-navigation" class="header__primary-navigation menu">
						<ul id="primary-menu"
						    class="header__primary-navigation-menu {{ (Theme::isPoweredBy() && !empty(SeparatorViewModel::getMenuSeparatorImage()) ? 'header__primary-navigation-menu--custom-separator' : '') }}">
							{{
								wp_nav_menu([
									'theme_location' => 'primary-menu',
									'container' => false,
									'items_wrap' => '%3$s',
									'walker'     => new WalkerPrimaryMenu()
								])
							}}
						</ul>
					</nav>
				@endif
				<div class="header__section-toggle">
					<button id="hamburger"
					        class="hamburger header__navigation-toggle"
					        aria-controls="primary-menu"
					        aria-expanded="false"
					        aria-label="{{ __('Primary Menu', 'wp-surf-theme') }}"
					>
						<span class="hamburger__part"></span>
						<span class="hamburger__part"></span>
						<span class="hamburger__part"></span>
					</button>
				</div>
			</div>
		</div>
	</header>
	@include('parts.header.mobile-menu')
	@if(!isset($disableSeparator) || !$disableSeparator)
		@if(Theme::isSURF() || Theme::isPoweredBy() && !SeparatorViewModel::hasMenuSeparator())
			<div class="header__separator container"></div>
		@else
			@php
				$image = SeparatorViewModel::getMenuSeparatorImage();
				$margins = SeparatorViewModel::getMenuSeparatorMargins();
			@endphp
			@unless(empty($image))
				<div class="header__separator header__separator--custom" style="
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
	@endif
	<main id="main-content" class="site-content">
