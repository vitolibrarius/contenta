<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class Job_Type extends Model
{
	const TABLE =		'job_type';
	const id =			'id';
	const name =		'name';
	const code =		'code';
	const desc = 		'desc';
	const scheduled = 	'scheduled';
	const processor = 	'processor';
	const parameter =	'parameter';

	public function tableName() { return Job_Type::TABLE; }
	public function tablePK() { return Job_Type::id; }
	public function sortOrder() { return array(Job_Type::name); }


	public function allColumnNames()
	{
		return array(
			Job_Type::id, Job_Type::name, Job_Type::code, Job_Type::desc, Job_Type::scheduled, Job_Type::processor, Job_Type::parameter
		 );
	}

    function jobTypeForCode($cd)
    {
		return $this->singleObjectForKeyValue( Job_Type::code, $cd );
    }

    function allScheduled()
    {
		return $this->singleObjectForKeyValue( Job_Type::scheduled, 1 );
    }

    function allForProcessor($processorName)
    {
    	return $this->allObjectsForKeyValue( Job_Type::processor, $processorName );
    }
}

?>
