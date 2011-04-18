<?php
/* Copyright (C) 2011 FH Technikum Wien
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
 *          Karl Burkhart <karl.burkhart@technikum-wien.at>.
 */
require_once('../config/cis.config.inc.php');
require_once('../include/content.class.php');
require_once('../include/template.class.php');
require_once('../include/functions.inc.php');
require_once('../include/sprache.class.php');
require_once('../include/gruppe.class.php');
require_once('../include/datum.class.php');
require_once('../include/xsdformprinter/xsdformprinter.php');
require_once('../include/organisationseinheit.class.php');
require_once('../include/benutzerberechtigung.class.php');
require_once('../include/DifferenceEngine/DifferenceEngine.php');

$user = get_uid();

$rechte = new benutzerberechtigung();
$rechte->getBerechtigungen($user);

if(!$rechte->isBerechtigt('basis/cms'))
	die('Sie haben keine Berechtigung fuer diese Seite');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>FH Complete CMS ContentEditor</title>
	<link href="../skin/tablesort.css" rel="stylesheet" type="text/css"/>
	<link href="../skin/jquery.css" rel="stylesheet" type="text/css"/>
	<link href="../skin/fhcomplete.css" rel="stylesheet" type="text/css">
	<link href="../skin/style.css.php" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../include/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript" src="../include/js/jquery.js"></script>
		
	<script type="text/javascript">

	tinyMCE.init
	(
		{
		mode : "textareas",
		theme : "advanced",
		file_browser_callback: "FHCFileBrowser",
		
		plugins : "spellchecker,pagebreak,style,layer,table,advhr,advimage,advlink,inlinepopups,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
			
		// Theme options
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,|,print,|,ltr,rtl,|,fullscreen",
        //theme_advanced_buttons4 : "insertfile,insertimage",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "center",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,
			
		}
	);
	function FHCFileBrowser(field_name, url, type, win) 
	{
		cmsURL = "<?php echo APP_ROOT;?>cms/tinymce_dms.php?type="+type;
		tinyMCE.activeEditor.windowManager.open({
			file: cmsURL,
			title : "FHComplete File Browser",
			width: 750,
			height: 550,
			resizable: "yes",
			close_previous: "no",
			scrollbars: "yes",
			popup_css : false
		},{
			window: win,
			input: field_name
		});
		return false;
	}
	</script>
</head>

<body>
<?php

$sprache = isset($_GET['sprache'])?$_GET['sprache']:DEFAULT_LANGUAGE;
$version = isset($_GET['version'])?$_GET['version']:null;
$content_id = isset($_GET['content_id'])?$_GET['content_id']:null;
$action = isset($_GET['action'])?$_GET['action']:'';
$method = isset($_GET['method'])?$_GET['method']:null;
$message = '';
$submenu_depth=0;

//Inhalt Speichern
if(isset($_POST['XSDFormPrinter_XML']))
{
	$content = new content();
	$content->getContent($content_id, $sprache, $version);

	
	if($content->saveContent($content->contentsprache_id, $_POST['XSDFormPrinter_XML']))
		$message.= '<span class="ok">Inhalt wurde erfolgreich gespeichert</span>';
	else
		$message.= '<span class="error">'.$content->errormsg.'</span>';
}

