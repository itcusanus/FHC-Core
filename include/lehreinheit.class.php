<?php
/* Copyright (C) 2006 Technikum-Wien
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
 *          Rudolf Hangl <rudolf.hangl@technikum-wien.at>.
 */

class lehreinheit
{
	var $conn;     // resource DB-Handle
	var $errormsg; // string
	var $new;      // boolean
	var $lehreinheiten = array(); // lehreinheit Objekt

	//Tabellenspalten
	var $lehreinheit_id;			// integer
	var $lehrveranstaltung_id;		// integer
	var $studiensemester_kurzbz; 	// varchar(16)
	var $lehrfach_id;				// integer
	var $lehrform_kurzbz;			// varchar(8)
	var $stundenblockung;			// smalint
	var $wochenrythmus;				// smalint
	var $start_kw;					// smalint
	var $raumtyp;					// varchar(8)
	var $raumtypalternativ;			// varchar(8)
	var $lehre;						// boolean
	var $anmerkung;					// varchar(255)
	var $unr;						// integer
	var $lvnr;						// bigint
	var $insertamum;				// timestamp
	var $insertvon;					// varchar(16)
	var $updateamum;				// timestamp
	var $updatevon;					// varchar(16)
	var $sprache;					// varchar(16)
	var $ext_id;					// bigint

	// *************************************************************************
	// * Konstruktor - Uebergibt die Connection und laedt optional eine LE
	// * @param $conn        	Datenbank-Connection
	// *        $gruppe_kurzbz
	// *        $unicode     	Gibt an ob die Daten mit UNICODE Codierung
	// *                     	oder LATIN9 Codierung verarbeitet werden sollen
	// *************************************************************************
	function lehreinheit($conn, $lehreinheit_id=null, $unicode=false)
	{
		$this->conn = $conn;

		if($unicode)
			$qry = "SET CLIENT_ENCODING TO 'UNICODE';";
		else
			$qry = "SET CLIENT_ENCODING TO 'LATIN9';";

		if(!pg_query($conn,$qry))
		{
			$this->errormsg	 = 'Encoding konnte nicht gesetzt werden';
			return false;
		}

		if($lehreinheit_id!=null)
			$this->load($lehreinheit_id);
	}

	// *********************************************************
	// * Laedt die LE
	// * @param lehreinheit_id
	// *********************************************************
	function load($lehreinheit_id)
	{
		if(!is_numeric($lehreinheit_id))
		{
			$this->errormsg = 'Lehreinheit_id muss eine gueltige Zahl sein';
		}

		$qry = "SELECT * FROM lehre.tbl_lehreinheit WHERE lehreinheit_id='$lehreinheit_id'";

		if($result = pg_query($this->conn, $qry))
		{
			if($row = pg_fetch_object($result))
			{
				$this->lehreinheit_id = $row->lehreinheit_id;
				$this->lehrveranstaltung_id = $row->lehrveranstaltung_id;
				$this->studiensemester_kurzbz = $row->studiensemester_kurzbz;
				$this->lehrfach_id = $row->lehrfach_id;
				$this->lehrform_kurzbz = $row->lehrform_kurzbz;
				$this->stundenblockung = $row->stundenblockung;
				$this->wochenrythmus = $row->wochenrythmus;
				$this->start_kw = $row->start_kw;
				$this->raumtyp = $row->raumtyp;
				$this->raumtypalternativ = $row->raumtypalternativ;
				$this->lehre = ($row->lehre=='t'?true:false);
				$this->anmerkung = $row->anmerkung;
				$this->unr = $row->unr;
				$this->lvnr = $row->lvnr;
				$this->sprache = $row->sprache;
				$this->insertamum = $row->insertamum;
				$this->insertvon = $row->insertvon;
				$this->updateamum = $row->updateamum;
				$this->updatevon = $row->updatevon;
				$this->ext_id = $row->ext_id;
			}
		}
		else
		{
			$this->errormsg = 'Fehler beim Laden der Lehreinheit';
			return false;
		}
	}

