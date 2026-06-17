<?php

namespace SURF\Services;

use Exception;
use SURF\PostTypes\Page;
use WP_User;

/**
 * Class DefaultContentService
 * @package SURF\Services
 */
class DefaultContentService
{

	/**
	 * @param string $email
	 * @param string $password
	 * @return WP_User|null
	 */
	public function createAccount( string $email, string $password ): ?WP_User
	{
		$userId = wp_create_user( $email, $password, $email );
		if ( is_wp_error( $userId ) ) {
			return null;
		}
		$user = get_userdata( $userId );
		$user->set_role( 'administrator' );

		return $user;
	}

	/**
	 * @return void
	 */
	public function createMenus(): void
	{
		$this->createMenu( 'Hoofdmenu', 'primary-menu', [
			'Home'       => Page::query( [ 'name' => 'home' ] )->firstOrFail(),
			'Berichten'  => Page::query( [ 'name' => 'nieuws' ] )->firstOrFail(),
			'Contact'    => Page::query( [ 'name' => 'contact' ] )->firstOrFail(),
			'Copyright'  => Page::query( [ 'name' => 'copyright' ] )->firstOrFail(),
			'Disclaimer' => Page::query( [ 'name' => 'disclaimer' ] )->firstOrFail(),
		] );

		$this->createMenu( 'Footermenu', 'footer-menu', [
			'Copyright'  => Page::query( [ 'name' => 'copyright' ] )->firstOrFail(),
			'Disclaimer' => Page::query( [ 'name' => 'disclaimer' ] )->firstOrFail(),
		] );

		$this->createMenu( 'Algemeen', 'footer-first-column-menu', [
			'Algemene voorwaarden' => Page::query( [ 'name' => 'algemene-voorwaarden' ] )->firstOrFail(),
			'Privacy'              => Page::query( [ 'name' => 'privacy' ] )->firstOrFail(),
			'Contact'              => Page::query( [ 'name' => 'contact' ] )->firstOrFail(),
		] );
	}

	/**
	 * @param string $name
	 * @param string $location
	 * @param array $items
	 * @return int|null
	 */
	public function createMenu( string $name, string $location, array $items = [] ): ?int
	{
		if ( $term = get_term_by( 'name', $name, 'nav_menu' ) ) {
			return $term->term_id;
		}

		$menu                   = wp_create_nav_menu( $name );
		$locations              = get_theme_mod( 'nav_menu_locations' );
		$locations[ $location ] = $menu;
		set_theme_mod( 'nav_menu_locations', $locations );

		foreach ( $items as $title => $page ) {
			$this->createMenuItem( $menu, $title, $page );
		}

		return $menu;
	}

	/**
	 * @param int $menuId
	 * @param string $title
	 * @param Page $page
	 * @return void
	 */
	public function createMenuItem( int $menuId, string $title, Page $page ): void
	{
		wp_update_nav_menu_item( $menuId, 0, [
			'menu-item-title'     => $title,
			'menu-item-type'      => 'post_type',
			'menu-item-object-id' => $page->ID,
			'menu-item-object'    => 'page',
			'menu-item-status'    => 'publish',
		] );
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public function createPages(): void
	{
		$this->createPage( 'Contact', 'contact', surfPath( 'content/contact.html' ) );
		$this->createPage( 'Downloads', 'downloads', surfPath( 'content/downloads.html' ) );
		$this->createPage( 'Evenementen', 'evenementen', surfPath( 'content/events.html' ) );
		$this->createPage( 'Home', 'home', surfPath( 'content/home.html' ) );
		$this->createPage( 'Nieuws', 'nieuws', surfPath( 'content/news.html' ) );
		$this->createPage( 'Privacy', 'privacy', surfPath( 'content/privacy.html' ) );
		$this->createPage( 'Algemene voorwaarden', 'algemene-voorwaarden', surfPath( 'content/terms-and-conditions.html' ) );
		$this->createPage( 'Copyright', 'copyright', surfPath( 'content/copyright.html' ) );
		$this->createPage( 'Disclaimer', 'disclaimer', surfPath( 'content/disclaimer.html' ) );
	}

	/**
	 * @param string $title
	 * @param string $slug
	 * @param string $contentPath
	 * @return void
	 * @throws Exception
	 */
	public function createPage( string $title, string $slug, string $contentPath ): void
	{
		if ( Page::query( [ 'name' => $slug ] )->first() ) {
			return;
		}

		if ( !is_readable( $contentPath ) ) {
			throw new Exception( sprintf( __( 'File does not exists: %s', 'wp-surf-theme' ), $contentPath ) );
		}

		Page::create( [
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => file_get_contents( $contentPath ),
			'post_status'  => 'publish',
		] );
	}

	/**
	 * @return void
	 */
	public function setupOptions(): void
	{
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', Page::query( [ 'name' => 'home' ] )->firstOrFail()->ID );
		update_option( 'page_for_posts', Page::query( [ 'name' => 'nieuws' ] )->firstOrFail()->ID );
		update_option( 'page_for_surf-agenda', Page::query( [ 'name' => 'evenementen' ] )->firstOrFail()->ID );
		update_option( 'page_for_surf-download', Page::query( [ 'name' => 'downloads' ] )->firstOrFail()->ID );
	}

	/**
	 * @return void
	 */
	public function setupRewriteRules(): void
	{
		require_once ABSPATH . '/wp-admin/includes/misc.php';

		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		flush_rewrite_rules( true );
	}

}
