<?php

return [
	'show_admin' => fn() => function_exists( 'is_user_logged_in' )
	                        && is_user_logged_in()
	                        && function_exists( 'current_user_can' )
	                        && current_user_can( 'administrator' ),
];
