@php
	use SURF\PostTypes\Vacancy;
	use Spatie\SchemaOrg\Schema;

	/**
	 * @var Vacancy $vacancy
	 */

	// NOTE: hiringOrganization is hardcoded, this was requested by the client.
@endphp

{!!
    Schema::jobPosting()
        ->hiringOrganization(
            Schema::organization()
                ->address('Moreelsepark 48, 3511 EP Utrecht')
                ->logo('https://www.surf.nl/themes/surf/logo.svg')
                ->name('SURF')
                ->sameAs('https://www.surf.nl')
                ->telephone('088 787 3000')
        )
        ->title($vacancy->title())
        ->name($vacancy->title())
        ->datePosted($vacancy->date('Y-m-d'))
        ->description(wp_strip_all_tags($vacancy->excerpt()))
        ->educationRequirements(
            Schema::educationalOccupationalCredential()
                ->credentialCategory($vacancy->getDegree())
        )
        ->employmentType($vacancy->getEmployment())
        ->baseSalary(
            Schema::monetaryAmount()
                ->currency('EUR')
                ->value(
                    Schema::quantitativeValue()
                        ->minValue($vacancy->getMinSalary())
                        ->maxValue($vacancy->getMaxSalary())
                        ->unitText('MONTH')
                )
        )
        ->jobLocation(
            Schema::place()
            ->address($vacancy->getLocation())
        )
        ->workHours($vacancy->getPrimaryHoursName($vacancy->ID()))
        ->validThrough($vacancy->getDeadline('Y-m-d'))
        ->toScript()
!!}

