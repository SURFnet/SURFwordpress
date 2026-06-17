<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;

/**
 * Class TaxonomySURFAgendaLocationController
 * @package SURF
 */
class TaxonomySURFAgendaLocationController extends TemplateController
{

	/**
	 * @param PostCollection $events
	 * @return View
	 */
	public function handle( PostCollection $events ): View
	{
		return $this->view( 'agenda.archive-location', compact( 'events' ) );
	}

}
