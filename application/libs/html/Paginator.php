<?php

namespace html;

use \Localized as Localized;
use \html\Element as H;
use \http\PageParams as PageParams;

/**
 * Class to generate HTML pagination components.
 */
class Paginator
{
	public $parameters = null;
	public $baseURL = null;

	public function __construct(PageParams $param = null, $url = null)
	{
		$this->parameters = $param;
		$this->baseURL = $url;
	}

	/**
	 * Renders the HTML output
	 *
	 * @return   string
	 */
	public function __toString()
	{
		return $this->render();
	}

	/*
	*/
	public function render()
	{
		if (isset($this->parameters) && $this->parameters->pageCount() > 1) {
			$liArray = array();
			if ($this->parameters->pageShown() > 0) {
				$liArray[] =
					H::li( array( "class" => "pageItem" ),
						H::a( array(
							"href" => "#",
							"class" => "page",
							"data-pagenum" => ($this->parameters->pageShown()-1),
							"data-url" => ($this->baseURL)
							), Localized::GlobalLabel("Pagination", "Previous")
						)
					);
			}

			if ( $this->parameters->pageCount() < 5 ) {
				for( $i = 0; $i < $this->parameters->pageCount(); $i++ ) {
					$liArray[] =
						H::li( array( "class" => "pageItem " . ($this->parameters->pageShown() == $i ? "active" : "")),
							H::a( array(
								"href" => "#",
								"class" => "page",
								"data-pagenum" => $i,
								"data-url" => ($this->baseURL)
							), $i)
						);
				}
			}
			else {
				$min = MAX(0, ($this->parameters->pageShown() - 3));
				$max = MIN($this->parameters->pageCount(), ($this->parameters->pageShown() + 3));

				if ( $min > 0 ) {
					if ( $min > 1 ) {
						$liArray[] =
							H::li( array( "class" => "pageItem "),
								H::a( array(
									"href" => "#",
									"class" => "page",
									"data-pagenum" => 0,
									"data-url" => ($this->baseURL)
								), 1)
							);
					}
					$liArray[] = H::li( array( "class" => "pageItem"), "...");
				}

				for( $i = $min; $i < $max; $i++ ) {
					$liArray[] =
						H::li( array( "class" => "pageItem " . ($this->parameters->pageShown() == $i ? "active" : "")),
							H::a( array(
								"href" => "#",
								"class" => "page",
								"data-pagenum" => $i,
								"data-url" => ($this->baseURL)
							), $i+1)
						);
				}

				if ( $max < $this->parameters->pageCount() ) {
					$liArray[] = H::li( array( "class" => "pageItem"), "...");
					if ( $max < ($this->parameters->pageCount() - 1) ) {
						$liArray[] =
							H::li( array( "class" => "pageItem "),
								H::a( array(
									"href" => "#",
									"class" => "page",
									"data-pagenum" => $this->parameters->pageCount()-1,
									"data-url" => ($this->baseURL)
								), (string)($this->parameters->pageCount()) )
							);
					}
				}
			}

			if ($this->parameters->pageShown() < $this->parameters->pageCount() -1) {
				$liArray[] =
					H::li( array( "class" => "pageItem" ),
						H::a( array(
							"href" => "#",
							"class" => "page",
							"data-pagenum" => ($this->parameters->pageShown()+1),
							"data-url" => ($this->baseURL)
							), Localized::GlobalLabel("Pagination", "Next")
						)
					);
			}

			$pageSection = H::div( array( "class" => "pagination" ),
				H::span(array("class" => "large"), $this->parameters->pageSize() . " / " . $this->parameters->querySize()),
				H::ul( array( "class" => "page-numbers" ), function() use($liArray) { return $liArray; })
			);

			return $pageSection->render();
		}
		return "";
	}
}
