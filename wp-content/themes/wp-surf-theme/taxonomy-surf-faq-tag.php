<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;

/**
 * Class TaxonomySURFFaqTagController
 * @package SURF
 */
class TaxonomySURFFaqTagController extends TemplateController
{

	/**
	 * @param PostCollection $faqs
	 * @return View
	 */
	public function handle( PostCollection $faqs ): View
	{
		return $this->view( 'faq.archive-taxonomy', compact( 'faqs' ) );
	}

}