	function load_lehreinheiten($lehrveranstaltung_id, $studiensemester_kurzbz)
	{
		$qry = "SELECT * FROM lehre.tbl_lehreinheit WHERE lehrveranstaltung_id='$lehrveranstaltung_id' AND studiensemester_kurzbz='$studiensemester_kurzbz'";

		if($result = pg_query($this->conn, $qry))
		{
			while($row = pg_fetch_object($result))
			{
				$le_obj = new lehreinheit($this->conn);

				$le_obj->lehreinheit_id = $row->lehreinheit_id;
				$le_obj->lehrveranstaltung_id = $row->lehrveranstaltung_id;
				$le_obj->studiensemester_kurzbz = $row->studiensemester_kurzbz;
				$le_obj->lehrfach_id = $row->lehrfach_id;
				$le_obj->lehrform_kurzbz = $row->lehrform_kurzbz;
				$le_obj->stundenblockung = $row->stundenblockung;
				$le_obj->wochenrythmus = $row->wochenrythmus;
				$le_obj->start_kw = $row->start_kw;
				$le_obj->raumtyp = $row->raumtyp;
				$le_obj->raumtypalternativ = $row->raumtypalternativ;
				$le_obj->lehre = ($row->lehre=='t'?true:false);
				$le_obj->anmerkung = $row->anmerkung;
				$le_obj->unr = $row->unr;
				$le_obj->lvnr = $row->lvnr;
				$le_obj->sprache = $row->sprache;
				$le_obj->insertamum = $row->insertamum;
				$le_obj->insertvon = $row->insertvon;
				$le_obj->updateamum = $row->updateamum;
				$le_obj->updatevon = $row->updatevon;
				$le_obj->ext_id = $row->ext_id;

				$this->lehreinheiten[] = $le_obj;
			}
		}
		else
		{
			$this->errormsg = 'Fehler beim Laden der Lehreinheiten';
			return false;
		}
	}

	// *******************************************
	// * Prueft die Variablen vor dem Speichern
	// * auf Gueltigkeit.
	// * @return true wenn ok, false im Fehlerfall
	// *******************************************
	function validate()
	{
		if($this->lehreinheit_id!='' && !is_numeric($this->lehreinheit_id))
		{
			$this->errormsg = 'Lehreinheit_id muss eine gueltige Zahl sein';
			return false;
		}
		if(!is_numeric($this->lehrveranstaltung_id))
		{
			$this->errormsg = 'LehrveranstaltungsNr muss eine gueltige Zahl sein';
			return false;
		}
		if(strlen($this->studiensemester_kurzbz)>16)
		{
			$this->errormsg = 'Studiensemesterkurzbz darf nicht laenger als 16 Zeichen sein';
			return false;
		}
		if($this->studiensemester_kurzbz=='')
		{
			$this->errormsg = 'Studiensemester muss angegeben werden';
			return false;
		}
		if(!is_numeric($this->lehrfach_id))
		{
			$this->errormsg = 'Lehrfach_id muss eine gueltige Zahl sein';
			return false;
		}
		if(strlen($this->lehrform_kurzbz)>8)
		{
			$this->errormsg = 'Lehrform_kurzbz darf nicht laenger als 8 Zeichen sein';
			return false;
		}
		if($this->lehrform_kurzbz=='')
		{
			$this->lehrform_kurzbz='SO';
			//TODO
			//$this->errormsg = 'Lehrform muss angegeben werden';
			//return false;
		}
		if(!is_numeric($this->stundenblockung))
		{
			$this->errormsg = 'Stundenblockung muss eine gueltige Zahl sein';
			return false;
		}
		if(!is_numeric($this->wochenrythmus))
		{
			$this->errormsg = 'Wochenrythmus muss eine gueltige Zahl sein';
			return false;
		}
		if($this->start_kw!='' && !is_numeric($this->start_kw))
		{
			$this->errormsg = 'StartKW muss eine gueltige Zahl sein';
			return false;
		}
		if($this->start_kw!='' && ($this->start_kw>53 || $this->start_kw<1))
		{
			$this->errormsg = 'StartKW muss zwischen 1 und 53 liegen';
			return false;
		}
		if(strlen($this->raumtyp)>8)
		{
			$this->errormsg = 'Raumtyp darf nicht laenger als 8 Zeichen sein';
			return false;
		}
		if(strlen($this->raumtypalternativ)>8)
		{
			$this->errormsg = 'Raumtypalternativ darf nicht alenger als 8 Zeichen sein';
			return false;
		}
		if($this->raumtypalternativ=='')
		{
			//TODO
			$this->raumtypalternativ='Dummy';
		}
		if(!is_bool($this->lehre))
		{
			$this->errormsg = 'Lehre muss ein boolscher Wert sein';
			return false;
		}
		if(strlen($this->anmerkung)>255)
		{
			$this->errormsg = 'Anmerkung darf nicht laenger als 255 Zeichen sein';
			return false;
		}
		if($this->unr!='' && !is_numeric($this->unr))
		{
			$this->errormsg = 'UNR muss eine gueltige Zahl sein';
			return false;
		}
		if($this->ext_id!='' && !is_numeric($this->ext_id))
		{
			$this->errormsg = 'Ext_id muss eine gueltige Zahl sein';
			return false;
		}

		return true;
	}