if(!is_null($method))
{
	switch($method)
	{
		case 'add_new_content':
			$oe = new organisationseinheit();
			$oe->getAll();
			if(!isset($oe->result[0]))
				die('Es ist keine Organisationseinheit vorhanden');
				
			$template = new template();
			$template->getAll();
			if(!isset($template->result[0]))
				die('Es ist kein Template vorhanden');
			
			$content = new content();
			$content->new = true;
			$content->oe_kurzbz=$oe->result[0]->oe_kurzbz;
			$content->template_kurzbz=$template->result[0]->template_kurzbz;
			$content->titel = 'Neuer Eintrag';
			$content->content = '<?xml version="1.0" encoding="UTF-8" ?><content></content>';		
			$content->sichtbar=false;
			$content->version='0';
			$content->sprache='German';
			$content->insertvon = $user;
			$content->insertamum = date('Y-m-d H:i:s');
			
			if($content->save())
			{
				$message .= '<span class="ok">Eintrag wurde erfolgreich angelegt</span>';
				$action='prefs';
				$content_id=$content->content_id;
			}
			else
				$message .= '<span class="error">'.$content->errormsg.'</span>';

			break;
		case 'rights_add_group':
			if(!isset($_POST['gruppe_kurzbz']))
				die('Fehlender Parameter');
			
			$content = new content();
			$content->gruppe_kurzbz = $_POST['gruppe_kurzbz'];
			$content->insertamum = date('Y-m-d H:i:s');
			$content->insertvon = $user;
			$content->content_id=$content_id;
			
			if(!$content->addGruppe())
				$message .= '<span class="error">'.$content->errormsg.'</span>';
			else
				$message .= '<span class="ok">Gruppe wurde erfolgreich hinzugefügt</span>';
			
			break;
		case 'rights_delete_group':
			if(!isset($_GET['gruppe_kurzbz']))
				die('Fehlender Parameter');
			
			$content = new content();
			if(!$content->deleteGruppe($content_id, $_GET['gruppe_kurzbz']))
				$message .= '<span class="error">'.$content->errormsg.'</span>';
			else
				$message .= '<span class="ok">Gruppe wurde erfolgreich entfernt</span>';
			
			break;
		case 'prefs_save':
			$content = new content();
			$titel = $_POST['titel'];
			$oe_kurzbz=$_POST['oe_kurzbz'];
			$sichtbar=isset($_POST['sichtbar']);
			$template_kurzbz = $_POST['template_kurzbz'];
			
			if($content->getContent($content_id, $sprache, $version))
			{
				$content->titel = $titel;
				$content->oe_kurzbz = $oe_kurzbz;
				$content->sichtbar = $sichtbar;
				$content->template_kurzbz = $template_kurzbz;
				$content->updateamum=date('Y-m-d H:i:s');
				$content->updatevon=$user;
				
				if($content->save())
					$message.='<span class="ok">Daten erfolgreich gespeichert</span>';
				else
					$message.='<span class="error">'.$content->errormsg.'</span>';
			}
			else
				$message.='<span class="error">'.$content->errormsg.'</span>';
			break;
		case 'childs_add':
			$content = new content();
			$content->content_id = $content_id;
			$content->child_content_id = $_POST['child_content_id'];
			$content->insertamum = date('Y-m-d');
			$content->insertvon = $user;
			if($content->addChild())
				$message.='<span class="ok">Daten erfolgreich gespeichert</span>';
			else
				$message.='<span class="error">'.$content->errormsg.'</span>';
			break;
		case 'childs_delete':
			if(isset($_GET['contentchild_id']))
			{
				$contentchild_id = $_GET['contentchild_id'];
				$content = new content();
				if($content->deleteChild($contentchild_id))
					$message.='<span class="ok">Zuordnung wurde erfolgreich entfernt</span>';
				else
					$message.='<span class="error">'.$content->errormsg.'</span>';				
			}
			else
			{
				$message.='<span class="error">Fehler: ID wurde nicht uebergeben</span>';
			}
			break;
		default: break;
	}
}
//Menue Baum
echo '<table width="100%">
	<tr>
		<td colspan="2">
		<h1>FH Complete CMS</h1>
		</td>
	</tr>
	<tr>
		<td valign="top" width="200px">';


$db = new basis_db();

echo '
<a href="'.$_SERVER['PHP_SELF'].'?action=prefs&method=add_new_content">Neuen Eintrag hinzufügen</a>
<br><br>
<table class="treetable">';
$qry = "SELECT * FROM (
				SELECT 
					distinct on(content_id) *					 
				FROM 
					campus.tbl_content
					LEFT JOIN campus.tbl_contentchild USING(content_id)
				WHERE content_id NOT IN (SELECT child_content_id FROM campus.tbl_contentchild WHERE child_content_id=tbl_content.content_id)
				) as a
				ORDER BY contentchild_id, titel";
if($result = $db->db_query($qry))
{
	
	while($row = $db->db_fetch_object($result))
	{
		echo '<tr>';
		$content = new content();
	
		echo '<td>';
		drawmenulink($row->content_id, $row->titel);
		echo '</td>';
		$submenu_depth=0;
		drawsubmenu($row->content_id);
		echo '</tr>';
	}
	
}

echo '</table>';

echo '</td><td valign="top">';

