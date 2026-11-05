<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Mode
	|--------------------------------------------------------------------------
	|
	| "enforce" sends Content-Security-Policy, "report-only" sends
	| Content-Security-Policy-Report-Only and "disabled" sends nothing.
	| Can be overridden per site with the SURF_CSP_MODE constant.
	|
	*/
	'mode'       => 'enforce',

	/*
	|--------------------------------------------------------------------------
	| Directives
	|--------------------------------------------------------------------------
	|
	| The base policy, shared by every context. A directive with an empty
	| list of sources is left out of the header.
	|
	*/
	'directives' => [
		'connect-src' => [
			"'self'",
			'api.friendlycaptcha.com',
			'api.redirect.li',
			'eu-api.friendlycaptcha.eu',
			'eu.frcapi.com',
			'surfnl.containers.piwik.pro',
			'surfnl.piwik.pro',
			'yoast.com',
		],
		'default-src' => [
			"'self'",
		],
		'font-src'    => [
			"'self'",
			'data:',
			'surfnl.containers.piwik.pro',
			'surfnl.piwik.pro',
		],
		'frame-src'   => [
			"'self'",
			'*.openstreetmap.org',
			'*.vimeo.com',
			'*.youtube.com',
			'eu.frcapi.com',
		],
		'img-src'     => [
			"'self'",
			'*.gravatar.com',
			'data:',
			'ps.w.org',
			'surfnl.containers.piwik.pro',
			'surfnl.piwik.pro',
		],
		'script-src'  => [
			"'self'",
			"'unsafe-eval'",
			"'unsafe-inline'",
			"'wasm-unsafe-eval'",
			'*.cloudflare.com',
			'api.friendlycaptcha.com',
			'eu-api.friendlycaptcha.eu',
			'eu.frcapi.com',
			'surfnl.containers.piwik.pro',
			'surfnl.piwik.pro',
		],
		'style-src'   => [
			"'self'",
			"'unsafe-inline'",
			'surfnl.containers.piwik.pro',
			'surfnl.piwik.pro',
			'yoast.com',
		],
		'worker-src'  => [
			'blob:',
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Contexts
	|--------------------------------------------------------------------------
	|
	| Per context ("admin" or "front") you can add sources to a directive
	| or replace a directive entirely. Replacing with an empty list removes
	| the directive from the header for that context.
	|
	| Example:
	| 'front' => [
	|     'add'     => [ 'frame-src' => [ 'player.example.com' ] ],
	|     'replace' => [ 'script-src' => [ "'self'" ] ],
	| ],
	|
	*/
	'contexts'   => [
		'admin' => [
			'add'     => [],
			'replace' => [],
		],
		'front' => [
			'add'     => [],
			'replace' => [],
		],
	],

];
