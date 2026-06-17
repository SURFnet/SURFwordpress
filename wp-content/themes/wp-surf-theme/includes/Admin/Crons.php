<?php

namespace SURF\Admin;

use SURF\Services\CspService;

/**
 * Class Crons
 * @package SURF\Admin
 */
class Crons
{

	public const JOBS_OPTION = 'surf_cron_jobs';
	protected static array $jobs = [];

	/**
	 * Register Cron jobs
	 * @return void
	 */
	public static function init(): void
	{
		static::$jobs = [];
		static::filters();
		static::actions();
		static::jobs();

		static::execute();
	}

	/**
	 * Add cron intervals
	 * See: https://developer.wordpress.org/plugins/cron/understanding-wp-cron-scheduling/.
	 * @param $schedules
	 * @return mixed
	 */
	public static function addSchedules( $schedules )
	{
		return $schedules;
	}

	/**
	 * Add CRON actions here
	 * Example: add_action('surf_example_action', [Example::class, 'job']);.
	 */
	protected static function actions(): void
	{
		$cspService = new CspService();
		add_action( 'surf_check_for_csp', [ $cspService, 'syncCsp' ] );
	}

	/**
	 * Add CRON jobs here
	 * Example: static::addJob('surf_example_job', 'daily', 'Today 02:00');.
	 */
	protected static function jobs(): void
	{
		static::addJob( 'surf_check_for_csp', 'hourly' );
	}

	/**
	 * Add cron schedules filter.
	 */
	protected static function filters(): void
	{
		add_filter( 'cron_schedules', [ static::class, 'addSchedules' ] );
	}

	/**
	 * Add a job with a given action, recurrence and strtotime() string.
	 * @param string $action
	 * @param string $recurrence
	 * @param string $strTime
	 */
	protected static function addJob( string $action, string $recurrence = 'daily', string $strTime = 'Today 00:00' )
	{
		static::$jobs[] = [
			'action'     => $action,
			'recurrence' => $recurrence,
			'strTime'    => $strTime,
		];
	}

	/**
	 * Execute the cron job scheduling
	 * @return void
	 */
	public static function execute(): void
	{
		$current_jobs  = get_option( static::JOBS_OPTION, [] );
		$removed_crons = array_filter( $current_jobs, [ static::class, 'job_array_filter' ] );
		$changed_crons = array_udiff( static::$jobs, $current_jobs, [ static::class, 'job_array_diff' ] );
		foreach ( array_merge( $removed_crons, $changed_crons ) as $job ) {
			wp_clear_scheduled_hook( $job['action'] );
		}

		foreach ( static::$jobs as $job ) {
			if ( !wp_next_scheduled( $job['action'] ) ) {
				wp_schedule_event(
					strtotime( $job['strTime'] ),
					$job['recurrence'],
					$job['action']
				);
			}
		}

		update_option( static::JOBS_OPTION, static::$jobs, true );
	}

	/**
	 * Checks for differences in array.
	 * @param array $arr1
	 * @param array $arr2
	 * @return int
	 */
	public static function job_array_diff( array $arr1, array $arr2 ): int
	{
		if ( $arr1['action'] === $arr2['action'] ) {
			if ( $arr1['recurrence'] === $arr2['recurrence'] ) {
				if ( $arr1['strTime'] === $arr2['strTime'] ) {
					return 0;
				}
			}
		}

		return -1;
	}

	/**
	 * Filters out existing.
	 * @param array $array
	 * @return int
	 */
	public static function job_array_filter( array $array ): int
	{
		return !in_array( $array['action'], array_column( static::$jobs, 'action' ) );
	}

}
