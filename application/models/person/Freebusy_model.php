<?php
class Freebusy_model extends DB_Model
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->dbTable = 'campus.tbl_freebusy';
		$this->pk = 'freebusy_id';
	}
}
