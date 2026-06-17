<?php

namespace SURF\Admin\Pages;

/**
 * Class SetupWizard
 * @package SURF\Admin\Pages
 */
class SetupWizard
{

	/**
	 * @return void
	 */
	public function render(): void
	{
		echo surfView( 'admin.pages.setup-wizard' );
	}

}
