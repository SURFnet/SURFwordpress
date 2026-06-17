<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use SURF\Core\Controllers\TemplateController;
use SURF\PostTypes\Asset;
use SURF\PostTypes\Page;
use SURF\Services\PostTypeSitemapService;

/**
 * Template Name: Asset Sitemap template
 */
_x( 'Asset Sitemap template', 'template', 'wp-surf-theme' );

/**
 * Class AssetSitemapTemplateController
 * @package SURF
 */
class AssetSitemapTemplateController extends TemplateController
{

	/**
	 * @param Request $request
	 * @param Page $page
	 * @return View
	 */
	public function handle( Request $request, Page $page ): View
	{
		$hide_empty = !empty( $request->get( 'hide_empty', false ) );
		$sitemap    = PostTypeSitemapService::build( Asset::class, $hide_empty );

		return $this->view( 'page-templates.sitemap-template', compact( 'page', 'sitemap' ) );
	}

}
