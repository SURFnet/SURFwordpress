<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use SURF\Core\Controllers\TemplateController;
use SURF\PostTypes\Faq;
use SURF\PostTypes\Page;
use SURF\Services\PostTypeSitemapService;

/**
 * Template Name: FAQ Sitemap template
 */
_x( 'FAQ Sitemap template', 'template', 'wp-surf-theme' );

/**
 * Class FaqSitemapTemplateController
 * @package SURF
 */
class FaqSitemapTemplateController extends TemplateController
{

	/**
	 * @param Request $request
	 * @param Page $page
	 * @return View
	 */
	public function handle( Request $request, Page $page ): View
	{
		$hide_empty = !empty( $request->get( 'hide_empty', false ) );
		$sitemap    = PostTypeSitemapService::build( Faq::class, $hide_empty );

		return $this->view( 'page-templates.sitemap-template', compact( 'page', 'sitemap' ) );
	}

}
