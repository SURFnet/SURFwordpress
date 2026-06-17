<?php

namespace SURF\View\Components;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;

/**
 * Class DevNotice
 * @package SURF\View\Components
 */
class DevNotice extends Component
{

	public ?string $title;

	/**
	 * @param string|null $title
	 */
	public function __construct( string $title = null )
	{
		$this->title = $title;
	}

	/**
	 * @return View|void
	 */
	public function render()
	{
		if ( surfApp()->isDevelopment() || surfApp()->isLocal() ) {
			return surfView( 'components.dev-notice' );
		}
	}

}