	// ************************************************
	// * wenn $var '' ist wird NULL zurueckgegeben
	// * wenn $var !='' ist werden Datenbankkritische
	// * Zeichen mit Backslash versehen und das Ergbnis
	// * unter Hochkomma gesetzt.
	// ************************************************
	function addslashes($var)
	{
		return ($var!=''?"'".addslashes($var)."'":'null');
	}

	// ************************************************************
	// * Speichert LE in die Datenbank
	// * Wenn $new auf true gesetzt ist wird ein neuer Datensatz
	// * angelegt, ansonsten der Datensatz upgedated
	// * @return true wenn erfolgreich, false im Fehlerfall
	// ************************************************************
	function save($new=null)
	{
		if(is_null($new))
			$new = $this->new;

		//Variablen auf Gueltigkeit pruefen
		if(!$this->validate())
			return false;

		if($new)
		{
			//ToDo ID entfernen
			$qry = 'INSERT INTO lehre.tbl_lehreinheit (lehrveranstaltung_id, studiensemester_kurzbz,
			                                     lehrfach_id, lehrform_kurzbz, stundenblockung, wochenrythmus,
			                                     start_kw, raumtyp, raumtypalternativ, lehre, anmerkung, unr, lvnr, insertamum, insertvon, updateamum, updatevon,  ext_id, sprache)
			        VALUES('.$this->addslashes($this->lehrveranstaltung_id).','.
					$this->addslashes($this->studiensemester_kurzbz).','.
					$this->addslashes($this->lehrfach_id).','.
					$this->addslashes($this->lehrform_kurzbz).','.
					$this->addslashes($this->stundenblockung).','.
					$this->addslashes($this->wochenrythmus).','.
					$this->addslashes($this->start_kw).','.
					$this->addslashes($this->raumtyp).','.
					$this->addslashes($this->raumtypalternativ).','.
					($this->lehre?'true':'false').','.
					$this->addslashes($this->anmerkung).','.
					$this->addslashes($this->unr).','.
					$this->addslashes($this->lvnr).','.
					$this->addslashes($this->insertamum).','.
					$this->addslashes($this->insertvon).','.
					$this->addslashes($this->updateamum).','.
					$this->addslashes($this->updatevon).','.
					$this->addslashes($this->ext_id).','.
					$this->addslashes($this->sprache).');';
		}
		else
		{
			$qry = 'UPDATE lehre.tbl_lehreinheit SET'.
			       ' lehrveranstaltung_nr='.$this->addslashes($this->lehrveranstaltung_id).','.
			       ' studiensemester_kurzbz='.$this->addslashes($this->studiensemester_kurzbz).','.
			       ' lehrfach_id='.$this->addslashes($this->lehrfach_id).','.
			       ' lehrform_kurzbz='.$this->addslashes($this->lehrform_kurzbz).','.
			       ' stundenblockung='.$this->addslashes($this->stundenblockung).','.
			       ' wochenrythmus='.$this->addslashes($this->wochenrythmus).','.
			       ' start_kw='.$this->addslashes($this->start_kw).','.
			       ' raumtyp='.$this->addslashes($this->raumtyp).','.
			       ' raumtypalternativ='.$this->addslashes($this->raumtypalternativ).','.
			       ' lehre='.($this->lehre?'true':'false').','.
			       ' anmerkung='.$this->addslashes($this->anmerkung).','.
			       ' unr='.$this->addslashes($this->unr).','.
			       ' lvnr='.$this->addslashes($this->lvnr).','.
				   ' updateamum='.$this->addslashes($this->updateamum).','.
				   ' updatevon='.$this->addslashes($this->updatevon).','.
				   ' sprache='.$this->addslashes($this->sprache).','.
			       ' ext_id='.$this->addslashes($this->ext_id).
			       " WHERE lehreinheit_id=".$this->addslashes($this->lehreinheit_id).";";
		}

		if(pg_query($this->conn,$qry))
		{
			//Log schreiben
			return true;
		}
		else
		{
			$this->errormsg = 'Fehler beim Speichern der LE:'.$qry;
			return false;
		}
	}

