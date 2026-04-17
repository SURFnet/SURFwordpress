<?php

namespace SURF\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use SURF\Core\PostTypes\BasePost;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\PostTypes\Faq;
use SURF\Taxonomies\FaqCategory;

/**
 * Class ExportService
 * @package SURF\Services
 */
class ExportService
{

	protected Collection $posts;
	protected string     $postType;
	protected array      $filters = [];

	protected Spreadsheet $spreadsheet;
	protected Mpdf        $mpdf;
	protected array       $sheetTerms = [];

	protected bool $isXlsx = false;
	protected bool $isPdf  = false;

	protected array $filterTerms = [];

	/**
	 * @param Collection $posts
	 * @param string $postType
	 * @param array $filters
	 */
	public function __construct( Collection $posts, string $postType, array $filters = [] )
	{
		$this->posts    = $posts;
		$this->postType = $postType;
		$this->filters  = $filters;

		$this->spreadsheet = new Spreadsheet();
		$this->mpdf        = new Mpdf();
	}

	/**
	 * @return $this
	 */
	public function toXlsx(): self
	{
		if ( !$this->isEnabled() ) {
			return $this;
		}

		$this->isXlsx = true;
		$this->isPdf  = false;

		$this->setupXlsx();
		$this->setSheetData();
		$this->setSheetOptions();

		return $this;
	}

	/**
	 * @return $this
	 * @throws MpdfException
	 */
	public function toPdf(): self
	{
		if ( !$this->isEnabled() ) {
			return $this;
		}

		$this->isPdf  = true;
		$this->isXlsx = false;

		$this->setupPdf();
		$this->setPdfFirstPage();
		$this->setPdfData();

		return $this;
	}

	/**
	 * @param array $terms
	 * @return $this
	 */
	public function setFilterTerms( array $terms ): self
	{
		foreach ( $terms as $termId ) {
			$term = get_term( $termId );

			if ( !$term || is_wp_error( $term ) ) {
				continue;
			}

			$parent = null;
			if ( $term->parent ) {
				$parent = get_term( $term->parent );

				if ( !$parent || is_wp_error( $parent ) ) {
					$parent = null;
				}
			}

			$this->filterTerms[] = [
				'name'   => $term->name,
				'parent' => $parent?->name,
			];
		}

		return $this;
	}

