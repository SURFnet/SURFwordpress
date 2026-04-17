<?php

namespace SURF\DB\Factories;

use Faker\Generator;
use SURF\Core\DB\AbstractFactory;
use SURF\PostTypes\Faq;
use SURF\Taxonomies\FaqCategory;

/**
 * Class FaqFactory
 * @package SURF\DB\Factories
 */
class FaqFactory extends AbstractFactory
{

	protected string $postType = Faq::class;

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
				FaqCategory::getName() => $faker->randomElement(
					[ 'Algemene vragen', 'Software', 'Hardware', 'Handleidingen', 'Single Sign On' ]
				),
			],
		];
	}

}
