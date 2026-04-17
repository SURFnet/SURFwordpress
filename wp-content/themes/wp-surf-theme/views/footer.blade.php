@php
	$simpleFooter = get_option('options_surf_theme_footer_simple');
@endphp

</main><!-- #main-content -->

<footer @class(['footer', 'container padded' => !$simpleFooter])>
	@if(!$simpleFooter)
		@include('parts/footer-extended')
	@else
		@include('parts/footer-simple')
	@endif
</footer>
</div>
{{ wp_footer() }}
</body>
</html>
