<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Exception as Exception;

use \model\user\Users as Users;
use model\Publisher as Publisher;
use model\Character as Character;
use model\Series as Series;
use model\Publication as Publication;
use model\PublicationDBO as PublicationDBO;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\EndpointDBO as EndpointDBO;
use model\Story_Arc as Story_Arc;
use model\Story_Arc_Character as Story_Arc_Character;
use model\Story_Arc_Series as Story_Arc_Series;
use model\Flux as Flux;
use model\FluxDBO as FluxDBO;
use model\RssDBO as RssDBO;

use connectors\NewznabConnector as NewznabConnector;
use processor\NewznabSearchProcessor as NewznabSearchProcessor;

class FluxImporter extends EndpointImporter
{
	const META_IMPORTS = "imports";

	function __construct($guid = '')
	{
		if ( empty( $guid ) ) {
			$guid = uuid();
		}
		parent::__construct($guid);
	}

	public function setEndpoint(EndpointDBO $point = null)
	{
		if ( is_null($point) == false ) {
			$type = $point->type();
			if ( $type == false || $type->code != Endpoint_Type::SABnzbd ) {
				throw new Exception("Endpoint " . $point->displayName() . " is is not for " . Endpoint_Type::SABnzbd);
			}
		}
		parent::setEndpoint($point);
	}

	public function importFluxRSS( RssDBO $rss = null )
	{
		$this->importFluxValues( $rss->endpoint(), $rss->displayName(), $rss->guid, $rss->pub_date, $rss->enclosure_url );
	}


	public function importFluxValues( EndpointDBO $endpoint = null, $name = null, $guid = null, $publishedDate = null, $url = null )
	{
		if ( is_null($endpoint) || is_null($endpoint) || is_null($endpoint) || is_null($endpoint) || is_null($endpoint) ) {
			throw new \Exception( "Invalid null argument for $endpoint | $name | $guid | $publishedDate | $url");
		}
		$imports = ($this->isMeta(FluxImporter::META_IMPORTS) ? $this->getMeta(FluxImporter::META_IMPORTS) : array());
		$imports[] = array('endpoint_id'=>$endpoint->id, 'name'=>$name, 'guid'=>$guid, 'publishedDate'=>$publishedDate, 'url'=>$url);
		$this->setMeta(FluxImporter::META_IMPORTS, $imports);
	}

	public function importFlux( FluxDBO $flux = null )
	{
		if ( is_null($flux) ) {
			throw new \Exception( "Invalid null argument for flux");
		}

		$imports = ($this->isMeta(FluxImporter::META_IMPORTS) ? $this->getMeta(FluxImporter::META_IMPORTS) : array());
		$imports[] = array(	'flux_id' => $flux->id );
		$this->setMeta(FluxImporter::META_IMPORTS, $imports);
	}


	public function findPostingsForPublication( PublicationDBO $publication, NewznabConnector $nzbSearch )
	{
		if ( isset($publication, $nzbSearch)) {
			$FluxModel = Model::Named('Flux');
			$searchEndpoint = $nzbSearch->endpoint();

			$xml = $nzbSearch->searchComics($publication->searchString());
			if ( is_array($xml) ) {
// 				Logger::logWarning( "NZB search found " . count($xml) );
				foreach ($xml as $key => $item) {
					$guid = $item['guid'];
					// first check if this is a new item and not password protected
					$flux = $FluxModel->objectForSourceIdEndpointGUID( $searchEndpoint->id, $guid );
					if ( $flux == false && (isset($item['password']) == false || $item['password'] == false)) {
						$seriesName = $item['metadata']['name'];
						$issue = (isset($item['metadata']['issue']) ? $item['metadata']['issue'] : '');
						$year = (isset($item['metadata']['year']) ? $item['metadata']['year'] : '');
						$link = $item['url'];
						$pubDate = $item['publishedDate'];

						if ( isset($item['len']) && intval($item['len']) > (MEGABYTE * 100)) {
// 							Logger::logWarning( "Rejecting NZB '$seriesName' $issue ($year) [$guid] for size "
// 								. formatSizeUnits($item['len']));
							continue;
						}

						if ( NewznabSearchProcessor::isAcceptableMatch($publication, $seriesName, $issue, $year) ) {
// 							Logger::logWarning( "nzb " . $item['metadata']['name'] . " has accepted" );
							$this->importFluxValues( $nzbSearch->endpoint(), $seriesName.' '.$issue.' ('.$year.')' , $guid, $pubDate, $link );
							break;
						}
					}
// 					else {
// 						Logger::logWarning( "nzb " . $item['metadata']['name'] . " has flux " . $flux->src_guid );
// 					}
				}
			}
		}
	}

