<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;
use \Logger as Logger;
use \SQL as SQL;

use db\Qualifier as Qualifier;
use utilities\MediaFilename as MediaFilename;

use model\Publication as Publication;
use model\PublicationDBO as PublicationDBO;
use model\Endpoint as Endpoint;
use model\EndpointDBO as EndpointDBO;

/** Synonym for torrent ..
	flux
	noun state of constant change
 */
class Flux extends Model
{
	const TABLE =			'flux';
	const id =				'id';
	const publication_id =	'publication_id';
	const created =			'created';

	const name =			'name';
	const flux_hash = 		'flux_hash';
	const flux_error = 		'flux_error';

	const src_endpoint =	'src_endpoint';
	const src_guid =		'src_guid';
	const src_status = 		'src_status';
	const src_pub_date =	'src_pub_date';
	const src_url =			'src_url';

	const dest_endpoint =	'dest_endpoint';
	const dest_guid =		'dest_guid';
	const dest_submission =	'dest_submission';
	const dest_status =		'dest_status';

	public function tableName() { return Flux::TABLE; }
	public function tablePK() { return Flux::id; }
	public function sortOrder() { return array(Flux::name, Flux::dest_submission); }

	public function allColumnNames()
	{
		return array(
			Flux::id, Flux::publication_id, Flux::created, Flux::name, Flux::flux_hash, Flux::flux_error,
			Flux::src_endpoint, Flux::src_guid, Flux::src_status, Flux::src_pub_date, Flux::src_url,
			Flux::dest_endpoint, Flux::dest_guid, Flux::dest_submission, Flux::dest_status
		);
	}

	public function objectForSrc_guid( $guid )
	{
		return $this->singleObjectForKeyValue( Flux::src_guid, $guid );
	}

	public function objectForSourceGUID( $guid )
	{
		return $this->singleObjectForKeyValue( Flux::src_guid, $guid );
	}

	public function objectForDestinationGUID( $guid )
	{
		return $this->singleObjectForKeyValue( Flux::dest_guid, $guid );
	}

	public function objectForSourceEndpointGUID( EndpointDBO $endpoint = null, $guid )
	{
		return $this->singleObjectForKeyValues( array( Flux::src_endpoint => $endpoint->id, Flux::src_guid => $guid ) );
	}

	public function objectForSourceIdEndpointGUID( $endpoint_id = 0, $guid )
	{
		return $this->singleObjectForKeyValues( array( Flux::src_endpoint => $endpoint_id, Flux::src_guid => $guid ) );
	}

	public function objectForDestinationEndpointGUID( EndpointDBO $endpoint = null, $guid )
	{
		return $this->singleObjectForKeyValues( array( Flux::dest_endpoint => $endpoint->id, Flux::dest_guid => $guid ) );
	}

	public function destinationIncomplete($limit = 50)
	{
		$select = SQL::Select( $this )->where( Qualifier::AndQualifier(
				Qualifier::IsNotNull( Flux::dest_guid ),
				Qualifier::NotQualifier( Qualifier::IN( Flux::dest_status, array('Completed', 'Failed') ))
			)
		)->limit( $limit );
		return $select->fetchAll();
	}

	public function create( PublicationDBO $pub = null, $name = null, EndpointDBO $src, $guid = null, $pub_date = null, $url = null )
	{
		if ( isset($name, $guid, $src, $url)
			&& empty($name) == false && empty($guid) == false && empty($url) == false && is_null($src) == false ) {

			$obj = $this->objectForSourceGUID($guid);
			if ( $obj == false ) {
				$params = array(
					Flux::created => time(),
					Flux::name => $name,
					Flux::src_pub_date => $pub_date,
					Flux::src_guid => $guid,
					Flux::src_url => $url,
					Flux::src_endpoint => $src->id,
					Flux::flux_error => Model::TERTIARY_FALSE
				);

				if ( $pub instanceof PublicationDBO ) {
					$params[Flux::publication_id] = $pub->id;
				}

				list( $obj, $errorList ) = $this->createObject($params);
				if ( is_array($errorList) ) {
					return $errorList;
				}
			}
			return $obj;
		}

		return false;
	}
}
?>
