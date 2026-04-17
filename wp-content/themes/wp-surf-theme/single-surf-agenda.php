<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\PostCollection;
use SURF\PostTypes\Agenda;
use SURF\Taxonomies\AgendaCategory;

/**
 * Class SingleSURFAgendaController
 * @package SURF
 */
class SingleSURFAgendaController extends TemplateController
{

	/**
	 * @param Agenda $event
	 * @return View
	 */
	public function handle( Agenda $event ): View
	{
		$relatedEvents = $event->showRelatedItems() ? $this->getRelatedEvents( $event ) : [];

		return $this->view( 'agenda.single', compact( 'event', 'relatedEvents' ) );
	}

	/**
	 * @param BasePost $event
	 * @return PostCollection
	 */
	public function getRelatedEvents( BasePost $event ): PostCollection
	{
		$args = [
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'no_found_rows'  => true,
			'post__not_in'   => [ $event->ID() ],
		];

		$taxonomy = AgendaCategory::getName();
		$terms    = get_the_terms( $event->ID(), $taxonomy );
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return Agenda::query( $args );
		}

		$args['tax_query'] = [
			[
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => [ $terms[0]->term_id ],
			],
		];

		return Agenda::query( $args );
	}

}