	/*************************************************************************
	 * Prueft die geladene Lehrveranstaltung auf Kollisionen im Stundenplan.
	 * Rueckgabewert 'false' und die Fehlermeldung steht in '$this->errormsg'.
	 * @param string	datum	gewuenschtes Datum YYYY-MM-TT
	 * @param integer	stunde	gewuenschte Stunde
	 * @param string	ort		gewuenschter Ort
	 * @param string	db_stpl_table	Tabllenname des Stundenplans im DBMS
	 * @return boolean true=ok, false=fehler
	 *************************************************************************/
	function check_lva($datum,$stunde,$ort,$stpl_table)
	{
		// Parameter Checken
		// Bezeichnung der Stundenplan-Tabelle und des Keys
		$stpl_id=$stpl_table.TABLE_ID;
		$stpl_table=TABLE_BEGIN.$stpl_table;

		/*// Connection holen
		if (is_null($conn=$this->getConnection()))
		{
			return false;
		}*/

		// Datenbank abfragen
		$sql_query="SELECT $stpl_id FROM $stpl_table
					WHERE datum='$datum' AND stunde=$stunde
					AND ((ort_kurzbz='$ort' OR (uid='$this->lektor' AND uid!='_DummyLektor'))
					AND unr!=$this->unr)"; //AND lehrveranstaltung_id!=$this->lehrveranstaltung_id
		//$this->errormsg=$sql_query;
		if (! $erg_stpl=pg_query($this->conn, $sql_query))
		{
			$this->errormsg=pg_last_error($this->conn);
			//echo $this->errormsg;
			return false;
		}
		$anzahl=pg_numrows($erg_stpl);
		//Check
		if ($anzahl==0)
			return true;
		else
		{
			$row=pg_fetch_row($erg_stpl);
			$this->errormsg="Kollision mit StundenplanID($stpl_table.$stpl_id): $row[0]";
			return false;
		}
	}

