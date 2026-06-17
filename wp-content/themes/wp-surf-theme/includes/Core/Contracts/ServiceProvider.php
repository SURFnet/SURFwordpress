<?php

namespace SURF\Core\Contracts;

use SURF\Application;

/**
 * Interface ServiceProvider
 * Contract for service providers within the SURF application
 * @package SURF\Core\Contracts
 */
abstract class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

	/**
	 * The application instance.
	 * @var Application
	 */
	protected $app;

}
