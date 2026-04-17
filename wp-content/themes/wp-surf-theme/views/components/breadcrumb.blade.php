@php
	use SURF\Helpers\BreadcrumbsHelper;

	if ( !BreadcrumbsHelper::shouldShow() ) {
		return;
	}

	$spacer      = [];
	$show_spacer = ( !empty( $spacer ) && in_array( get_post_type(), $spacer ) ) || is_page_template();

@endphp
{!! yoast_breadcrumb( '<div id="breadcrumbs" class="breadcrumbs">','</div>' ) !!}
@if( $show_spacer )
	<div class="breadcrumbs__spacer"></div>
@endif
