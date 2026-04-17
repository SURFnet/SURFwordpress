<?php

namespace SURF\Admin\Pages;

use Illuminate\Contracts\Container\BindingResolutionException;
use SURF\Helpers\Helper;
use SURF\Services\CspService;

/**
 * Class CspTool
 * @package SURF\Admin\Pages
 */
class CspTool
{

	public const ACTION = 'sync_csp_config';
	public ?bool $status = null;

	/**
	 * Class constructor
	 * @throws BindingResolutionException
	 */
	public function __construct()
	{
		$action = Helper::getSanitizedPost( 'action', '' );
		if ( $action === static::ACTION ) {
			check_admin_referer( static::ACTION );
			$this->status = ( new CspService() )->syncCsp();
		}
	}

	/**
	 * @return void
	 */
	public function render(): void
	{
		echo surfView( 'admin.pages.csp-tool', [ 'status' => $this->status ] );
	}

}
