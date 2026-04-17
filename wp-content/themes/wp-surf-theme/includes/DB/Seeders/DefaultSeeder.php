<?php

namespace SURF\DB\Seeders;

use Exception;
use SURF\Core\DB\AbstractSeeder;
use SURF\DB\Factories\FaqFactory;
use SURF\PostTypes\Agenda;
use SURF\PostTypes\Faq;
use SURF\PostTypes\Page;
use SURF\PostTypes\Post;

/**
 * Class DefaultSeeder
 * @package SURF\DB\Seeders
 */
class DefaultSeeder extends AbstractSeeder
{

	/**
	 * @return void
	 * @throws Exception
	 */
	public function run(): void
	{
		Post::factory()->count( 5 )->make();
		Agenda::factory()->count( 5 )->make();
		//Page::factory()->count(5)->make();
		Faq::factory()->count( 50 )->make();
	}

}