	private function downloadForFlux( FluxDBO $flux )
	{
		$localFile = $this->workingDirectory( "flux_" . $flux->id . "." . file_ext($flux->name));
		if ( file_exists($localFile) == true ) {
			safe_unlink($localFile);
		}
		$nzb = file_get_contents($flux->src_url);
		if ( $nzb != null ) {
			if (file_put_contents($localFile, $nzb)) {
				Model::Named('Flux')->updateObject( $flux,
					array(
						Flux::flux_hash => hash_file(HASH_DEFAULT_ALGO, $localFile),
						Flux::src_status => 'Downloaded'
					)
				);
				return $localFile;
			}
			else {
				Model::Named('Flux')->updateObject( $flux,
					array(
						Flux::flux_error => Model::TERTIARY_TRUE,
						Flux::src_status => 'Download failed'
					)
				);
			}
		}
		else {
			Model::Named('Flux')->updateObject( $flux,
				array(
					Flux::flux_error => Model::TERTIARY_TRUE,
					Flux::src_status => 'Download failed'
				)
			);
		}
		return false;
	}

	public function processData()
	{
// 		Logger::logInfo( "starting flux import" );
		$FluxModel = Model::Named('Flux');
		$imports = ($this->isMeta(FluxImporter::META_IMPORTS) ? $this->getMeta(FluxImporter::META_IMPORTS) : array());
		foreach( $imports as $idx => $fluxImport ) {
			if ( isset($fluxImport['flux_id']) ) {
				$flux = $FluxModel->objectForId( $fluxImport['flux_id'] );
			}
			else if (isset($fluxImport['endpoint_id'], $fluxImport['name'],
				$fluxImport['guid'], $fluxImport['publishedDate'], $fluxImport['url']) ) {
				$srcEndpoint = Model::Named('Endpoint')->objectForId($fluxImport['endpoint_id']);
				$flux = $FluxModel->objectForSourceGUID($fluxImport['guid']);
				if ( $flux == false ) {
					$flux = $FluxModel->create( null,
						$fluxImport['name'],
						$srcEndpoint,
						$fluxImport['guid'],
						$fluxImport['publishedDate'],
						$fluxImport['url']
					);
				}
			}

			if ( $flux instanceof FluxDBO ) {
				if ( $flux->isError() ) {
					throw new \Exception( $flux->__toString() . ' cannot import, is in error state ' . $flux->errorReason() );
				}

				if ( $flux->isSourceComplete() == false ) {
					$localFile = $this->downloadForFlux($flux);
					if ( file_exists($localFile) ) {
						$upload = $this->endpointConnector()->addNZB( $localFile, $flux->name );
						if ( is_array($upload) && isset($upload['status'], $upload['nzo_ids']) && $upload['status'] == true ) {
							$FluxModel->updateObject( $flux,
								array(
									Flux::dest_endpoint => $this->endpoint()->id,
									Flux::dest_status => 'Active',
									Flux::dest_guid => $upload['nzo_ids'][0],
									Flux::dest_submission => time()
								)
							);
						}
						else {
							$FluxModel->updateObject( $flux,
								array(
									Flux::dest_endpoint => $sabnzbd_point,
									Flux::dest_status => 'Failed ' . var_export($upload, true)
								)
							);
							Logger::logError( "Failed to schedule download for " . $flux->name );
						}
					}
				}
			}
			else {
				throw new \Exception( "Failed to find/create flux for " . var_export($fluxImport, true));
			}
		}

		$this->setPurgeOnExit(true);
		return true;
	}
}
