<?php
/* Copyright (C) 2017 fhcomplete.org
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
 * Authors: Andreas Oesterreicher 	<andreas.oesterreicher@technikum-wien.at>
 */
/**
 * Gui zum aktivieren der Zutrittskarte
 * Hier wird die neue Karte einmal über den Kartenleser gezogen zum das Ausgabedatum zu setzen
 */
require_once('../../config/vilesci.config.inc.php');
require_once('../../include/functions.inc.php');
require_once('../../include/person.class.php');
require_once('../../include/benutzer.class.php');
require_once('../../include/student.class.php');
require_once('../../include/studiengang.class.php');
require_once('../../include/betriebsmittel.class.php');
require_once('../../include/betriebsmittelperson.class.php');
require_once('../../include/benutzerberechtigung.class.php');

$uid = get_uid();

$rechte = new benutzerberechtigung();
$rechte->getBerechtigungen($uid);

echo '<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="../../skin/fhcomplete.css" type="text/css">
	<link rel="stylesheet" href="../../skin/vilesci.css" type="text/css">
	<link rel="stylesheet" href="../../skin/jquery.css" type="text/css"/>
	<script type="text/javascript" src="../../include/js/jquery.js"></script>
	<title>Kartentausch</title>
</head>
<body>
<h2>Zutrittskarte - Ruecknahme</h2>';

if(!$rechte->isBerechtigt('basis/fhausweis', 'suid'))
	die('Sie haben keine Berechtigung für diese Seite');

$db = new basis_db();
$kartennummer = (isset($_POST['kartennummer'])?$_POST['kartennummer']:'');
$action=(isset($_POST['action'])?$_POST['action']:'');

if ($action == 'kartenruecknahme')
{
	$bmp = new betriebsmittelperson();
	if ($bmp->getKartenzuordnung($kartennummer))
	{
		if ($bmp->uid != '')
		{
			$karten_user = $bmp->uid;

			$benutzer = new benutzer();
			if(!$benutzer->load($karten_user))
			{
				echo '<span class="error">Fehler beim Laden des Benutzers</span>';
			}
			else
			{
				$error=false;
				//Neue Karte aktivieren
				$bmp = new betriebsmittelperson();
				if ($bmp->getKartenzuordnungPerson($benutzer->person_id, $kartennummer))
				{
					if ($bmp->ausgegebenam != '' && $bmp->retouram == '')
					{
						$bmp->retouram=date('Y-m-d');
						$bmp->updateamum = date('Y-m-d H:i:s');
						$bmp->updatevon = $uid;

						if(!$bmp->save(false))
						{
							echo '<span class="error">Fehler beim Tauschen: '.$bmp->errormsg.'</span>';
							$error=true;
						}
						else
							echo '<span class="ok">Karte wurde erfolgreich ausgetragen.</span><br>
							<table>
								<tr>
									<td>
										<img src="../../content/bild.php?src=person&person_id='.$benutzer->person_id.'"
										height="100px" width="75px"/>
									</td>
									<td>
										Vorname: '.$benutzer->vorname.'<br>
										Nachname: '.$benutzer->nachname.'<br>
										UID: <b>'.$benutzer->uid.'<br>
									</td>
								</tr>
							</table>';
					}
					else
					{
						echo '<span>Karte ist nicht ausgegeben oder wurde bereits retourniert</span>';
					}
				}
				else
				{
					echo '
						<span class="error">
						Fehler beim Tauschen: Die Karte wurde dieser
						Person noch nicht zugeordnet ('.$benutzer->uid.' '.$kartennummer.')
						</span>';
					$error = true;
				}
			}
		}
		else
		{
			echo '<span class="error">Diese Karte ist derzeit nicht zugewiesen</span>';
		}
	}
	else
	{
		echo '<span class="error">Diese Karte ist derzeit nicht zugewiesen</span>';
	}

	echo '<br><hr><br>';
}

echo '
Ziehen Sie die neue Karte über den Hitag Kartenleser um die Karte zu deaktivieren:
<script type="text/javascript">
	$(document).ready(function()
	{
		$("#kartennummer").val("");
		$("#kartennummer").focus();
	});
</script>
<br><br>
<form action="'.$_SERVER['PHP_SELF'].'" METHOD="POST">
	<input type="hidden" name="action" value="kartenruecknahme" />
	Kartennummer:
	<input type="text" id="kartennummer" name="kartennummer"/>
	<input type="submit" name="retournieren" value="Karte austragen" />
</form>
';

echo '</body>
</html>';
?>
