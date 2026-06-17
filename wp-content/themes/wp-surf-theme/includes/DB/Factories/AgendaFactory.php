<?php

namespace SURF\DB\Factories;

use Faker\Generator;
use SURF\Core\DB\AbstractFactory;
use SURF\PostTypes\Agenda;
use SURF\Taxonomies\AgendaCategory;

/**
 * Class AgendaFactory
 * @package SURF\DB\Factories
 */
class AgendaFactory extends AbstractFactory
{

	protected string $postType = Agenda::class;

	/**
	 * @param Generator $faker
	 * @return array
	 */
	public function definition( Generator $faker ): array
	{
		return [
			'post_title'   => $faker->sentence( rand( 3, 6 ) ),
			'post_status'  => 'publish',
			'post_content' => $faker->paragraphs( rand( 2, 10 ), true ),
			'taxonomy'     => [
				AgendaCategory::getName() => $faker->randomElement( [ 'Festival', 'Concert', 'Beurs' ] ),
			],
			'meta'         => [
				'date'     => $faker->dateTimeBetween( 'now', '1 year' )->format( 'Y-m-d' ),
				'location' => "{$faker->streetAddress}, {$faker->city}",
			],
		];
	}

}
