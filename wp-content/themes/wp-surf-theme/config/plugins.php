<?php

use SURF\Plugins\Acf\Acf;
use SURF\Plugins\AcfPro\AcfPro;
use SURF\Plugins\CspManager\CspManager;
use SURF\Plugins\FriendlyCaptcha\FriendlyCaptcha;
use SURF\Plugins\GravityForms\GravityForms;
use SURF\Plugins\OpenIdConnect\OpenIdConnect;
use SURF\Plugins\PiwikPro\PiwikPro;
use SURF\Plugins\Polylang\Polylang;
use SURF\Plugins\PolylangPro\PolylangPro;
use SURF\Plugins\Redirection\Redirection;
use SURF\Plugins\UpdraftPlus\UpdraftPlus;
use SURF\Plugins\WpMailSmtp\WpMailSmtp;
use SURF\Plugins\Yoast\Yoast;

return [
	'installable' => [
		Acf::class,
		AcfPro::class,
		CspManager::class,
		FriendlyCaptcha::class,
		GravityForms::class,
		OpenIdConnect::class,
		PiwikPro::class,
		Polylang::class,
		PolylangPro::class,
		Redirection::class,
		UpdraftPlus::class,
		WpMailSmtp::class,
		Yoast::class,
	],
];
