<?php

namespace SURF\Core\Actions\Commands\DB;

use SURF\Core\Actions\AbstractAction;
use SURF\Core\PostTypes\BasePost;
use WP_CLI;

/**
 * Class Refresh
 * Command to refresh the database by removing factory-made content
 * @package SURF\Core\Actions\Commands\DB
 */
class Refresh extends AbstractAction
{

	/**
	 * @param array $args
	 * @param array $assocArgs
	 * @return void
	 */
	public function handle( array $args = [], array $assocArgs = [] ): void
	{
		if ( wp_get_environment_type() === 'production' ) {
			WP_CLI::error( "Whoops, you're trying to refresh a production database, I'm not letting you do that!" );
			exit;
		}

		$posts = BasePost::query( [
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'meta_query'     => [
				[
					'key'   => 'made_in_factory',
					'value' => true,
				],
			],
		] );

		$ids = $posts->pluck( 'ID' )->join( ',' );

		if ( !empty( $ids ) ) {
			global $wpdb;
			$queries = [
				"DELETE FROM {$wpdb->posts} WHERE `ID` IN ({$ids})",
				"DELETE FROM {$wpdb->postmeta} WHERE `post_id` IN ({$ids})",
				"DELETE FROM {$wpdb->term_relationships} WHERE `object_id` IN ({$ids})",
			];

			array_map( [ $wpdb, 'query' ], $queries );
		}

		$termIds = get_terms( [
			'fields'     => 'ids',
			'meta_query' => [
				[
					'key'   => 'made_in_factory',
					'value' => true,
				],
			],
		] );

		if ( !empty( $termIds ) ) {
			global $wpdb;

			$termIds = implode( ',', $termIds );
			$queries = [
				"DELETE FROM {$wpdb->term_taxonomy} WHERE `term_id` IN ({$termIds})",
				"DELETE FROM {$wpdb->termmeta} WHERE `term_id` IN ({$termIds})",
			];

			array_map( [ $wpdb, 'query' ], $queries );
		}

		WP_CLI::success( 'Database was refreshed.' );

		if ( $assocArgs['seed'] ?? false ) {
			Seed::run( $args, $assocArgs );
		}
	}

}
