<?php
class Status_model extends DB_Model
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->dbTable = 'public.tbl_status';
		$this->pk = 'status_kurzbz';
	}
}
