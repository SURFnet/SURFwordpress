<?php

namespace SURF\View\ViewModels;

use SURF\Helpers\PolylangHelper;

/**
 * Class ErrorPageViewModel
 * Loads information for the custom selected 404 page
 * @package SURF\View\ViewModels
 */
class ErrorPageViewModel
{

	/**
	 * @return false|int|mixed
	 */
	public function getPage()
	{
		return get_option( 'options_error_page' ) ?? 0;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return get_the_title( $this->getPage() ) ?: __( 'Oops! Page not found', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public function getContent(): string
	{
		return get_post_field( 'post_content', $this->getPage() ) ?? '';
	}

}
