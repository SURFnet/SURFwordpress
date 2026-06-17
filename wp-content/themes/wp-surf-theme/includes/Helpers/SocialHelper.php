<?php

namespace SURF\Helpers;

/**
 * Class SocialShareHelper
 * @package SURF\Helpers
 */
class SocialHelper
{

	public const SOCIAL_TWITTER   = 'twitter';
	public const SOCIAL_FACEBOOK  = 'facebook';
	public const SOCIAL_MASTODON  = 'mastodon';
	public const SOCIAL_LINKEDIN  = 'linkedin';
	public const SOCIAL_INSTAGRAM = 'instagram';
	public const SOCIAL_YOUTUBE   = 'youtube';
	public const SOCIAL_VIMEO     = 'vimeo';
	public const SOCIAL_TIKTOK    = 'tiktok';

	public const SHARE_MAIL  = 'mail';
	public const SHARE_PRINT = 'print';

	/**
	 * @return array
	 */
	public static function allShareOptions(): array
	{
		return [
			static::SOCIAL_TWITTER  => _x( 'Twitter', 'admin', 'wp-surf-theme' ),
			static::SOCIAL_FACEBOOK => _x( 'Facebook', 'admin', 'wp-surf-theme' ),
			static::SOCIAL_LINKEDIN => _x( 'LinkedIn', 'admin', 'wp-surf-theme' ),
			static::SHARE_MAIL      => _x( 'Email', 'admin', 'wp-surf-theme' ),
			static::SHARE_PRINT     => _x( 'Print', 'admin', 'wp-surf-theme' ),
		];
	}

	/**
	 * @param string $type
	 * @return bool
	 */
	public static function allowShare( string $type = '' ): bool
	{
		if ( empty( static::allShareOptions()[ $type ] ) ) {
			return false;
		}

		return !empty( get_option( 'options_social_share_' . $type, true ) );
	}

	/**
	 * @param int|null $post_id
	 * @return array
	 */
	public static function getShareList( ?int $post_id ): array
	{
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}
		if ( empty( $post_id ) ) {
			return [];
		}

		$url   = get_the_permalink( $post_id );
		$title = get_the_title( $post_id );
		$list  = [
			static::SOCIAL_TWITTER  => [
				'label'  => __( 'Share this on Twitter', 'wp-surf-theme' ),
				'icon'   => 'social-twitter',
				'sprite' => 'global',
			],
			static::SOCIAL_FACEBOOK => [
				'label'  => __( 'Share this on Facebook', 'wp-surf-theme' ),
				'icon'   => 'social-facebook',
				'sprite' => 'global',
			],
			static::SOCIAL_LINKEDIN => [
				'label'  => __( 'Share this on LinkedIn', 'wp-surf-theme' ),
				'icon'   => 'social-linkedin',
				'sprite' => 'global',
			],
			static::SHARE_MAIL      => [
				'label'  => __( 'Share this by email', 'wp-surf-theme' ),
				'icon'   => 'email',
				'sprite' => 'global',
			],
			static::SHARE_PRINT     => [
				'label'   => __( 'Print this item', 'wp-surf-theme' ),
				'icon'    => 'print',
				'sprite'  => 'global',
				'onclick' => 'window.print();return false;',
			],
		];

		foreach ( $list as $type => $data ) {
			if ( !static::allowShare( $type ) ) {
				unset( $list[ $type ] );
				continue;
			}

			$share_url            = static::getShareUrl( $type, $url, $title );
			$list[ $type ]['url'] = str_replace( ' ', '%20', $share_url );
		}