	/**
	 * @return string
	 */
	protected function getLabel(): string
	{
		$default = get_post_type_object( $this->postType )?->labels?->name ?? $this->postType;

		$prefix = Faq::getLocalizedSettingsPrefix();

		return match ( $this->postType ) {
			Faq::getName() => get_option( "{$prefix}_faq_field_single_title", $default ),
			default        => $default
		};
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	protected function setupXlsx(): void
	{
		$this->spreadsheet->getDefaultStyle()->getAlignment()->setWrapText( true )
		                  ->setVertical( Alignment::VERTICAL_TOP );

		$this->setupSheetTerms();
	}

	/**
	 * @return void
	 */
	protected function setupSheetTerms(): void
	{
		foreach ( static::getTaxonomies()[ $this->postType ] ?? [] as $taxonomy ) {
			$parentTerms = get_terms( [
				'taxonomy' => $taxonomy,
				'parent'   => 0,
			] );

			foreach ( $parentTerms as $term ) {
				$this->sheetTerms[] = [
					'id'       => $term->term_id,
					'name'     => $term->name,
					'slug'     => $term->slug,
					'taxonomy' => $term->taxonomy,
				];
			}
		}
	}

	/**
	 * Return the columns for the sheet. This sets the order the columns have to be in.
	 * @return array<string>
	 */
	protected function getSheetColumns(): array
	{
		$columns = array_merge(
			[
				'title_prefix',
				'title',
				'content',
			],
			Faq::getContentBlockLabels( true ),
			[
				'id',
				'version',
			]
		);

		$terms = [];
		foreach ( $this->sheetTerms as $term ) {
			if ( !in_array( $term['slug'], $columns, true ) ) {
				$terms[] = $term['slug'];
			}
		}

		return array_merge( $terms, $columns );
	}

	/**
	 * @return void
	 */
	protected function setSheetData(): void
	{
		$sheet = $this->spreadsheet->getActiveSheet();

		$sheet->setTitle( __( 'Data', 'wp-surf-theme' ) );
		$this->setSheetHeader();

		/** @var BasePost $post */
		foreach ( $this->posts->all() as $i => $post ) {
			$data = [];

			foreach ( $this->sheetTerms as $term ) {
				$postTerms = get_terms( [
					'taxonomy'   => $term['taxonomy'],
					'object_ids' => $post->ID(),
					'parent'     => $term['id'],
					'fields'     => 'names',
				] );

				$data[ $term['slug'] ] = implode( ', ', $postTerms );
			}

			$postValues = $this->getPostValuesForSheet( $post );

			foreach ( $postValues as $key => $value ) {
				$data[ $key ] = html_entity_decode( $value );
			}

			$row  = $sheet->getHighestDataRow() + 1;
			$cell = 'A';

			foreach ( $this->getSheetColumns() as $columnKey ) {
				if ( array_key_exists( $columnKey, $data ) ) {
					$sheet->setCellValue( "$cell$row", html_entity_decode( $data[ $columnKey ] ) );

					$cell++;
				}
			}

			// Flush the output buffer every X items, so it doesn't overflow.
			if ( $i % 100 === 0 ) {
				flush();
			}
		}
	}

	/**
	 * @param BasePost|Faq $post
	 * @return array
	 */
	protected function getPostValuesForSheet( BasePost|Faq $post ): array
	{
		$values = [
			'id'      => $post->ID(),
			'title'   => $post->title(),
			'content' => wp_strip_all_tags( $post->content() ),
		];

		if ( $this->postType === Faq::getName() ) {
			$values['id'] = $post->customId();

			if ( Faq::useExtraFields() ) {
				$blocks = collect( $post->getContentBlocks( true ) )
					->mapWithKeys( fn( array $block ) => [ $block['label'] => wp_strip_all_tags( $block['value'] ) ] )
					->toArray();

				$values = array_merge(
					$values,
					[ 'title_prefix' => $post->getTitlePrefix() ],
					$blocks
				);
			}
		}

		return $values;
	}

	/**
	 * @return void
	 */
	protected function setSheetHeader(): void
	{
		// First we set up all header data.
		$headers = [];

		foreach ( $this->sheetTerms as $term ) {
			$headers[ $term['slug'] ] = [
				'value' => $term['name'],
				'width' => 20,
			];
		}

		$titleHeaders   = [
			'title' => [
				'value' => __( 'Title', 'wp-surf-theme' ),
				'width' => 42,
			],
		];
		$contentHeaders = [
			'content' => [
				'value' => __( 'Description', 'wp-surf-theme' ),
				'width' => 80,
			],
		];

		// If enabled and in case of FAQs, we need different headers.
		if ( $this->postType === Faq::getName() && Faq::useExtraFields() ) {
			/** @var Faq|null $firstPost */
			$firstPost = $this->posts->first();

			if ( $firstPost ) {
				$titleHeaders = array_merge( [
					'title_prefix' => [
						'value' => get_option( 'options_faq_single_title_prefix_label' ) ?: __( 'Title prefix', 'wp-surf-theme' ),
						'width' => 42,
					],
				], $titleHeaders );

				$contentBlocks = collect( $firstPost->getContentBlocks() )
					->mapWithKeys( function ( array $block )
					{
						$label = wp_strip_all_tags( $block['label'] );

						return [
							Str::snake( $label ) => [
								'value' => $label,
								'width' => 80,
							],
						];
					} )
					->toArray();

				$contentHeaders = array_merge( $contentHeaders, $contentBlocks );
			}
		}

		// Combine all headers together.
		$headers = array_merge(
			$headers,
			$titleHeaders,
			$contentHeaders,
			[
				'id' => [
					'value' => __( 'ID', 'wp-surf-theme' ),
					'width' => 5,
				],
			]
		);

		$sheet  = $this->spreadsheet->getActiveSheet();
		$column = 'A';

		// Loop through all columns and set the header values.
		foreach ( $this->getSheetColumns() as $columnKey ) {
			if ( !array_key_exists( $columnKey, $headers ) ) {
				continue;
			}

			$cell = "{$column}1";

			$sheet->setCellValue( $cell, html_entity_decode( $headers[ $columnKey ]['value'] ) );
			$this->setSheetHeaderCellStyle( $cell );
			$sheet->getColumnDimension( $column )->setWidth( $headers[ $columnKey ]['width'] );

			$column++;
		}
	}

	/**
	 * @param string $cell
	 * @return void
	 */
	protected function setSheetHeaderCellStyle( string $cell ): void
	{
		$sheet     = $this->spreadsheet->getActiveSheet();
		$cellStyle = $sheet->getStyle( $cell );

		$cellStyle->getFill()->setFillType( Fill::FILL_SOLID )->getStartColor()->setRGB( '4472C4' );
		$cellStyle->getFont()->getColor()->setRGB( 'FFFFFF' );
		$cellStyle->getFont()->setBold( true );
	}

	/**
	 * @return void
	 */
	protected function setSheetOptions(): void
	{
		$optionsSheet = $this->spreadsheet->createSheet();

		$optionsSheet->setTitle( __( 'Selected filters', 'wp-surf-theme' ) );
		$optionsSheet->getColumnDimension( 'A' )->setWidth( 20 );
		$optionsSheet->getColumnDimension( 'B' )->setWidth( 20 );

		foreach ( [ 'A1', 'B1' ] as $cell ) {
			$cellStyle = $optionsSheet->getStyle( $cell );

			$cellStyle->getFill()->setFillType( Fill::FILL_SOLID )->getStartColor()->setRGB( '4472C4' );
			$cellStyle->getFont()->getColor()->setRGB( 'FFFFFF' );
			$cellStyle->getFont()->setBold( true );
		}

		$optionsSheet->setCellValue( 'A1', __( 'Filter', 'wp-surf-theme' ) );
		$optionsSheet->setCellValue( 'B1', __( 'Value', 'wp-surf-theme' ) );

		if ( array_key_exists( 'orderby', $this->filters ) && !empty( $this->filters['orderby'] ) ) {
			$optionsSheetRow = $optionsSheet->getHighestDataRow() + 1;

			$optionsSheet->setCellValue( "A$optionsSheetRow", __( 'Order by', 'wp-surf-theme' ) );
			$optionsSheet->setCellValue( "B$optionsSheetRow", $this->getOrderBy( $this->filters['orderby'] ) );
		}

		if ( array_key_exists( 'order', $this->filters ) && !empty( $this->filters['order'] ) ) {
			$optionsSheetRow = $optionsSheet->getHighestDataRow() + 1;

			$optionsSheet->setCellValue( "A$optionsSheetRow", __( 'Order', 'wp-surf-theme' ) );
			$optionsSheet->setCellValue(
				"B$optionsSheetRow",
				strtolower( $this->filters['order'] ) === 'desc' ? __( 'Descending', 'wp-surf-theme' ) : __(
					'Ascending',
					'wp-surf-theme'
				)
			);
		}

		if ( !empty( $this->filterTerms ) ) {
			foreach ( $this->filterTerms as $term ) {
				$optionsSheetRow = $optionsSheet->getHighestDataRow() + 1;

				$optionsSheet->setCellValue(
					"A$optionsSheetRow",
					html_entity_decode( $term['parent'] ?? __( 'Category', 'wp-surf-theme' ) )
				);
				$optionsSheet->setCellValue( "B$optionsSheetRow", html_entity_decode( $term['name'] ) );
			}
		}
	}

	/**
	 * @return void
	 * @throws MpdfException
	 */
	protected function setupPdf(): void
	{
		error_reporting( 0 );

		$this->mpdf->SetTitle( 'Export' );

		$css   = vite()->entry( 'src/scss/exports.scss', true );
		$style = $css !== null ? file_get_contents( $css ) : null;

		if ( $style !== null ) {
			$this->mpdf->WriteHTML( $style, HTMLParserMode::HEADER_CSS );
		}

		$this->mpdf->SetHTMLFooter( '<div style="text-align: center">{PAGENO}</div>' );
	}

	/**
	 * @return void
	 * @throws MpdfException
	 */
	protected function setPdfFirstPage(): void
	{
		$this->mpdf->WriteHTML( sprintf( '<h2>%s: %s</h2>', __( 'Export for', 'wp-surf-theme' ), $this->getLabel() ) );

		$this->mpdf->WriteHTML( sprintf( '<h3>%s</h3>', __( 'Selected filters', 'wp-surf-theme' ) ) );

		$this->mpdf->WriteHTML(
			surfView( 'exports.archive-surf-faq-filters', [
				'filters' => $this->filterTerms,
				'orderBy' => $this->getOrderBy( $this->filters['orderby'] ?? '' ),
				'order'   => $this->filters['order'] ?? null,
			] )->render(),
			HTMLParserMode::HTML_BODY
		);
	}

	/**
	 * @return void
	 * @throws MpdfException
	 */
	protected function setPdfData(): void
	{
		/** @var BasePost $post */
		foreach ( $this->posts->all() as $i => $post ) {
			$this->mpdf->AddPage();

			if ( $this->postType === Faq::getName() ) {
				$this->mpdf->WriteHTML(
					surfView( 'exports.archive-' . Faq::getName(), [ 'faq' => $post ] )->render(),
					HTMLParserMode::HTML_BODY
				);
			}

			// Flush the output buffer every X items, so it doesn't overflow.
			if ( $i % 100 === 0 ) {
				flush();
			}
		}
	}

	/**
	 * @param string $orderBy
	 * @return string
	 */
	protected function getOrderBy( string $orderBy = '' ): string
	{
		return match ( strtolower( $orderBy ) ) {
			'id'    => __( 'ID', 'wp-surf-theme' ),
			'title' => __( 'Title', 'wp-surf-theme' ),
			default => $orderBy
		};
	}

	/**
	 * @return void
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function toBrowser(): void
	{
		if ( !$this->isEnabled() ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit;
		}

		if ( $this->isXlsx ) {
			header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
			header( 'Content-Disposition: attachment;filename="export.xlsx"' );
			header( 'Cache-Control: max-age=0' );

			$writer = new Xlsx( $this->spreadsheet );
			$writer->save( 'php://output' );
			exit;
		}

		if ( $this->isPdf ) {
			$this->mpdf->OutputHttpDownload( 'export.pdf' );
			exit;
		}
	}

	/**
	 * @return bool
	 */
	protected function isEnabled(): bool
	{
		return static::getEnabledStates()[ $this->postType ] ?? false;
	}

	/**
	 * @param Collection $posts
	 * @param string $postType
	 * @param array $args
	 * @return static
	 */
	public static function make( Collection $posts, string $postType, array $args = [] ): static
	{
		return new static( $posts, $postType, $args );
	}

	/**
	 * @return array<string,bool>
	 */
	protected static function getEnabledStates(): array
	{
		return [
			Faq::getName() => get_option( 'options_faq_archive_show_export_button', false ),
		];
	}

	/**
	 * @return array<string,array<Taxonomy>>
	 */
	protected static function getTaxonomies(): array
	{
		return [
			Faq::getName() => [ FaqCategory::getName() ],
		];
	}

}
