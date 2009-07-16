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
 * Authors: Christian Paminger 	< christian.paminger@technikum-wien.at >
 *          Andreas Oesterreicher 	< andreas.oesterreicher@technikum-wien.at >
 *          Rudolf Hangl 		< rudolf.hangl@technikum-wien.at >
 *          Gerald Simane-Sequens 	< gerald.simane-sequens@technikum-wien.at >
 */
 
	require_once('../../config/vilesci.config.inc.php');
	require_once('../../include/betriebsmittel.class.php');
	require_once('../../include/betriebsmittelperson.class.php');
	require_once('../../include/globals.inc.php');
	require_once('../../include/functions.inc.php');
	require_once('../../include/benutzerberechtigung.class.php');
	require_once('../../include/variable.class.php');
	require_once('../../include/person.class.php');
	require_once('../../include/benutzer.class.php');
	require_once('../../include/studiensemester.class.php');
		if (!$db = new basis_db())
				die('Es konnte keine Verbindung zum Server aufgebaut werden.');
	

	$user = get_uid();
	$rechte = new benutzerberechtigung();
	$rechte->getBerechtigungen($user);
	
	
	
	if(!$rechte->isBerechtigt('admin'))
		die('Sie haben keine Rechte für diese Seite');
	
	$reloadstr = "";  // neuladen der liste im oberen frame
	$htmlstr = "";
	$errorstr = ""; //fehler beim insert
		
	if (isset($_REQUEST['betriebsmittel_id']))
		$betriebsmittel_id =$_REQUEST['betriebsmittel_id'];
	if (isset($_REQUEST['person_id']))
		$person_id =$_REQUEST['person_id'];
	
	$uid = isset($_REQUEST['uid'])?$_REQUEST['uid']:'';
	$wert = isset($_REQUEST['wert'])?$_REQUEST['wert']:'';
	
	$bmbetriebsmitteltyp=isset($_POST["bmbetriebsmitteltyp"])?$_POST["bmbetriebsmitteltyp"]:'';
	$bmbeschreibung=isset($_POST["bmbeschreibung"])?$_POST["bmbeschreibung"]:'';
	$bmnummer=isset($_POST["bmnummer"])?$_POST["bmnummer"]:'';
	$bmnummerintern=isset($_POST["bmnummerintern"])?$_POST["bmnummerintern"]:'';
	$bmreservieren=isset($_POST["bmreservieren"])?$_POST["bmreservieren"]:'';
	$bmort_kurzbz=isset($_POST["bmort_kurzbz"])?$_POST["bmort_kurzbz"]:'';
	$bmext_id=isset($_POST["bmext_id"])?$_POST["bmext_id"]:'';
  
   	$bmupdatevon=isset($_POST["bmupdatevon"])?$_POST["bmupdatevon"]:$user;
   	$bminsertvon=isset($_POST["bminsertvon"])?$_POST["bminsertvon"]:$user;
	
	$bmpausgegebenam=isset($_POST["bmpausgegebenam"])?$_POST["bmpausgegebenam"]:'';
	$bmpretouram=isset($_POST["bmpretouram"])?$_POST["bmpretouram"]:'';
	$bmpkaution=isset($_POST["bmpkaution"])?$_POST["bmpkaution"]:'';
	$bmpanmerkung=isset($_POST["bmpanmerkung"])?$_POST["bmpanmerkung"]:'';
	$bmpext_id=isset($_POST["bmpext_id"])?$_POST["bmpext_id"]:'';

   	$bmpupdatevon=isset($_POST["bmpupdatevon"])?$_POST["bmpupdatevon"]:$user;
   	$bmpinsertvon=isset($_POST["bmpinsertvon"])?$_POST["bmpinsertvon"]:$user;
	
	if(isset($_GET['standard']))
	{
		$stsem_obj = new studiensemester();
		$stsem = $stsem_obj->getaktorNext();
		
		$qrys = array(
			"Insert into public.tbl_variable(name, uid, wert) values('semester_aktuell','$uid','$stsem');",
			"Insert into public.tbl_variable(name, uid, wert) values('db_stpl_table','$uid','stundenplandev');",
			"Insert into public.tbl_variable(name, uid, wert) values('ignore_kollision','$uid','false');",
			"Insert into public.tbl_variable(name, uid, wert) values('kontofilterstg','$uid','false');",
			"Insert into public.tbl_variable(name, uid, wert) values('ignore_zeitsperre','$uid','false');",
			"Insert into public.tbl_variable(name, uid, wert) values('ignore_reservierung','$uid','false');"
			);
					
		$error = false;
		foreach ($qrys as $qry)
		{
			if(!$db->db_query($qry))
			{
				$error = true;
			}
		}
		
		if($error)
		{
			$errorstr.="Es konnten nicht alle Werte angelegt werden";
		}
		
		$reloadstr .= "<script type='text/javascript'>\n";
		$reloadstr .= "	parent.uebersicht.location.href='variablen_uebersicht.php';";
		$reloadstr .= "</script>\n";
	}
	
	if(isset($_POST["schick"]))
	{
		if($betriebsmittel_id!='')
		{
			$bm=new betriebsmittel();
			$bm->betriebsmittel_id=$betriebsmittel_id;
			$bm->betriebsmitteltyp=$bmbetriebsmitteltyp;
			$bm->nummer=$bmnummer;
			$bm->nummerintern=$bmnummerintern;
			$bm->beschreibung=$bmbeschreibung;
			$bm->ort_kurzbz=$bmort_kurzbz;
			$bm->reservieren=$bmreservieren;
			$bm->insertvon=$bminsertvon;
			$bm->updatevon=$bmupdatevon;
			$bm->ext_id=$bmext_id;
			
			if(!$bm->save())
			{
				$reloadstr.="<br>Aktualisierung des Betriebsmittel-Datensatzes fehlgeschlagen!";
			}
			else 
			{
				$reloadstr.="<br>Betriebsmittel-Datensatz wurde aktualisiert.";
			}
			if($person_id!='')
			{
				$bmp=new betriebsmittelperson();
				$bmp->betriebsmittel_id=$betriebsmittel_id;
				$bmp->person_id=$person_id;
				$bmp->ausgegebenam=$bmpausgegebenam;
				$bmp->retouram=$bmpretouram;
				$bmp->kaution=$bmpkaution;
				$bmp->anmerkung=$bmpanmerkung;
				$bmp->ext_id=$bmpext_id;
			     	$bmp->updatevon=$bmpupdatevon;
			     	$bmp->insertvon=$bmpinsertvon;
				if(!$bmp->save())
				{
					$reloadstr.="<br>Aktualisierung des Betriebsmittelperson-Datensatzes fehlgeschlagen!";
				}
				else 
				{
					$reloadstr.="<br>Betriebsmittelperson-Datensatz wurde aktualisiert.";
				}
			}
		}
	}
	
	
	if (isset($person_id) && isset($betriebsmittel_id))
	{

		$bm=new betriebsmittel($betriebsmittel_id);
		$bmp=new betriebsmittelperson($betriebsmittel_id,$person_id);
	
	
	
		$htmlstr .= "<table style='padding-top:10px;'>\n";
		$htmlstr .= "<form action='' method='POST'>\n";
		$htmlstr .= "	<tr>\n";
		$htmlstr .= "		<td><b>BM-ID </b><input type='hidden' name='bmbetriebsmittel' value='$bm->betriebsmittel_id'>".$bm->betriebsmittel_id."</td>\n";
		$htmlstr .= "		<td><b>Betriebsmitteltyp </b><input type='text' name='bmbetriebsmitteltyp' value='".$bm->betriebsmitteltyp."' size='15' maxlength='64'></td>\n";

		$htmlstr .= "		<td><b>Nummer &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b><input type='text' name='bmnummer' value='".$bm->nummer."' size='15' maxlength='64'></td>\n";
		$htmlstr .= "		<td><b>Nummer intern </b><input type='text' name='bmnummerintern' value='".$bm->nummerintern."' size='15' maxlength='64'></td>\n";
		$htmlstr .= "	</tr><tr>";
		$htmlstr .= "		<td><b>Beschreibung </b><input type='text' name='bmbeschreibung' value='".$bm->beschreibung."' size='15' maxlength='64'></td>\n";
		$htmlstr .= "		<td><b>Ort Kurzbz &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>
							<input type='text' name='bmort_kurzbz' value='".$bm->ort_kurzbz."' size='15' maxlength='64'></td>\n";
		$htmlstr .= "		<td><b>reservieren </b><input type='text' name='bmreservieren' value='".$bm->reservieren."' size='15' maxlength='64'></td>\n";
		$htmlstr .= "	</tr><tr>";
		$htmlstr .= "		<td><b>insertamum </b>".$bm->insertamum."</td>\n";
		$htmlstr .= "		<td><b>insertvon </b><input type='hidden' name='bminsertvon' value='".$bm->insertvon."'>".$bm->insertvon."</td>\n";

		$htmlstr .= "		<td><b>updateamum </b>".$bm->updateamum."</td>\n";
		$htmlstr .= "		<td><b>updatevon </b><input type='hidden' name='bmupdatevon' value='".$user."'>".$bm->updatevon."</td>\n";
		$htmlstr .= "		<td><b>ext_id </b><input type='hidden' name='bmext_id' value='".$bm->ext_id."'>".$bm->ext_id."</td>\n";
		$htmlstr .= "	</tr><tr>";
		$htmlstr .= "		<td>&nbsp;</td>\n";
		$htmlstr .= "	</tr><tr>";
		$htmlstr .= "	</tr>\n";
		

		
		
		$htmlstr .= "	<tr>\n";
		$htmlstr .= "		<td><b>P-ID </b><input type='hidden' name='bmpperson_id' value='$bmp->person_id'>".$bmp->person_id."</td>\n";
		$htmlstr .= "		<td><b>ausgegeben am </b><input type='text' name='bmpausgegebenam' value='".$bmp->ausgegebenam."' size='15' maxlength='64'></td>\n";
		$htmlstr .= "		<td><b>retour am </b><input type='text' name='bmpretouram' value='".$bmp->retouram."' size='15' maxlength='64'></td>\n";
		$htmlstr .= "		<td><b>Kaution </b><input type='text' name='bmpkaution' value='".$bmp->kaution."' size='15' maxlength='64'></td>\n";
		$htmlstr .= "	</tr><tr>";
		$htmlstr .= "		<td><b>Anmerkung </b><input type='bmpanmerkung' name='bmpanmerkung' value='".$bmp->anmerkung."' size='15' maxlength='64'></td>\n";
		$htmlstr .= "	</tr><tr>";
		$htmlstr .= "		<td><b>insertamum </b>".$bmp->insertamum."</td>\n";
		$htmlstr .= "		<td><b>insertvon </b><input type='hidden' name='bmpinsertvon' value='$bm->insertvon'>".$bmp->insertvon."</td>\n";
		$htmlstr .= "		<td><b>updateamum </b>".$bmp->updateamum."</td>\n";
		$htmlstr .= "		<td><b>updatevon </b><input type='hidden' name='bmpupdatevon' value='$user'>".$bmp->updatevon."</td>\n";
		$htmlstr .= "		<td><b>ext_id </b><input type='hidden' name='bmpext_id' value='$bm->ext_id'>".$bmp->ext_id."</td>\n";
		$htmlstr .= "	</tr><tr>";
		$htmlstr .= "		<td><input type='submit' name='schick' value='speichern'></td>";
		$htmlstr .= "		<td><input type='submit' name='del' value='l&ouml;schen'></td>";
		$htmlstr .= "	</tr>\n";
		$htmlstr .= "</form>\n";
		$htmlstr .= "</table>\n";
	}
	$htmlstr .= "<div class='inserterror'>".$errorstr."</div>\n";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Betriebsmitel-Details</title>
<link rel="stylesheet" href="../../skin/vilesci.css" type="text/css">
<script src="../../include/js/mailcheck.js"></script>
<script src="../../include/js/datecheck.js"></script>
<script type="text/javascript">

function confdel()
{
	if(confirm("Diesen Datensatz wirklick loeschen?"))
	  return true;
	return false;
}

</script>
</head>
<body style="background-color:#eeeeee;">
<h2>Betriebsmittel - Details</h2>
<?php
	echo $htmlstr;
	echo $reloadstr;
?>

</body>
</html>