//Editieren
if(!is_null($content_id))
{
	echo get_content_link('prefs','Eigenschaften').' | ';
	echo get_content_link('content','Inhalt').' | ';
	echo get_content_link('preview','Vorschau').' | ';
	echo get_content_link('rights','Rechte').' | ';
	echo get_content_link('childs','Childs').' | ';
	echo get_content_link('history','History');
	
	echo '<div style="float: right;">'.$message.'</div>';
	echo '<br><br>';

	
	switch($action)
	{
		case 'prefs':
					print_prefs(); 
					break;
		case 'content': 
					print_content();
					break;
		case 'preview': 
					echo '<iframe src="content.php?content_id='.$content_id.'&version='.$version.'&sprache='.$sprache.'" style="width: 600px; height: 500px; border: 1px solid black;">';
					break;
		case 'rights': 
					print_rights();
					break;
		case 'childs':
					print_childs();
					break;
		case 'history':
					print_history();
					break;
		default: break;
	}
	
}
echo '</td></tr></table>';
echo '</body>
</html>';

/******* FUNCTIONS **********/

/**
 * Gibt einen Menue Link aus
 * @param $id
 * @param $titel
 */
function drawmenulink($id, $titel)
{
	global $content_id, $action, $sprache, $version;
	echo '<a href="admin.php?content_id='.$id.'&action='.$action.'&sprache='.$sprache.'&version='.$version.'" '.($content_id==$id?'class="marked"':'').'>'.$titel.'</a> ('.$id.')';
}

/**
 * Zeichnet ein Submenue unterhalb eines Contents
 * 
 * @param $content_id Content ID des Parents
 * @param $einrueckung Einrueckungszeichen fuer den Content
 */
function drawsubmenu($content_id, $einrueckung="&nbsp;&nbsp;")
{
	global $db, $action, $submenu_depth;
	$submenu_depth++;
	if($submenu_depth>20)
	{
		echo 'Menürekursion?! -> Abbruch';
		return 0;
	}
	$qry = "SELECT 
				tbl_contentchild.content_id,
				tbl_contentchild.child_content_id,
				tbl_content.titel
			FROM
				campus.tbl_contentchild
				JOIN campus.tbl_content ON(tbl_contentchild.child_content_id=tbl_content.content_id)
			WHERE
				tbl_contentchild.content_id='".addslashes($content_id)."'";
	if($result = $db->db_query($qry))
	{
		if($db->db_num_rows($result)>0)
		{
			
			while($row = $db->db_fetch_object($result))
			{
				echo "<tr>\n";
				echo '<td>';
				echo $einrueckung;
				drawmenulink($row->child_content_id, $row->titel);
				drawsubmenu($row->child_content_id, $einrueckung."&nbsp;&nbsp;");
				echo "</td>\n";
				echo "</tr>\n";
			}
		}
	}
}

/**
 * Liefert den Link zum Anzeigen von Content Modulen
 * @param $key Action Key
 * @param $name Name des Links
 */
function get_content_link($key, $name)
{
	global $action, $content_id;	
	return '<a href="'.$_SERVER['PHP_SELF'].'?action='.$key.'&content_id='.$content_id.'" '.($action==$key?'class="marked"':'').'>'.$name.'</a>';
}

/**
 * Erstellt den Karteireiter zum Verwalten der Kindelemente eines Contents
 */
