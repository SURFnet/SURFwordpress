<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\TemplateController;
use SURF\PostTypes\Faq;

/**
 * Class SingleSURFFaqController
 * @package SURF
 */
class SingleSURFFaqController extends TemplateController
{

	/**
	 * @param Faq $faq
	 * @return View
	 */
	public function handle( Faq $faq ): View
	{
		$related = $faq->relatedQuestions();

		return $this->view( 'faq.single', compact( 'faq', 'related' ) );
	}

}
