<?php
	/**
	 * FAS-Synchronisation
	 */
	include ('../../vilesci/config.inc.php');
	$adress='fas_sync@technikum-wien.at, baier@technikum-wien.at';
	mail($adress,"FAS Synchro mit VILESCI","BEGIN OF SYNCHRONISATION","From: vilesci@technikum-wien.at");
	$conn=pg_connect(CONN_STRING);
	$conn_fas=pg_connect(CONN_STRING_FAS);

 	// Anzahl der Studenten in VILESCI
	$sql_query="SELECT count(*) AS anz FROM tbl_student";
	//echo $sql_query."<br>";
	$result=pg_exec($conn, $sql_query);
	$vilesci_anz_std=pg_fetch_result($result,0,'anz');

	// Start Studenten Synchro
	$sql_query="SELECT DISTINCT uid FROM fas_view_vilesci_abbrecher WHERE uid IS NOT NULL AND uid NOT LIKE '' ORDER BY uid"; // LIMIT 5";
	//echo $sql_query."<br>";
	flush();
	$result=pg_exec($conn_fas, $sql_query);
	$num_rows=pg_numrows($result);
	$text="Dies ist eine automatische eMail!\r\r";
	$text.="Es wurde eine Synchronisation mit FAS durchgeführt.\r";
	$text.="Anzahl der Ex-Studenten vom FAS-Import: $num_rows \r";
	$text.="Anzahl der Studenten in VILESCI: $vilesci_anz_std \r\r";
	echo $text.'<BR>';
	flush();
	$update_error=0;
	$anz_update=0;
	for ($i=0;$i<$num_rows;$i++)
	{
		$row=pg_fetch_object($result,$i);
		$uid=str_replace(' ','',$row->uid);
		// SQL vorbereiten (jeden Studenten vom FAS im vilesci suchen
		$sql_query="SELECT uid,titel,vornamen,nachname, semester FROM tbl_person NATURAL JOIN tbl_student WHERE uid LIKE '$uid'";
		//echo $sql_query;
		$res_std=pg_query($conn, $sql_query);
		$num_rows_std=pg_numrows($res_std);
		// neue Studenten
		if ($num_rows_std>=1)
		{
			$row_std=pg_fetch_object($res_std);
			if ($row_std->semester!=10)
			{
				$text.="Der Student $row_std->vornamen $row_std->nachname ($row_std->uid) wird verschoben.\r";
				$sql_query="UPDATE tbl_student SET semester=10, updateamum=now(), updatevon='auto' WHERE uid LIKE '$uid'";
				echo $sql_query.'<BR>';
				if(!$res_update=pg_query($conn, $sql_query))
				{
					$text.=$sql_query;
					$text.="\rFehler: ".pg_errormessage($conn)."\r";
					$update_error++;
				}
				else
					$anz_update++;
			}
		}
	}
	$text.="$update_error Fehler bei Student-Update!\r";
	$text.="$anz_update Studenten wurden ins 10te Semester verschoben.\r";
	$text.="\rEND OF SYNCHRONISATION\r";
	if (mail($adress,"FAS Synchro mit VILESCI (Ex-Studenten)",$text,"From: vilesci@technikum-wien.at"))
		$sendmail=true;
	else
		$sendmail=false;
?>

<html>
<head>
	<title>FAS-Synchro mit VILESCI (Ex-Studenten)</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<LINK rel="stylesheet" href="../../include/styles.css" type="text/css">
</head>
<body>
<?php
if ($sendmail)
	echo 'Mail wurde verschickt an '.$adress.'!<br>';
else
	echo "Mail konnte nicht verschickt werden!<br>";
echo $text;

?>
</body>
</html>