function print_childs()
{
	global $content_id, $sprache, $version;
	
	$content = new content();
	$content->getChilds($content_id);
	
	echo 'Folgende Einträge sind diesem Untergeordnet:<br><br>';
	echo '
	<script type="text/javascript">
		$(document).ready(function() 
		{ 
			$("#childs_table").tablesorter(
			{
				sortList: [[1,1]],
				widgets: ["zebra"]
			});
		});
	</script>';
	echo '<table id="childs_table" class="tablesorter" style="width: auto;">
		<thead>
		<tr>
			<th>ID</th>
			<th>Titel</th>
			<th></th>
		</tr>
		</thead>
		<tbody>';
	foreach($content->result as $row)
	{
		echo '<tr>';
		echo '<td>',$row->child_content_id,'</td>';
		echo '<td>',$row->titel,'</td>';
		echo '<td>
				<a href="'.$_SERVER['PHP_SELF'].'?action=childs&content_id='.$content_id.'&sprache='.$sprache.'&version='.$version.'&contentchild_id='.$row->contentchild_id.'&method=childs_delete" title="entfernen">
					<img src="../skin/images/delete_x.png">
				</a>
			</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
	
	$content = new content();
	$content->getpossibleChilds($content_id);
	echo '<form action="'.$_SERVER['PHP_SELF'].'?content_id='.$content_id.'&sprache='.$sprache.'&version='.$version.'&action=childs&method=childs_add" method="POST">';
	
	echo '<select name="child_content_id">';
	foreach($content->result as $row)
	{
		echo '<option value="'.$row->content_id.'">'.$row->titel.' ('.$row->content_id.')</option>';
	}
	echo '</select>';
	echo '<input type="submit" value="Hinzufügen" name="add">';
	echo '</form>';
}

/**
 * Erstellt den Karteireiter zum Eintragen der Eigenschaften eines Contents
 * 
 */
function print_prefs()
{
	global $content_id, $sprache, $version;
	
	$content = new content();
	if(!$content->getContent($content_id, $sprache, $version))
		die($content->errormsg);
		
	echo '<form action="'.$_SERVER['PHP_SELF'].'?content_id='.$content_id.'&sprache='.$sprache.'&version='.$version.'&action=prefs&method=prefs_save" method="POST">
	<table>
		<tr>
			<td>Titel</td>
			<td><input type="text" name="titel" size="40" maxlength="256" value="'.$content->titel.'"></td>
		</tr>
		<tr>
			<td>Vorlage</td>
			<td>
				<SELECT name="template_kurzbz">';
	$template = new template();
	$template->getAll();
	foreach($template->result as $row)
	{
		if($row->template_kurzbz==$content->template_kurzbz)
			$selected='selected';
		else
			$selected='';
		
		echo '<OPTION value="'.$row->template_kurzbz.'" '.$selected.'>'.$row->bezeichnung.'</OPTION>';
	}
	echo '	
				</SELECT>
			</td>
		</tr>
		<tr>
			<td>Organisationseinheit</td>
			<td>
				<SELECT name="oe_kurzbz">
	';
	$oe = new organisationseinheit();
	$oe->getAll();
	foreach($oe->result as $row)
	{
		if($row->oe_kurzbz==$content->oe_kurzbz)	
			$selected='selected';
		else
			$selected='';
		if($row->aktiv)
			$class='';
		else
			$class='class="inactive"';
		echo '<OPTION value="'.$row->oe_kurzbz.'" '.$selected.' '.$class.'>'.$row->organisationseinheittyp_kurzbz.' '.$row->bezeichnung.'</OPTION>';
	}
	echo '	
				</SELECT>
			</td>
		</tr>
		<tr>
			<td>Sichtbar</td>
			<td><input type="checkbox" name="sichtbar" '.($content->sichtbar?'checked':'').'></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Speichern"></td>
		</tr>
	</table>';	 
	
}

/**
 * Erstellt den Karteireiter zum Verwalten der Zugriffsrechte auf einen Content
 * Zu einem Content können Gruppen zugeteilt werden. Diese haben dann zugriff auf den Content
 * Wenn keine Gruppen zugeordnet sind, können alle Personen auf den Content zugreifen
 */
function print_rights()
{
	global $content_id, $sprache, $version;
	$content = new content();
	$content->loadGruppen($content_id);
	
	if(count($content->result)>0)
	{
		echo 'Die Mitglieder der folgenden Gruppen dürfen die Seite ansehen:<br><br>';
		echo '
		<script type="text/javascript">
			$(document).ready(function() 
			{ 
				$("#rights_table").tablesorter(
				{
					sortList: [[1,1]],
					widgets: ["zebra"]
				});
			});
		</script>';
		echo '<table id="rights_table" class="tablesorter" style="width: auto;">
			<thead>
			<tr>
				<th>Gruppe Kurzbz</th>
				<th>Bezeichnung</th>
				<th></th>
			</tr>
			</thead>
			<tbody>';
		foreach($content->result as $row)
		{
			echo '<tr>';
			echo '<td>',$row->gruppe_kurzbz,'</td>';
			echo '<td>',$row->bezeichnung,'</td>';
			echo '<td>
					<a href="'.$_SERVER['PHP_SELF'].'?action=rights&content_id='.$content_id.'&sprache='.$sprache.'&version='.$version.'&gruppe_kurzbz='.$row->gruppe_kurzbz.'&method=rights_delete_group" title="entfernen">
						<img src="../skin/images/delete_x.png">
					</a>
				</td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	}
	else
		echo 'Diese Seite darf von allen angezeigt werden!<br><br>';
		
	$gruppe = new gruppe();
	$gruppe->getgruppe(null, null, null, null, true);
	
	echo '<form action="'.$_SERVER['PHP_SELF'].'?content_id='.$content_id.'&sprache='.$sprache.'&version='.$version.'&action=rights&method=rights_add_group" method="POST">';
	echo 'Gruppe <select name="gruppe_kurzbz">';
	foreach($gruppe->result as $row)
	{
		echo '<option value="'.$row->gruppe_kurzbz.'">'.$row->gruppe_kurzbz.'</option>';
	}
	echo '</select>';
	echo '<input type="submit" value="Hinzufügen" name="addgroup">';
	echo '</form>';
}

/**
 * Erstellt den Karteireiter zum Eintragen des Contents
 * 
 * Hier wird Aufgrund der XSD Vorlage des Templates ein Formular erstellt und mit den
 * entsprechenden Werten des XML Files vorausgefuellt. 
 * 
 */
function print_content()
{
	global $content_id, $sprache, $version;
	
	$content = new content();

	if(!$content->getContent($content_id, $sprache, $version))
		die($content->errormsg);
		
	echo '<div>';
	$template = new template();
	$template->load($content->template_kurzbz);

	$xfp = new XSDFormPrinter();
	$xfp->getparams='?content_id='.$content_id.'&sprache='.$sprache.'&version='.$version.'&action=content';
	$xfp->output($template->xsd,$content->content);
	echo '</div>';
}

/**
 * Zeigt die Historie eines Contents an. 
 * 
 */
function print_history()
{
	global $content_id, $sprache, $version, $method;
	if($method=='history_changes')
	{
		if(!isset($_GET['v1']) || !isset($_GET['v2']))
		{
			echo 'Invalid Parameter';
			return false;
		}
		
		$v1 = $_GET['v1'];
		$v2 = $_GET['v2'];
		
		$content_old = new content();
		$content_old->getContent($content_id, $sprache, $v1);
		$dom = new DOMDocument();
		$dom->loadXML($content_old->content);
		$content_old = $dom->getElementsByTagName('inhalt')->item(0)->nodeValue;
		
		$content_new = new content();
		$content_new->getContent($content_id, $sprache, $v2);
		$dom = new DOMDocument();
		$dom->loadXML($content_new->content);
		$content_new = $dom->getElementsByTagName('inhalt')->item(0)->nodeValue;
		
		$arr_old = explode("\n",trim($content_old));
		$arr_new = explode("\n",trim($content_new));
		
		$diff = new Diff($arr_new, $arr_old);
		$tdf = new TableDiffFormatter();
		echo '<table>';
		echo html_entity_decode($tdf->format($diff));
		echo '</table>';
	}
	else
	{
		$content = new content();
		$content->loadVersionen($content_id, $sprache);
		
		$datum_obj = new datum();
		echo '<h3>Versionen</h3>';
		echo '<form action="'.$_SERVER['PHP_SELF'].'" method="GET">';
		echo '
			<input type="hidden" name="action" value="history">
			<input type="hidden" name="method" value="history_changes">
			<input type="hidden" name="sprache" value="'.$sprache.'">
			<input type="hidden" name="version" value="'.$version.'">
			<input type="hidden" name="content_id" value="'.$content_id.'">';
		echo 'Änderungen von Version
			<input type="text" value="1" size="2" name="v1"> zu 
			<input type="text" value="2" size="2" name="v2"> 
			<input type="submit" value="Anzeigen">
			</form>'; 
		echo '<ul>';
		foreach($content->result as $row)
		{
			echo '<li>';
			echo '<b>Version '.$row->version.'</b><br>Erstellt am '.$datum_obj->formatDatum($row->insertamum,'d.m.Y').' von '.$row->insertvon;
			if($row->updateamum!='' || $row->updatevon!='')
				echo '<br>Letzte Änderung von '.$row->updatevon.' am '.$datum_obj->formatDatum($row->updateamum,'d.m.Y');
			if($row->reviewvon!='' || $row->reviewamum!='')
				echo '<br>Review von '.$row->reviewvon.' am '.$datum_obj->formatDatum($row->reviewamum,'d.m.Y');
			echo '<br><br>';
			echo '</li>';
		}
		echo '</ul>';
	}
}
?>
