<?php
/* Copyright (C) 20012 FH Technikum-Wien
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.
 *
 * Authors: Andreas Oesterreicher <andreas.oesterreicher@technikum-wien.at> and 
 */
/**
 * Klasse Service
 *  
 */
require_once(dirname(__FILE__).'/basis_db.class.php');

class service extends basis_db
{
	public $new;
	public $result = array();

	//Tabellenspalten
	public $service_id;		// bigint
	public $bezeichnung;	// varchar(64)
	public $beschreibung; 	// text
	public $ext_id;			// bigint
	public $oe_kurzbz;		// varchar(32)
	 
	/**
	 * Konstruktor - Laedt optional ein Service
	 * @param $service_id
	 */
	public function __construct($service_id=null)
	{
		parent::__construct();
				
		if(!is_null($service_id))
			$this->load($service_id);
	}

	/**
	 * Laedt ein Service mit der uebergebenen ID
	 * 
	 * @param $service_id
	 * @return boolean
	 */
	public function load($service_id)
	{
		if(!is_numeric($service_id))
		{
			$this->errormsg = 'Service ID ist ungueltig';
			return false;
		}
		
		
		$qry = "SELECT * FROM public.tbl_service WHERE service_id=".$this->db_add_param($service_id, FHC_INTEGER);

		if($result = $this->db_query($qry))
		{
			if($row = $this->db_fetch_object($result))
			{
				$this->service_id = $row->service_id;
				$this->bezeichnung = $row->bezeichnung;
				$this->beschreibung = $row->beschreibung;
				$this->ext_id = $row->ext_id;
				$this->oe_kurzbz = $row->oe_kurzbz;
				
				return true;
			}
			else
			{
				$this->errormsg = 'Service mit dieser ID exisitert nicht';
				return false;
			}
		}
		else
		{
			$this->errormsg = 'Fehler beim Laden des Service';
			return false;
		}
	}
	
	/**
	 * Laedt alle vorhandenen Services
	 */
	public function getAll()
	{	
		$qry = "SELECT * FROM public.tbl_service ORDER BY oe_kurzbz, bezeichnung";
		
		if($result = $this->db_query($qry))
		{
			while($row = $this->db_fetch_object($result))
			{
				$obj = new service();
					
				$obj->service_id = $row->service_id;
				$obj->bezeichnung = $row->bezeichnung;
				$obj->beschreibung = $row->beschreibung;
				$obj->ext_id = $row->ext_id;
				$obj->oe_kurzbz = $row->oe_kurzbz;
					
				$this->result[] = $obj;
			}
			return true;
		}
		else
		{
			$this->errormsg='Fehler beim Laden der Daten';
			return false;
		}
	}
	
	public function getServicesOrganisationseinheit($oe_kurzbz)
	{	
		$qry = 'SELECT 
					* 
				FROM 
					public.tbl_service 
				WHERE 
					oe_kurzbz='.$this->db_add_param($oe_kurzbz).' 
				ORDER BY bezeichnung';
		
		if($result = $this->db_query($qry))
		{
			while($row = $this->db_fetch_object($result))
			{
				$obj = new service();
					
				$obj->service_id = $row->service_id;
				$obj->bezeichnung = $row->bezeichnung;
				$obj->beschreibung = $row->beschreibung;
				$obj->ext_id = $row->ext_id;
				$obj->oe_kurzbz = $row->oe_kurzbz;
					
				$this->result[] = $obj;
			}
			return true;
		}
		else
		{
			$this->errormsg='Fehler beim Laden der Daten';
			return false;
		}
	}
	
	/**
	 * Prueft die Daten vor dem Speichern
	 * @return boolean
	 */
	public function validate()
	{
		return true;
	}
	
	/**
	 * Speichert ein Service
	 * @param $new
	 */
	public function save($new=null)
	{
		if(is_null($new))
			$new = $this->new;
			
		if(!$this->validate())
			return false;
					
		if($new)
		{
			$qry = "BEGIN;INSERT INTO public.tbl_service (bezeichnung, beschreibung, ext_id, oe_kurzbz)
					VALUES(".
				$this->db_add_param($this->bezeichnung).','.
				$this->db_add_param($this->beschreibung).','.
				$this->db_add_param($this->ext_id).','.
				$this->db_add_param($this->oe_kurzbz).');';
		}
		else
		{
			$qry = 'UPDATE public.tbl_service SET'.
				' bezeichnung = '.$this->db_add_param($this->bezeichnung).','.
				' beschreibung = '.$this->db_add_param($this->beschreibung).','.
				' ext_id = '.$this->db_add_param($this->ext_id).','.
				' oe_kurzbz = '.$this->db_add_param($this->oe_kurzbz).
				' WHERE service_id='.$this->db_add_param($this->service_id, FHC_INTEGER).';';					
		}
		
		if($this->db_query($qry))
		{
			if($new)
			{
				$qry = "SELECT currval('public.seq_service_service_id') as id";
				if($result = $this->db_query($qry))
				{
					if($row = $this->db_fetch_object($result))
					{
						$this->service_id = $row->id;
						$this->db_query('COMMIT;');
						return true;
					}
					else
					{
						$this->errormsg = 'Fehler beim Auslesen der Sequence';
						$this->db_query('ROLLBACK');
						return false;
					}
				}
				else
				{
					$this->errormsg = 'Fehler beim Auslesen der Sequence';
					$this->db_query('ROLLBACK');
					return false;
				}
			}
			else
				return true;
		}
		else
		{
			$this->errormsg = 'Fehler beim Speichern der Daten';
			return false;
		}
	}
	
	/**
	 * Loescht einen Service
	 
	 * @param $service_id
	 */
	public function delete($service_id)
	{
		if(!is_numeric($service_id))
		{
			$this->errormsg='ID ist ungueltig';
			return false;
		}
		$qry = "DELETE FROM public.tbl_service WHERE service_id=".$this->db_add_param($service_id);
		
		if($this->db_query($qry))
			return true;
		else
		{
			$this->errormsg = 'Fehler beim Loeschen des Service';
			return false;
		}
	}
}
?>