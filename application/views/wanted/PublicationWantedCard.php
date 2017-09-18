<?php

use html\Element as H;
use \model\media\PublicationDBO as PublicationDBO;

class PublicationWantedCard {
	private $publication;
	public function __construct(PublicationDBO $pub)
	{
		$this->publication = $pub;
	}

	public function publisherIconPath($record = null)
	{
		if (isset($record) && is_null($record) == false) {
			$publisher = $record->publisher();
			if ( $publisher != false ) {
				$pk = $publisher->pkValue();
				return Config::Web( "Image", "icon", 'publisher', $pk);
			}
		}
		return Config::Web('/public/img/Logo_favicon.png');
	}

	public function render()
	{
		$card = H::figure( array("class"=>"card"),
			H::div( array("class"=>"figure_top"),
				H::div( array("class"=>"figure_image"),
					H::a( array("href"=>"#"),
						H::img( array("class"=>"thumbnail publication", "src"=>Config::Web("Image", "thumbnail", "publication", $this->publication->id)))
					)
				),
				H::div( array( "class" => "figure_details" ),
					H::div( array( "class" => "figure_detail_top" ),
						H::img( array( "src" => $this->publisherIconPath($this->publication), "class" => "icon publisher" ))
					),
					H::div( array( "class" => "figure_detail_middle" ),
						H::p( array( "class" => "pub_name" ),  $this->publication->name ),
						H::p( array( "class" => "issue_num" ),  $this->publication->issue_num ),
						H::p( array( "class" => "pub_date" ),  $this->publication->publishedMonthYear() )
					)
				)
			),

			H::figcaption( array("class" => "caption"),
				H::div( array("style"=>"height:1em;"),
					H::span(array("class" => "search_string", "style"=>"font-size: 0.8em; float:left"), $this->publication->searchString() ),
					H::span(array("class" => "search_date", "style"=>"font-size: 0.8em; float:right"), $this->publication->formattedDate_search_date() )
				),
				function() {
					$rssMatch = $this->publication->rssMatches();
					if ( is_array($rssMatch) && count($rssMatch) > 0 ) {
						$rssTable = H::table(array("width"=>"100%"), H::tr( H::th("RSS Item"), H::th("Status")));
						foreach($rssMatch as $rss) {
							$flux = $rss->flux();
							if ($flux == false ) {
								// create download
								if ( $rss->endpoint()->isOverMaximum('daily_dnld_max') == false ) {
									$fluxHTML = H::div( array("id"=>"dnld_".$rss->safe_guid()),
										H::a( array(
											"href"=>"#",
											"class"			=>	"nzb button",
											"style"			=>	"white-space:nowrap;",
											"data-name"		=>	htmlentities($rss->clean_name),
											"data-issue"	=>	$rss->clean_issue,
											"data-year"		=>	$rss->clean_year,
											"data-endpoint_id"=>$rss->endpoint_id,
											"data-guid"		=>	$rss->guid,
											"data-url"		=>	$rss->enclosure_url,
											"data-postedDate"	=>	$rss->pub_date,
											"data-ref_guid"	=>	"dnld_".$rss->safe_guid()), "Download")
									);
								}
								else {
									$fluxHTML = H::div(
										H::div( array("style"=>"white-space: nowrap;"),
											H::span( array("class"=>"icon false")),
											H::span( array("class"=>"break-word"), $rss->endpoint()->dailyMaximumStatus())
										)
									);
								}
							}
							else {
								$fluxHTML = H::div(
									H::div( array("style"=>"white-space: nowrap;"),
										H::span( array("class"=>"icon ".($flux->isSourceComplete()?'true':'false'))),
										H::span( array("class"=>"break-word"), $flux->src_status)
									),
									H::div( array("style"=>"white-space: nowrap;"),
										H::span( array("class"=>"icon ".($flux->isComplete()?'true':'false'))),
										H::span( array("class"=>"break-word"), $flux->dest_status)
									)
								);
							}

							$rssTable->addElement( H::tr(
									H::td(
										H::h4( $rss->displayName() ),
										H::p( date("M d, Y", $rss->pub_date) ),
										H::p( formatSizeUnits($rss->enclosure_length) ),
										H::p( $rss->endpoint()->displayName() )
									),
									H::td( $fluxHTML )
								)
							);

						}
						$rssHTMP = H::div(array("class"=>"mediaData"), $rssTable);
					}
					return (isset($rssHTMP) ? $rssHTMP : null);
				},
				H::div(
					H::a( array("href"=>"#", "class"=>"srch button", "style"=>"white-space:nowrap;", "data-pub_id"=>$this->publication->id), "Search now")
				),
				H::div( array( "id"=>"ajaxDiv_".$this->publication->id) )
			)
		);
		return $card->render();
	}
}

