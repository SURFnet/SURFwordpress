<?php /** @noinspection PhpDocMissingThrowsInspection */

namespace SURF\Providers;

use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SURF\Core\Contracts\ServiceProvider;
use SURF\Core\Controllers\ArchiveController;
use SURF\Core\Controllers\TaxonomyArchiveController;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\Taxonomies\Taxonomy;

/**
 * Class TemplateControllerServiceProvider
 * @package SURF\Providers
 */
class TemplateControllerServiceProvider extends ServiceProvider
{

	/**
	 * @return void
	 */
	public function register(): void {}

	/**
	 * @return void
	 */
	public function boot(): void
	{
		$this->initArchiveControllers();
		$this->initTaxonomyArchiveControllers();
		add_filter( 'template_include', [ $this, 'handleTemplateInclude' ] );
	}

	/**
	 * @return void
	 */
	protected function initArchiveControllers(): void
	{
		// Gets al non-default archive pages
		$archives = array_merge(
			glob( SURF_THEME_DIR . "/archive-*.php" ),
			glob( SURF_THEME_DIR . "/search.php" ),
			glob( SURF_THEME_DIR . "/home.php" )
		);

		foreach ( $archives as $archive ) {
			include_once $archive;

			$class = static::getTemplateControllerClass( $archive );

			// Checks if the archive uses the ArchiveController Trait and initializes it when it has.
			if ( class_exists( $class ) && in_array( ArchiveController::class, class_uses( $class ) ) ) {
				$postType = basename( $archive ) === 'home'
					? 'post'
					: str_replace( 'archive-', '', basename( $archive, '.php' ) );

				/** @var ArchiveController $class */
				$class::init( $postType );
			}
		}
	}

	/**
	 * @return void
	 */
	protected function initTaxonomyArchiveControllers()
	{
		// Gets al non-default archive pages
		$archives = glob( SURF_THEME_DIR . "/taxonomy-*.php" );
		foreach ( $archives as $archive ) {
			include_once $archive;

			$class = static::getTemplateControllerClass( $archive );

			// Checks if the archive uses the ArchiveController Trait and initializes it when it has.
			if ( class_exists( $class ) && in_array( TaxonomyArchiveController::class, class_uses( $class ) ) ) {
				$taxonomy = str_replace( 'taxonomy-', '', basename( $archive, '.php' ) );

				/** @var TaxonomyArchiveController $class */
				$class::init( $taxonomy );
			}
		}
	}

	/**
	 * @param string $template
	 * @return void
	 */
	public function handleTemplateInclude( string $template ): void
	{
		include_once $template;

		$class = $this->getTemplateControllerClass( $template );
		if ( is_a( $class, TemplateController::class, true ) ) {
			$this->handleControllerRequest( $class );
		}

		$this->app->shutdown();
	}

	/**
	 * @param string $controller
	 * @return void
	 */
	public function handleControllerRequest( string $controller )
	{
		/** @var TemplateController $instance */
		$instance = $this->app->get( $controller );

		$result = $this->app->call( [ $instance, 'handle' ] );
		if ( $result ) {
			echo $result;
		}
	}

	/**
	 * @param string $template
	 * @return string
	 */
	public function getTemplateControllerClass( string $template )
	{
		$file      = basename( $template, '.php' );
		$namespace = 'SURF\\';
		$class     = Str::studly( $file ) . 'Controller';
		if ( $class === '404Controller' ) {
			$class = 'Error404Controller';
		}

		return $namespace . $class;
	}

}