	/*************************************************************************
	 * Speichert die geladene Lehrveranstaltung im Stundenplan.
	 * Rueckgabewert 'false' und die Fehlermeldung steht in '$this->errormsg'.
	 * @param string	datum	gewuenschtes Datum YYYY-MM-TT
	 * @param integer	stunde	gewuenschte Stunde
	 * @param string	ort		gewuenschter Ort
	 * @param string	db_stpl_table	Tabllenname des Stundenplans im DBMS
	 * @param string	user	UID des aktuellen Bentzers
	 * @return boolean true=ok, false=fehler
	 *************************************************************************/
	function save_stpl($datum,$stunde,$ort,$stpl_table, $user)
	{
		// Parameter Checken
		// Bezeichnung der Stundenplan-Tabelle und des Keys
		$stpl_id=$stpl_table.TABLE_ID;
		$stpl_table=TABLE_BEGIN.$stpl_table;

		// Datenbank abfragen
		$sql_query="INSERT INTO $stpl_table
			(unr,uid,datum,	stunde,	ort_kurzbz,lehrfach_nr,lehrform_kurzbz,studiengang_kz,semester,verband,
			gruppe,	einheit_kurzbz,	titel, anmerkung, updatevon, lehrveranstaltung_id)
			VALUES ($this->unr,'$this->lektor','$datum',$stunde,
			'$ort',$this->lehrfach_nr, '$this->lehrform', $this->studiengang_kz,$this->semester,
			'$this->verband','$this->gruppe'";
		if ($this->einheit_kurzbz==null)
			$sql_query.=',NULL';
		else
			$sql_query.=",'$this->einheit_kurzbz'";
		$sql_query.=",'$this->titel','$this->anmerkung','$user',$this->lehrveranstaltung_id)";
		//$this->errormsg=$sql_query.'<br>';
		//return false;
		if (! $erg_stpl=pg_query($this->conn, $sql_query))
		{
			$this->errormsg=pg_last_error($this->conn);
			//echo $this->errormsg;
			return false;
		}
		return true;
	}

	/**
	 * Rueckgabewert ist ein Array mit den Ergebnissen. Bei Fehler false und die
	 * Fehlermeldung liegt in errormsg.
	 * Wenn der Parameter stg_kz NULL ist tritt einheit_kurzbzb in Kraft.
	 * @param string $einheit_kurzbz    Einheit
	 * @param string grp    Gruppe
	 * @param string ver    Verband
	 * @param integer sem    Semester
	 * @param integer stg_kz    Kennzahl des Studiengangs
	 * @return variabel Array mit LVA; <b>false</b> bei Fehler
	 */
	function getLehreinheitLVPL($db_stpl_table,$studiensemester, $type, $stg_kz, $sem, $lektor, $ver=null, $grp=null, $gruppe=null)
	{
		$lva_stpl_view=VIEW_BEGIN.'lva_'.$db_stpl_table;

		if (strlen($studiensemester)<=0)
		{
			$this->errormsg='Ausbildungssemester ist nicht gesetzt!';
			return false;
		}
		else $where=" studiensemester_kurzbz='$studiensemester'";

		if ($type=='lektor')
			$where.=" AND lektor_uid='$lektor'";
		elseif ($type=='einheit')
			$where.=" AND einheit='$einheit'";
		elseif ($type=='verband')
		{
			$where.=" AND studiengang_kz='$stg_kz'";
			if ($sem>0)
				$where.=" AND semester=$sem";
			if (strlen($ver)>0 && $ver!=' ')
				$where.=" AND verband='$ver'";
			if (strlen($grp)>0 && $grp!=' ')
				$where.=" AND gruppe='$grp' ";
		}
		$sql_query='SELECT *, semesterstunden-verplant::smallint AS offenestunden
			FROM lehre.'.$lva_stpl_view.' JOIN lehre.tbl_lehrform ON '.$lva_stpl_view.'.lehrform=tbl_lehrform.lehrform_kurzbz
			WHERE '.$where.' AND verplanen ORDER BY offenestunden DESC, lehrfach, lehrform, semester, verband, gruppe, gruppe_kurzbz;';
	    //$this->errormsg=$sql_query;
	    //return false;
		if(!($erg=@pg_exec($this->conn, $sql_query)))
		{
			$this->errormsg=pg_errormessage($this->conn);
			return false;
		}
		$num_rows=pg_numrows($erg);
		$l=array();
		for($i=0;$i<$num_rows;$i++)
		{
   			$row=pg_fetch_object($erg,$i);
			//$l[$row->unr]=new lehrveranstaltung();
			$l[$row->unr]->lehreinheit_id[]=$row->lehreinheit_id;
			$l[$row->unr]->lvnr[]=$row->lvnr;
			$l[$row->unr]->unr=$row->unr;
			$l[$row->unr]->fachbereich=$row->fachbereich_kurzbz;
			$l[$row->unr]->lehrfach_id=$row->lehrfach_id;
			$l[$row->unr]->lehrfach[]=$row->lehrfach;
			$l[$row->unr]->lehrfach_bez[]=$row->lehrfach_bez;
			$l[$row->unr]->lehrfach_farbe[]=$row->lehrfach_farbe;
			$l[$row->unr]->lehrform[]=$row->lehrform;
			$l[$row->unr]->lektor_uid[]=$row->lektor_uid;
			$l[$row->unr]->lektor[]=trim($row->lektor);
			$l[$row->unr]->stg_kz[]=$row->studiengang_kz;
			$l[$row->unr]->stg[]=$row->studiengang;
			$l[$row->unr]->gruppe_kurzbz[]=$row->gruppe_kurzbz;
			$l[$row->unr]->semester[]=$row->semester;
			$l[$row->unr]->verband[]=$row->verband;
			$l[$row->unr]->gruppe[]=$row->gruppe;
			$l[$row->unr]->gruppe_kurzbz[]=$row->gruppe_kurzbz;
			$l[$row->unr]->raumtyp=$row->raumtyp;
			$l[$row->unr]->raumtypalternativ=$row->raumtypalternativ;
			$l[$row->unr]->stundenblockung[]=$row->stundenblockung;
			$l[$row->unr]->wochenrythmus[]=$row->wochenrythmus;
			$l[$row->unr]->semesterstunden[]=$row->semesterstunden;
			$l[$row->unr]->start_kw[]=$row->start_kw;
			$l[$row->unr]->anmerkung[]=$row->anmerkung;
			$l[$row->unr]->studiensemester_kurzbz=$row->studiensemester_kurzbz;
			$l[$row->unr]->verplant[]=$row->verplant;
			$l[$row->unr]->offenestunden[]=$row->offenestunden;
			if (isset($l[$row->unr]->verplant_gesamt))
				$l[$row->unr]->verplant_gesamt+=$row->verplant;
			else
				$l[$row->unr]->verplant_gesamt=$row->verplant;
			$lvb=$row->studiengang.'-'.$row->semester;
			if ($row->verband!='' && $row->verband!=' ' && $row->verband!='0' && $row->verband!=null)
				$lvb.=$row->verband;
			if ($row->gruppe!='' && $row->gruppe!=' ' && $row->gruppe!='0' && $row->gruppe!=null)
				$lvb.=$row->gruppe;
			if ($row->gruppe_kurzbz!='' && $row->gruppe_kurzbz!=null)
				$l[$row->unr]->lehrverband[]=$row->gruppe_kurzbz;
			else
				$l[$row->unr]->lehrverband[]=$lvb;
		}
		return $l;
	}
}
?>