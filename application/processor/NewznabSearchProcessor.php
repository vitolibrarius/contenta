<?php

namespace processor;

use \Processor as Processor;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Localized as Localized;
use \Metadata as Metadata;

use \SQL as SQL;
use db\Qualifier as Qualifier;

use \interfaces\ProcessStatusReporter as ProcessStatusReporter;

use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job as Job;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Publication as Publication;
use \model\media\Series as Series;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint as Endpoint;

use connectors\NewznabConnector as NewznabConnector;
use processor\FluxImporter as FluxImporter;

use exceptions\EndpointConnectionException as EndpointConnectionException;

class NewznabSearchProcessor extends Processor
{
	const GUID = 'f641-9b49-20a1385e';

	function __construct($guid = null)
	{
		parent::__construct(NewznabSearchProcessor::GUID);
	}

	public static function isAcceptableMatch($publication, $name, $issue, $year)
	{
		$isAcceptable = false;
		if ( isset($publication, $name, $issue, $year) && $publication->series() != null ) {
			$pub_seriesName = $publication->series()->search_name;
			$margin = min( 5, (strlen($pub_seriesName)/2) );
			if ( levenshtein( $pub_seriesName, $name ) < $margin && $publication->publishedYear() == $year) {
				$padded_issue = $issue;
				if ( is_numeric($issue) ) {
					$padded_issue = str_pad($issue, 3, "0", STR_PAD_LEFT);
				}
// 				Logger::logWarning( "Testing match name ok  '" .$publication->paddedIssueNum(). "' " . $padded_issue );
				$isAcceptable = ($publication->paddedIssueNum() == $padded_issue);
			}
// 			else {
// 				Logger::logWarning( "Testing match '" .$pub_seriesName. "' "
// 					. levenshtein( $pub_seriesName, $name ) . " < " . $margin . " '" . $name . "'" );
// 			}
		}
		return $isAcceptable;
	}

	private function processRss( $publication, $fluxImporter )
	{
		$found_nzb_to_try = false;
		$rssMatch = $publication->rssMatches();
		if ( is_array($rssMatch) && count($rssMatch) > 0 ) {
			foreach( $rssMatch as $rss ) {
// 				Logger::logWarning( "Testing RSS " . $rss->id . "'" . $rss->displayName() . "'" );
				$existingFlux = $rss->flux();
				if ( $existingFlux != false ) {
					// skip already tried
// 					Logger::logWarning( "RSS " . $rss->displayName() . " has flux " . $existingFlux->src_guid );
					continue;
				}

				if ( isset($rss->enclosure_password) && $rss->enclosure_password ) {
					// skip password protected files
// 					Logger::logWarning( "RSS " . $rss->displayName() . " has password " );
					continue;
				}

				if ( isset($rss->enclosure_length) && intval($rss->enclosure_length) > (MEGABYTE * 100)) {
					// skip big files, the user can manually select them
// 					Logger::logWarning( "RSS " . $rss->displayName() . " has length " . $rss->enclosure_length );
					continue;
				}

				if ( NewznabSearchProcessor::isAcceptableMatch($publication, $rss->clean_name, $rss->clean_issue, $rss->clean_year) ) {
					$found_nzb_to_try = true;
// 					Logger::logWarning( "RSS " . $rss->displayName() . " is accepted" );
					$fluxImporter->importFluxRSS( $rss );
					break;
				}
			}
		}
		return $found_nzb_to_try;
	}

	public function processData(ProcessStatusReporter $reporter = null)
	{
		$endpoint_model = Model::Named('Endpoint');
		$newznab = $endpoint_model->allForTypeCode(Endpoint_Type::Newznab, true);
		if ( is_array($newznab) == false || count($newznab) == 0 ) {
			throw new EndpointConnectionException( 'No Newznab endpoints configured');
		}
		$newznabSearch = array();
		foreach( $newznab as $nzbd ) {
			if ( $nzbd->isOverMaximum() == false ) {
				$newznabSearch[] = new NewznabConnector( $nzbd );
			}
		}

		$sabnzbd = $endpoint_model->allForTypeCode(Endpoint_Type::SABnzbd, true);
		if ( is_array($sabnzbd) == false || count($sabnzbd) == 0 ) {
			throw new EndpointConnectionException( 'No SABnzbd endpoints configured');
		}

		$publication_model = Model::Named('Publication');
		$srchMax = Config::GetInteger("Search/daemon_max", 200);
		$queueLimit = 5;
		$srchCount = 0;

		if ( is_null($reporter) == false ) {
			$reporter->setProcessMaximum($srchMax);
			$reporter->setProcessMinimum(0);
			$reporter->setProcessCurrent(0);
			$reporter->setProcessMessage("NZB searching started");
		}

		while ( true ) {
			$pubs = $publication_model->searchQueueList( $queueLimit );
			if ( $pubs == false )  {
				break;
			}

			foreach ( $pubs as $publication ) {
				$srchCount++;
				if ( is_null($reporter) == false ) {
					$reporter->setProcessCurrent($srchCount);
					$reporter->setProcessMessage( "[".$srchCount . "/". $srchMax . "] for " . $publication->searchString() );
				}
				$publication->setSearch_date(time());
				$publication->saveChanges();

				$fluxImporter = new FluxImporter();
				$fluxImporter->setEndpoint( $sabnzbd[0] );
				if ( strlen($publication->seriesName()) > 5 && strlen($publication->paddedIssueNum()) > 2 && $publication->publishedYear() > 1900) {
					$found_nzb_to_try = $this->processRss( $publication, $fluxImporter );
					if ( $found_nzb_to_try == false ) {
						foreach( $newznabSearch as $nzbSearch ) {
							if ( $nzbSearch->endpointEnabled() ) {
								try {
									if ( $fluxImporter->findPostingsForPublication( $publication, $nzbSearch ) == true ) {
										break;
									}
								}
								catch ( \Exception $e ) {
									Logger::logException( $e );
								}
							}
						}
					}

					$fluxImporter->processData($reporter);
				}
			}

			if ($srchCount > $srchMax) break;
		}
	}
}
