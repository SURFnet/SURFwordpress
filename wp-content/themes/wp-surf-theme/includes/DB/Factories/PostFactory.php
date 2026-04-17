<?php

namespace SURF\DB\Factories;

use Faker\Generator;
use SURF\Helpers\AttachmentHelper;
use SURF\Core\DB\AbstractFactory;
use SURF\PostTypes\Post;
use SURF\Taxonomies\Category;
use SURF\Taxonomies\Tag;

/**
 * Class PostFactory
 * @package SURF\DB\Factories
 */
class PostFactory extends AbstractFactory
{

	protected string $postType = Post::class;

	/**
	 * @param Generator $faker
	 * @return array
	 */
	public function definition( Generator $faker ): array
	{
		return [
			'post_title'     => $faker->sentence( rand( 3, 6 ) ),
			'post_status'    => 'publish',
			'post_content'   => $faker->paragraphs( rand( 2, 10 ), true ),
			'meta'           => [
				'test_meta' => $faker->numberBetween( 100, 1000 ),
			],
			'taxonomy'       => [
				Category::getName() => $faker->randomElements( [
					'Algemeen',
					'Economie',
					'Sport',
					'Tech',
				], rand( 1, 3 ) ),
				Tag::getName()      => $faker->words( rand( 0, 3 ) ),
			],
			'featured_image' => AttachmentHelper::getBase64Placeholder(),
		];
	}

}
