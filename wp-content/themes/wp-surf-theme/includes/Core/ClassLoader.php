<?php

namespace SURF\Core;

/**
 * Class ClassLoader
 * @package SURF\Core
 */
class ClassLoader
{

	/**
	 * @param string $directory
	 * @return array
	 */
	public function loadDirectory( string $directory ): array
	{
		$classes = [];
		$root    = surfApp()->getNamespaceDirectory() . DIRECTORY_SEPARATOR;
		$files   = glob( $directory . DIRECTORY_SEPARATOR . '*.php' );
		foreach ( $files as $file ) {
			require_once( $file );
			$classes[] = 'SURF\\' . str_replace( [ $root, '.php', DIRECTORY_SEPARATOR ], [ '', '', '\\' ], $file );
		}

		return $classes;
	}

	/**
	 * @param array $directories
	 * @return array
	 */
	public function loadDirectories( array $directories ): array
	{
		$classes = [];
		foreach ( $directories as $directory ) {
			$classes = array_merge( $classes, $this->loadDirectory( $directory ) );
		}

		return $classes;
	}

}
