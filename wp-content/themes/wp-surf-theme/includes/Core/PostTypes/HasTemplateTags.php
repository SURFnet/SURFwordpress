<?php

namespace SURF\Core\PostTypes;

use Exception;
use WP_Post;

/**
 * Trait HasTemplateTags
 * Provides WordPress template tag methods for post objects
 * @package SURF\Core\PostTypes
 */
trait HasTemplateTags
{

	/**
	 * @return WP_Post
	 */
	abstract public function getPost(): WP_Post;

	/**
	 * @return mixed
	 */
	abstract public function setupPostdata();

	/**
	 * @return mixed
	 */
	abstract public function resetPostdata();

	/**
	 * @return int
	 * @throws Exception
	 */
	public function ID(): int
	{
		return $this->getPost()->ID;
	}

	/**
	 * @return string
	 */
	public function title(): string
	{
		return $this->call( 'the_title' );
	}

	/**
	 * @param $more_link_text
	 * @param $strip_teaser
	 * @return string
	 */
	public function content( $more_link_text = null, $strip_teaser = false ): string
	{
		return $this->call( 'the_content', func_get_args() );
	}

	/**
	 * @return string
	 */
	public function excerpt(): string
	{
		return $this->call( 'the_excerpt', func_get_args() );
	}

	/**
	 * @return string
	 */
	public function permalink(): string
	{
		return get_permalink( $this->ID );
	}

	/**
	 * @param string $separator
	 * @return string
	 */
	public function tags( string $separator = ', ' ): string
	{
		return $this->call( 'the_tags', [ '', $separator, '' ] );
	}

	/**
	 * @param string $separator
	 * @param string $parents
	 * @return string
	 */
	public function category( string $separator = '', string $parents = '' ): string
	{
		return $this->call( 'the_category', func_get_args() );
	}

	/**
	 * @param string $format
	 * @return string
	 * @throws Exception
	 */
	public function date( string $format = 'Y-m-d H:i:s' ): string
	{
		return wp_date(
			$format,
			strtotime( $this->getPost()->post_date )
		);
	}

	/**
	 * @param string $format
	 * @return string
	 */
	public function time( string $format = '' ): string
	{
		return $this->call( 'the_time', func_get_args() );
	}

	/**
	 * @param string $format
	 * @return string
	 */
	public function modifiedDate( string $format = '' ): string
	{
		return $this->call( 'the_modified_date', func_get_args() );
	}

	/**
	 * @param string $format
	 * @return string
	 */
	public function modifiedTime( string $format = '' )
	{
		return $this->call( 'the_modified_time', func_get_args() );
	}

	/**
	 * @return bool
	 */
	public function hasPostThumbnail(): bool
	{
		return $this->withPostdata(
			'has_post_thumbnail'
		);
	}

	/**
	 * @param string|int[] $size
	 * @param string|array $attr
	 * @return string
	 */
	public function postThumbnail( string|array $size = 'post-thumbnail', string|array $attr = '' ): string
	{
		return $this->call( 'the_post_thumbnail', func_get_args() );
	}

	/**
	 * @param string|string[] $class
	 */
	public function postClass( $class = '' ): string
	{
		return $this->call( 'post_class', func_get_args() );
	}

	/**
	 * @param string|null $text
	 * @param string $class
	 * @return string
	 */
	public function editPostLink( string $text = null, string $class = 'edit-post-link' ): string
	{
		return $this->call( 'edit_post_link', [ $text, '', '', 0, $class ] );
	}

	/**
	 * @return bool
	 */
	public function commentsOpen(): bool
	{
		return $this->withPostdata( 'comments_open' );
	}

	/**
	 * @return string
	 */
	public function getCommentsNumber(): string
	{
		return $this->withPostdata( 'get_comments_number' );
	}

	/**
	 * @return string
	 */
	public function commentsTemplate(): string
	{
		return $this->call( 'comments_template' );
	}

	/**
	 * Setup postdata and global post, then call the callback and reset the postdata
	 * @param callable $callback
	 * @return mixed
	 */
	public function withPostdata( callable $callback ): mixed
	{
		if ( get_post() === $this->getPost() ) {
			$output = $callback();
		} else {
			$this->setupPostdata();
			$output = $callback();
			$this->resetPostdata();
		}

		return $output;
	}

	/**
	 * Catch the output of functions that only echo like the_title() and the_post_thumbnail()
	 * @param callable $callback
	 * @return string
	 */
	public function echoToString( callable $callback ): string
	{
		ob_start();
		$callback();

		return ob_get_clean();
	}

	/**
	 * Call the callback with the correct postdata and capture echo output
	 * @param callable $callback
	 * @param array $args
	 * @return string
	 */
	protected function call( callable $callback, array $args = [] ): string
	{
		return $this->echoToString(
			fn() => $this->withPostdata(
				fn() => call_user_func_array( $callback, $args )
			)
		);
	}

}
