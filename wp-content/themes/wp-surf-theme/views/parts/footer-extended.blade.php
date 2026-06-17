@php
	use SURF\Helpers\SocialHelper;
	use SURF\Enums\Theme;

	$isSURF         = Theme::isSURF();
	$socialLinks    = SocialHelper::getFollowList();
	$hasFirstColumn = $isSURF || !empty( $socialLinks );
	$useWidgets     = surfShowNewFooter();

	// Different options:
	// - if is SURF site: footer with branding
	// - if not (aka is Powered by SURF): footer without branding, but optional third column for menu

@endphp
<section class="footer__main">
	<div class="container grid">
		@if( $hasFirstColumn )
			<div @class([
				'footer__column column',
				'span-4-sm',
				$isSURF ? 'span-3-md' : 'span-8-md',
				$isSURF ? 'span-5-lg' : 'span-4-lg',
			])>
				@if( $isSURF )
					<!-- Not visible on Powered by SURF -->
					<div class="footer__branding">
						<x-icon icon="surf-logo" sprite="surf" class="footer__branding-logo"/>
						<span class="footer__branding-slogan">{{ __('Driving innovation together', 'wp-surf-theme') }}</span>
					</div>
				@endif
				@if( !empty( $socialLinks ) )
					<div class="footer__socials">
						<x-social-links :socialLinks="$socialLinks" headingClass="screen-reader-text" title="{{ __('Follow us', 'wp-surf-theme') }}"/>
					</div>
				@endif
			</div>
		@endif
		<div @class([
			'footer__column column',
			'span-4-sm',
			$hasFirstColumn ? 'span-5-md' : 'span-8-md',
			$hasFirstColumn ? ($isSURF ? 'span-7-lg' : 'span-8-lg') : 'span-12-lg',
		])>
			<div class="footer__menus grid">
				@if( $useWidgets )
					@php dynamic_sidebar('footer') @endphp
				@else
					@include( 'parts.footer.menu-columns' )
				@endif
			</div>
		</div>
	</div>
</section>
<div class="footer__sub container padded">
	@if( has_nav_menu( 'footer-menu' ) )
		<div class="footer__sub-column footer__sub-menu">
			<nav class="footer-menu">
				<ul id="footer-menu" class="footer__sub-navigation">
					{{ wp_nav_menu( [
						'theme_location' => 'footer-menu',
						'container'      => false,
						'items_wrap'     => '%3$s'
					] ) }}
				</ul>
			</nav>
		</div>
	@endif
	@include( 'parts.footer.copyright' )
</div>
