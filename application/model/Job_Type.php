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

	public function tableName() { return Job_Type::TABLE; }
	public function tablePK() { return Job_Type::id; }
	public function sortOrder() { return array(Job_Type::name); }


	public function allColumnNames()
	{
		return array(
			Job_Type::id, Job_Type::name, Job_Type::code, Job_Type::desc, Job_Type::scheduled
		 );
	}

    function jobTypes()
    {
		return $this->fetchAll(Job_Type::TABLE, $this->allColumnNames(), null, array(Job_Type::name));
    }

    function jobTypeForCode($cd)
    {
		return $this->fetch(Job_Type::TABLE, $this->allColumnNames(), array(Job_Type::code => $cd));
    }

    function allScheduled()
    {
		return $this->fetch(Job_Type::TABLE, $this->allColumnNames(), array(Job_Type::scheduled => 1));
    }
}

?>