/*
		<figure class="card">
			<div class="figure_top">
				<div class="figure_image">
					<a href="#">
						<img src="<?php	echo Config::Web("Image", "thumbnail", "publication", $publication->id); ?>" class="thumbnail publication">
					</a>
				</div>
				<div class="figure_details">
					<div class="figure_detail_top">
						<?php if ($publication->publisher() != null): ?>
							<img src="<?php	echo Config::Web("Image", "icon", "publisher", $publication->publisher()->id); ?>" class="icon publisher">
						<?php endif; ?>
					</div>
					<div class="figure_detail_middle">
						<p class="pub_name"><?php echo $publication->name; ?></p>
						<p class="issue_num"><?php echo $publication->issue_num; ?></p>
						<p class="pub_date"><?php echo $publication->publishedMonthYear(); ?></p>
					</div>
				</div>
			</div>
			<figcaption class="caption">
				<p style="height:1em;"><span class="search_string" style="float:left"><?php echo $publication->searchString(); ?></span>
					<span class="search_date" style="float:right;"><?php echo $publication->formattedDate_search_date(); ?></span></p>
				<?php $rssMatch = $publication->rssMatches(); if ( is_array($rssMatch) && count($rssMatch) > 0 ) :?>
				<div class="mediaData">
					<table>
						<tr>
							<th>RSS Item</th>
							<th>Status</th>
						</tr>
				<?php foreach($rssMatch as $rss): ?>
						<tr>
							<td>
								<h4><?php echo $rss->displayName(); ?></h4>
								<p><?php echo date("M d, Y", $rss->pub_date); ?></p>
								<p><?php echo formatSizeUnits($rss->enclosure_length); ?></p>
								<?php echo ($rss->enclosure_password == true ? "<em>**** password protected</em>" : ""); ?>
							</td>
							<td>
					<?php $flux = $rss->flux(); if ($flux == false ) : ?>
						<div id="dnld_<?php echo $rss->safe_guid(); ?>">
						<a href="#" class="nzb button" style="white-space:nowrap;"
							data-name="<?php echo htmlentities($rss->clean_name); ?>"
							data-issue="<?php echo $rss->clean_issue; ?>"
							data-year="<?php echo $rss->clean_year; ?>"
							data-endpoint_id="<?php echo $rss->endpoint_id; ?>"
							data-guid="<?php echo $rss->guid; ?>"
							data-url="<?php echo $rss->enclosure_url; ?>"
							data-postedDate="<?php echo $rss->pub_date; ?>"
							data-ref_guid="dnld_<?php echo $rss->safe_guid(); ?>"
							>Download</a>
						</div>
					<?php else: ?>
						<div>
							<div style="white-space: nowrap;">
								<span class="icon <?php echo ($flux->isSourceComplete()?'true':'false'); ?>"></span>
								<span class="break-word"><?php echo $flux->src_status ; ?></span>
							</div>
							<div style="white-space: nowrap;">
								<span class="icon <?php echo ($flux->isFlux_error()?'false':'true'); ?>"></span>
								<span class="break-word"><?php echo $flux->dest_status ; ?></span>
							</div>
						</div>
					<?php endif; ?>
							</td>
						</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				</table>
				<a href="#" class="srch button" style="white-space:nowrap;" data-pub_id="<?php echo $publication->id; ?>">Search now</a>
				<div id="ajaxDiv_<?php echo $publication->id; ?>"></div>
			</figcaption>
		</figure>
*/
