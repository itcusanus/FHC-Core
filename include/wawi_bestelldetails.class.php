<?php
/* Copyright (C) 2010 Technikum-Wien
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
 * Authors: Christian Paminger <christian.paminger@technikum-wien.at>,
 *          Andreas Oesterreicher <andreas.oesterreicher@technikum-wien.at> and
 *          Karl Burkhart <burkhart@technikum-wien.at>.
 */
/**
 * Klasse WaWi Bestelldetail
 */
require_once(dirname(__FILE__).'/basis_db.class.php');

class wawi_bestelldetail extends basis_db
{	
	public $bestelldetail_id;	// serial 
	public $bestellung_id; 		// integer
	public $position; 			// integer
	public $menge; 				// integer
	public $verpackungseinheit; // char
	public $beschreibung; 		// text
	public $artikelnummer; 		// char
	public $preisprove; 		// numeric
	public $mwst; 				// numeric
	public $erhalten;  			// bool
	public $sort; 				// integer
	public $text; 				// bool
	public $insertamum; 		// timestamp
	public $insertvon; 			// char
	public $updateamum; 		// timestamp
	public $updatevon; 			// char
	
	public $result = array(); 
	public $user; 
	public $new; 
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $bestelldetail_id
	 */
	public function __construct($bestelldetail_id = null)
	{
		parent::__construct(); 
		
		if(!is_null($bestelldetail_id))
			$this->load($bestelldetail_id);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $bestelldetail_id
	 */
	public function load($bestelldetail_id)
	{
		if(!is_numeric($bestelldetail_id))
		{
			$this->errormsg ='Keine gültige Bestell ID.';
			return false; 
		}
		
		$qry = 'SELECT * FROM wawi.tbl_bestelldetail WHERE bestelldetail_id = '.$this->addslashes($bestelldetail_id).';'; 
		
		if(!$this->db_query($qry))
		{
			$this->errormsg = 'Fehler bei Datenbankabfrage.';
			return false; 
		}
		
		if($row = $this->db_fetch_object())
		{
			$this->bestelldetail_id = $row->bestelldetail_id;
			$this->bestellung_id = $row->bestellung_id; 
			$this->positoin = $row->position; 
			$this->menge = $row->menge; 
			$this->verpackungseinheit = $row->verpackungseinheit; 
			$this->beschreibung = $row->beschreibung; 
			$this->artikelnummer = $row->artikelnummer; 
			$this->preisprove = $row->preisprove; 
			$this->mwst = $row->mwst; 
			$this->erhalten = $row->erhalten; 
			$this->sort = $row->sort; 
			$this->text = $row->text; 
			$this->insertamum = $row->insertamum; 
			$this->insertvon = $row->insertvon; 
			$this->updateamum = $row->updateamum; 
			$this->updatevon = $row->updatevon; 
		}
		return true; 
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getAll()
	{
		$qry ='SELECT * FROM wawi.tbl_bestelldetail;';
		
		if(!$this->db_query($qry))
		{
			$this->errormsg = 'Fehler bei Datenbankabfrage.';
			return false; 
		}
		while($row = $this->db_fetch_object())
		{
			$detail = new wawi_bestelldetail();
			
			$detail->bestelldetail_id = $row->bestelldetail_id; 
			$detail->bestellung_id = $row->bestellung_id; 
			$detail->positoin = $row->position; 
			$detail->menge = $row->menge; 
			$detail->verpackungseinheit = $row->verpackungseinheit; 
			$detail->beschreibung = $row->beschreibung; 
			$detail->artikelnummer = $row->artikelnummer; 
			$detail->preisprove = $row->preisprove; 
			$detail->mwst = $row->mwst; 
			$detail->erhalten = $row->erhalten; 
			$detail->sort = $row->sort; 
			$detail->text = $row->text; 
			$detail->insertamum = $row->insertamum; 
			$detail->insertvon = $row->insertvon; 
			$detail->updateamum = $row->updateamum; 
			$detail->updatevon = $row->updatevon; 
			
			$detail->result = $detail; 
		}
		return true; 
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $bestelldetail_id
	 */
	public function delete($bestelldetail_id)
	{
		if(!is_numeric($bestelldetail_id))
		{
			$this->errormsg = 'Keine gültige Bestell ID.';
			return false;
		}
		
		$qry = 'DELETE FROM wawi.tbl_bestelldetail WHERE bestelldetail_id ='.$this->addslashes($bestelldetail_id).';';
		
		if(!$this->db_query($qry))
		{
			$this->errormsg = 'Fehler beim löschen des Betstelldetails.';
			return false; 
		}
		return true; 
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function validate()
	{
		
		if(!is_numeric($this->bestelldetail_id))
		{
			$this->errormsg = "Bestelldetail_id fehlerhaft.";
			return false;
		}
		if(!is_numeric($this->bestellung_id))
		{
			$this->errormsg = "Bestellung_id fehlerhaft.";
			return false;
		}
		if(!is_numeric($this->position))
		{
			$this->errormsg = "Position fehlerhaft.";
			return false;
		}		
		if(!is_numeric($this->menge))
		{
			$this->errormsg = "Menge fehlerhaft.";
			return false;
		}
		if(mb_strlen($this->verpackungseinheit)>16)
		{
			$this->errormsg ="Verpackungseinheit fehlerhaft.";
			return false; 
		}
		if(mb_strlen($this->artikelnummer)>32)
		{
			$this->errormsg ="Artikelnummer fehlerhaft.";
			return false; 
		}		
	
		return true; 		
	}
	
	
	public function save()
	{
		if(!$this->validate())
			return false; 
			
		if($this->new)
		{
			// insert
			$qry ='BEGIN; INSERT INTO wawi.tbl_bestelldetail (bestellung_id, position, menge, verpackungseinheit, beschreibung, artikelnummer, 
			preisprove, mwst, erhalten, sort, text, insertamum, insertvon, updateamum, updatevon) VALUES ('.
			$this->addslashes($this->bestellung_id).', '.
			$this->addslashes($this->position).', '.
			$this->addslashes($this->menge).', '.
			$this->addslashes($this->verpackungseinheit).', '.
			$this->addslashes($this->beschreibung).', '.
			$this->addslashes($this->artikelnummer).', '.
			$this->addslashes($this->preisprove).', '.
			$this->addslashes($this->mwst).', '.
			$this->addslashes($this->erhalten).', '.
			$this->addslashes($this->sort).', '.
			$this->addslashes($this->text).', '.
			$this->addslashes($this->insertamum).', '.
			$this->addslashes($this->insertvon).', '.
			$this->addslashes($this->updateamum).', '.
			$this->addslashes($this->updatevon).'); ';
			
		}
		else
		{
			// Update
			$qry = 'UPDATE wawi.tbl_bestellung SET 
			bestellung_id = '.$this->bestellung_id.',
			position = '.$this->position.',
			menge = '.$this->menge.',
			verpackungseinheit = '.$this->verpackungseinheit.',
			beschreibung = '.$this->beschreibung.',
			artikelnummer = '.$this->artikelnummer.',
			preisprove = '.$this->preisprove.',
			mwst = '.$this->mwst.',
			erhalten = '.$this->erhalten.',
			sort = '.$this->sort.',
			text = '.$this->text.',
			insertamum = '.$this->insertamum.',
			insertvon = '.$this->insertvon.',
			updateamum = '.$this->updateamum.',
			updatevon = '.$this->updatevon.' WHERE bestelldetail_id = '.$this->bestelldetail_id.';'; 
		}
		
		if($this->db_query($qry))
		{
			if($this->new)
			{
				//aktuelle Sequence holen
				$qry="SELECT currval('seq_bestelldetail_bestelldetail_id') as id;";
				if($this->db_query($qry))
				{
					if($row = $this->db_fetch_object())
					{
						$this->bestelldetail_id = $row->id;						
						$this->db_query('COMMIT');
					}
					else
					{
						$this->db_query('ROLLBACK');
						$this->errormsg = "Fehler beim Auslesen der Sequence";
						return false;
					}
				}
				else
				{
					$this->db_query('ROLLBACK');
					$this->errormsg = 'Fehler beim Auslesen der Sequence';
					return false;
				}
			}
		}
		else
		{
			return false;
		}
		return $this->bestelldetail_id;
		
		
	}
	
}