<?php

namespace SURF\DB\Factories;

use Faker\Generator;
use SURF\Core\DB\AbstractFactory;
use SURF\PostTypes\Page;

/**
 * Class PageFactory
 * @package SURF\DB\Factories
 */
class PageFactory extends AbstractFactory
{

	protected string $postType = Page::class;

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
		];
	}

}
