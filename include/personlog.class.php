<?php
/* Copyright (C) 2018 fhcomplete.org
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
 * Authors: Andreas Oesterreicher <andreas.oesterreicher@technikum-wien.at>
 */
require_once(dirname(__FILE__).'/basis_db.class.php');

class personlog extends basis_db
{
	public $new;      		// boolean
	public $logs = array(); // lehreinheit Objekt

	//Tabellenspalten
	public $log_id;				// Serial
	public $person_id;
	public $zeitpunkt;			// timestamp
	public $app;				// varchar(32)
	public $taetigkeit_kurzbz;  // varchar(32)
	public $oe_kurzbz;			// varchar(32)
	public $logtype_kurzbz;		// varchar(32)
	public $logdata;
	public $insertvon;

	/**
	 * Konstruktor
	 */
	public function __construct($log_id=null)
	{
		parent::__construct();
	}

	/**
	 * Erstellt einen Log Eintrag zu einer Person
	 * @param $person_id ID der Person.
	 * @param $logtype_kurzbz Typ des Logeintrages
	 * @param $logdata Array mit den zusaetzlichen Logdaten zu diesem Typ.
	 * @param $taetigkeit_kurzbz Kurzbz der Verarbeitungstaetigkeit.
	 * @param $app Applikation von der dieser Logeintrag stammt.
	 * @param $oe_kurzbz Kurzbz der Organisationseinheit. (optional)
	 * @param $user User der die Aktion durchgefuehrt hat. (optional)
	 * @return boolean true wenn erfolgreich, false im Fehlerfall.
	 */
	public function log($person_id, $logtype_kurzbz, $logdata, $taetigkeit_kurzbz, $app = 'core', $oe_kurzbz = null, $user = null)
	{
		$qry = "INSERT INTO system.tbl_log(person_id, zeitpunkt, taetigkeit_kurzbz, app, oe_kurzbz,
			logtype_kurzbz, logdata, insertvon)	VALUES(".
			$this->db_add_param($person_id).','.
			$this->db_add_param(date('Y-m-d H:i:s')).','.
			$this->db_add_param($taetigkeit_kurzbz).','.
			$this->db_add_param($app).','.
			$this->db_add_param($oe_kurzbz).','.
			$this->db_add_param($logtype_kurzbz).','.
			$this->db_add_param(json_encode($logdata)).','.
			$this->db_add_param($user).')';

		if($this->db_query($qry))
		{
			return true;
		}
		else
		{
			$this->errormsg = 'Fehler beim Speichern des Logeintrages';
			return false;
		}
	}
}
?>
