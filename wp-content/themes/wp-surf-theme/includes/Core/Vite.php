<?php

namespace SURF\Core;

use Illuminate\Support\Collection;

/**
 * Class Vite
 * @package SURF\Core
 */
class Vite
{

	protected ?array  $manifest = null;
	protected ?string $hot      = null;
	protected string  $out      = '/dist';
	protected string  $base     = '/wp-content/themes/wp-surf-theme/dist/';

	public function __construct()
	{
		$this->loadManifest();
		$this->loadHot();
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public function assetPath( string $path = '' ): string
	{
		return surfPath( $this->out ) . ( $path ? '/' . $path : $path );
	}

	/**
	 * @param string $path
	 * @param bool $usePath
	 * @return string
	 */
	public function asset( string $path = '', bool $usePath = false ): string
	{
		$base = $this->isHot()
			? $this->hot . rtrim( $this->base, '/' )
			: ( $usePath ? get_template_directory() : get_template_directory_uri() ) . $this->out;

		return $base . ( $path ? '/' . $path : $path );
	}

	/**
	 * @return void
	 */
	public function loadManifest(): void
	{
		$file = $this->assetPath( 'manifest.json' );

		if ( file_exists( $file ) ) {
			$this->manifest = json_decode( file_get_contents( $file ), true );
		}
	}

	/**
	 * @return void
	 */
	public function loadHot(): void
	{
		$file = surfPath( 'hot' );

		if ( file_exists( $file ) ) {
			$this->hot = file_get_contents( $file );
		}
	}

	/**
	 * @return bool
	 */
	public function hasManifest(): bool
	{
		return $this->manifest !== null;
	}

	/**
	 * @return bool
	 */
	public function isHot(): bool
	{
		return surfApp()->isLocal() && $this->hot !== null;
	}

	/**
	 * @param string $entry
	 * @param bool $usePath
	 * @return string|null
	 */
	public function entry( string $entry, bool $usePath = false ): ?string
	{
		if ( $this->isHot() ) {
			return $this->hot . '/' . $entry;
		}

		$file = $this->manifest[ $entry ]['file'] ?? null;

		return $file ? $this->asset( $file, $usePath ) : null;
	}

	/**
	 * @param string $entry
	 * @param bool $usePath
	 * @return Collection
	 */
	public function css( string $entry, bool $usePath = false ): Collection
	{
		return collect( $this->manifest[ $entry ]['css'] ?? [] )->map( fn( $c ) => $this->asset( $c, $usePath ) );
	}

	/**
	 * @param string $image
	 * @return string
	 */
	public function image( string $image ): string
	{
		return $this->asset( "images/{$image}" );
	}

	/**
	 * @param string $handle
	 * @param string $entry
	 * @param array $deps
	 * @param bool $inFooter
	 * @return void
	 */
	public function enqueue( string $handle, string $entry, array $deps = [], bool $inFooter = true )
	{
		wp_enqueue_script( $handle, $this->entry( $entry ), $deps, false, $inFooter );

		if ( $this->hot ) {
			return;
		}

		$this->css( $entry )->each( function ( $css, $index ) use ( $handle )
		{
			$number = $index + 1;
			wp_enqueue_style( "{$handle}.{$number}", $css );
		} );
	}

	/**
	 * @param string $handle
	 * @return void
	 */
	public function enqueueClient( string $handle = 'surf.vite-client' )
	{
		if ( !$this->hot ) {
			return;
		}

		wp_enqueue_script( $handle, "{$this->hot}{$this->base}@vite/client" );
	}

}