		return $list;
	}

	/**
	 * @param string $type
	 * @param string $url
	 * @param string $title
	 * @return string
	 */
	public static function getShareUrl( string $type = '', string $url = '', string $title = '' ): string
	{
		switch ( $type ) {
			case static::SOCIAL_TWITTER:
				$base_url = 'https://twitter.com/intent/tweet';
				$decoded  = html_entity_decode( $title, ENT_COMPAT, 'UTF-8' );
				$encoded  = htmlspecialchars( rawurlencode( $decoded ), ENT_COMPAT, 'UTF-8' );

				return add_query_arg( [ 'text' => $encoded . ' - ' . $url ], $base_url );

			case static::SOCIAL_FACEBOOK:
				$base_url = 'https://www.facebook.com/share.php';

				return add_query_arg( [ 'u' => $url ], $base_url );

			case static::SOCIAL_LINKEDIN:
				$base_url = 'https://www.linkedin.com/sharing/share-offsite/';

				return add_query_arg( [ 'url' => $url ], $base_url );

			case static::SHARE_MAIL:
				$subject = __( 'Read this:', 'wp-surf-theme' ) . ' ' . wp_strip_all_tags( $title );
				$content = __( 'This might interest you:', 'wp-surf-theme' ) . ' ' . $url;

				return str_replace( ' ', '%20', 'mailto:?subject=' . $subject . '&body=' . $content );

			case static::SHARE_PRINT:
				return '#';
		}

		return '';
	}

	/**
	 * @return array
	 */
	public static function allFollowOptions(): array
	{
		return [
			static::SOCIAL_TWITTER   => _x( 'Twitter', 'admin', 'wp-surf-theme' ),
			static::SOCIAL_FACEBOOK  => _x( 'Facebook', 'admin', 'wp-surf-theme' ),
			static::SOCIAL_MASTODON  => _x( 'Mastodon', 'admin', 'wp-surf-theme' ),
			static::SOCIAL_LINKEDIN  => _x( 'LinkedIn', 'admin', 'wp-surf-theme' ),
			static::SOCIAL_INSTAGRAM => _x( 'Instagram', 'admin', 'wp-surf-theme' ),
			static::SOCIAL_YOUTUBE   => _x( 'Youtube', 'admin', 'wp-surf-theme' ),
			static::SOCIAL_VIMEO     => _x( 'Vimeo', 'admin', 'wp-surf-theme' ),
			static::SOCIAL_TIKTOK    => _x( 'TikTok', 'admin', 'wp-surf-theme' ),
		];
	}

	/**
	 * @return array
	 */
	public static function getFollowList(): array
	{
		$list = [
			static::SOCIAL_TWITTER   => [
				'label'  => __( 'Follow us on X', 'wp-surf-theme' ),
				'icon'   => 'social-twitter',
				'sprite' => 'global',
			],
			static::SOCIAL_FACEBOOK  => [
				'label'  => __( 'Follow us on Facebook', 'wp-surf-theme' ),
				'icon'   => 'social-facebook',
				'sprite' => 'global',
			],
			static::SOCIAL_MASTODON  => [
				'label'  => __( 'Follow us on Mastodon', 'wp-surf-theme' ),
				'icon'   => 'social-mastodon',
				'sprite' => 'global',
			],
			static::SOCIAL_LINKEDIN  => [
				'label'  => __( 'Follow us on LinkedIn', 'wp-surf-theme' ),
				'icon'   => 'social-linkedin',
				'sprite' => 'global',
			],
			static::SOCIAL_INSTAGRAM => [
				'label'  => __( 'Follow us on Instagram', 'wp-surf-theme' ),
				'icon'   => 'social-instagram',
				'sprite' => 'global',
			],
			static::SOCIAL_YOUTUBE   => [
				'label'  => __( 'Follow us on Youtube', 'wp-surf-theme' ),
				'icon'   => 'social-youtube',
				'sprite' => 'global',
			],
			static::SOCIAL_VIMEO     => [
				'label'  => __( 'Follow us on Vimeo', 'wp-surf-theme' ),
				'icon'   => 'social-vimeo',
				'sprite' => 'global',
			],
			static::SOCIAL_TIKTOK    => [
				'label'  => __( 'Follow us on TikTok', 'wp-surf-theme' ),
				'icon'   => 'social-tiktok',
				'sprite' => 'global',
			],
		];

		foreach ( $list as $type => $data ) {
			$follow_url = static::getFollowUrl( $type );
			if ( empty( $follow_url ) ) {
				unset( $list[ $type ] );
				continue;
			}

			$list[ $type ]['url'] = $follow_url;
		}

		return $list;
	}

	/**
	 * @param string $type
	 * @return string
	 */
	public static function getFollowUrl( string $type ): string
	{
		if ( empty( static::allFollowOptions()[ $type ] ) ) {
			return '';
		}

		return (string) get_option( 'options_' . $type . '_url' );
	}

}
