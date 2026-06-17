<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\TemplateController;
use SURF\PostTypes\Asset;

/**
 * Class SingleSURFAssetController
 * @package SURF
 */
class SingleSURFAssetController extends TemplateController
{

	/**
	 * @param Asset $asset
	 * @return View
	 */
	public function handle( Asset $asset ): View
	{
		return $this->view( 'asset.single', compact( 'asset' ) );
	}

}
