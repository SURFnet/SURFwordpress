<?php

$view_paths = [ SURF_THEME_DIR . '/views' ];
if ( function_exists( 'apply_filters' ) ) {
	$view_paths = apply_filters( 'surf_views_paths', $view_paths );
}

return [
	'paths'    => $view_paths,
	'compiled' => SURF_THEME_DIR . '/cache/views',
];
