<?php
/**
 * FH-Complete
 *
 * @package		FHC-API
 * @author		FHC-Team
 * @copyright	Copyright (c) 2016, fhcomplete.org
 * @license		GPLv3
 * @link		http://fhcomplete.org
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

if(!defined('BASEPATH')) exit('No direct script access allowed');

class Lvregeltyp extends APIv1_Controller
{
	/**
	 * Lvregeltyp API constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		// Load model LvregeltypModel
		$this->load->model('education/lvregeltyp', 'LvregeltypModel');
		// Load set the uid of the model to let to check the permissions
		$this->LvregeltypModel->setUID($this->_getUID());
	}

	/**
	 * @return void
	 */
	public function getLvregeltyp()
	{
		$lvregeltyp_kurzbz = $this->get('lvregeltyp_kurzbz');
		
		if(isset($lvregeltyp_kurzbz))
		{
			$result = $this->LvregeltypModel->load($lvregeltyp_kurzbz);
			
			$this->response($result, REST_Controller::HTTP_OK);
		}
		else
		{
			$this->response();
		}
	}

	/**
	 * @return void
	 */
	public function postLvregeltyp()
	{
		if($this->_validate($this->post()))
		{
			if(isset($this->post()['lvregeltyp_kurzbz']))
			{
				$result = $this->LvregeltypModel->update($this->post()['lvregeltyp_kurzbz'], $this->post());
			}
			else
			{
				$result = $this->LvregeltypModel->insert($this->post());
			}
			
			$this->response($result, REST_Controller::HTTP_OK);
		}
		else
		{
			$this->response();
		}
	}
	
	private function _validate($lvregeltyp = NULL)
	{
		return true;
	}
}