<!-- Modified: Date            = 2013 Jul 22 -->
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Style-Type" CONTENT="text/css">
<LINK REL="stylesheet" HREF="style.css">

<?PHP
$OrderBy = htmlspecialchars($_GET['by']);
$OrderDir = htmlspecialchars($_GET['dir']);
$ToggleDir = htmlspecialchars($_GET['td']);
$Filter = htmlspecialchars($_GET['filter']);
$Check = htmlspecialchars($_GET['ck']);
$PageFunction = "Add";
$Submitted = date('Y\-m\-d');

echo "<!-- Variable: OrderBy         = $OrderBy -->\n";
echo "<!-- Variable: OrderDir        = $OrderDir -->\n";
echo "<!-- Variable: ToggleDir       = $ToggleDir -->\n";
echo "<!-- Variable: Filter          = $Filter -->\n";
echo "<!-- Variable: PatternID       = $PatternID -->\n";

include 'db-config.php';

if (isset($_POST['add-sdp'])) {
	$PatternID     = '';
	$Title 			= $_POST['form_title'];
	$Description 	= $_POST['form_description'];
	$Class 			= $_POST['form_class'];
	$Category 		= $_POST['form_category'];
	$Component 		= $_POST['form_component'];
	$Notes 			= $_POST['form_notes'];
	$PatternFile 	= $_POST['form_pattern_file'];
	$PatternType 	= $_POST['form_pattern_type'];
//	$Submitted     = date('Y\-m\-d'); # Must be set above
	$Modified 		= $Submitted;
	$Released 		= 'NULL';
	$Submitter 		= $_POST['form_submitter'];
	$Owner 			= $_POST['form_owner'];
	$TID 				= $_POST['form_tid'];
	$BUG 				= $_POST['form_bug'];
	$URL01 			= $_POST['form_url1'];
	$URL02 			= $_POST['form_url2'];
	$URL03 			= $_POST['form_url3'];
	$URL04 			= $_POST['form_url4'];
	$URL05 			= $_POST['form_url5'];
	$URL06 			= 'NULL';
	$URL07 			= 'NULL';
	$URL08 			= 'NULL';
	$URL09 			= 'NULL';
	$URL10 			= 'NULL';
	$Status 			= $_POST['form_status'];
	if ( strlen($TID) > 0 ) { $PrimaryLink = "'META_LINK_TID'"; }
	elseif ( strlen($BUG) > 0 ) { $PrimaryLink = "'META_LINK_BUG'"; }
	elseif ( strlen($URL01) > 0 ) { $URL = preg_split("/=/", "$URL01"); $PrimaryLink = "'META_LINK_$URL[0]'"; }
	elseif ( strlen($URL02) > 0 ) { $URL = preg_split("/=/", "$URL02"); $PrimaryLink = "'META_LINK_$URL[0]'"; }
	elseif ( strlen($URL03) > 0 ) { $URL = preg_split("/=/", "$URL03"); $PrimaryLink = "'META_LINK_$URL[0]'"; }
	elseif ( strlen($URL04) > 0 ) { $URL = preg_split("/=/", "$URL04"); $PrimaryLink = "'META_LINK_$URL[0]'"; }
	elseif ( strlen($URL05) > 0 ) { $URL = preg_split("/=/", "$URL05"); $PrimaryLink = "'META_LINK_$URL[0]'"; }
	else { $PrimaryLink = 'NULL'; }

	include 'db-open.php';
	$Query = "LOCK TABLES $TableName WRITE";
	mysql_query($Query) or die("<FONT SIZE=\"-1\"><B>ERROR</B>: Database: Table $TableName Lock -> <B>FAILED</B></FONT><BR>\n");

	echo "<!-- Query: Submitted          = $Query -->\n";
	echo "<!-- Database: Table           = Locked $TableName -->\n";

	if ( $Title && $Submitter && $Category && $Component ) {
		if ( $Status == "Complete" && $Owner == "" ) {
			echo "</HEAD>\n";
			echo "<BODY>\n";
			echo "<H2 ALIGN=\"center\">$PageFunction</H2>\n";
			echo "<H2 ALIGN=\"center\">Submit Pattern: <FONT COLOR=\"red\">FAILED</FONT></H2>\n";
			echo "<P ALIGN=\"center\"><B>ERROR:</B> No assigned owner.</P>\n";
			echo "<P ALIGN=\"center\">Click <B>back</B>, and correct.</P>\n";
		} else {
			if ( ( $Status == "Assigned" || $Status == "In-Progress" ) && $Owner == "" ) {
				$Owner = $Submitter;
				$Owner2submitter = 1;
				$LocalRefresh = $StatusRefresh * 3;
				echo "<!-- Override: Owner           = $Owner -->\n";
			} else {
				$Owner2submitter = 0;
				$LocalRefresh = $StatusRefresh;
			}
			if ( $Status == "Proposed" && $Owner != "" ) {
				$Status = 'Assigned'; 
				$Status2assigned = 1;
				$LocalRefresh = $StatusRefresh * 3;
				echo "<!-- Override: Status          = $Status -->\n";
			} else {
				$Status2assigned = 0;
				$LocalRefresh = $StatusRefresh;
			}
			$Query = "INSERT INTO $TableName VALUES ('','$Title','$Description','$Class','$Category','$Component','$Notes','$PatternFile','$PatternType','$Submitted','$Modified','$Released','$Submitter','$Owner',$PrimaryLink,'$TID','$BUG','$URL01','$URL02','$URL03','$URL04','$URL05','$URL06','$URL07','$URL08','$URL09','$URL10','$Status')";

			echo "<!-- Query: Submitted          = $Query -->\n";
			$result=mysql_query($Query);
			if ($result) {
				echo "<!-- Query: Result             = Success -->\n";
				if ( ! isset($DEBUG) ) { echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"$LocalRefresh;URL=patterns.php?by=$OrderBy&dir=$OrderDir&filter=$Filter&ck=$Check\">\n"; }
				echo "<BODY>\n";
				echo "<H2 ALIGN=\"center\">$PageFunction</H2>\n";
				if ( $Owner2submitter ) {
					echo "<H2 ALIGN=\"center\"><FONT COLOR=\"blue\">Override</FONT>: Submitter Assigned to Missing Owner -> <FONT COLOR=\"blue\">Done</FONT></H2>\n";
				}
				if ( $Status2assigned ) {
					echo "<H2 ALIGN=\"center\"><FONT COLOR=\"blue\">Override</FONT>: Pre-existing Owner, Status Changed to Assigned -> <FONT COLOR=\"blue\">Done</FONT></H2>\n";
				}
				echo "<H2 ALIGN=\"center\">Submit Pattern: <FONT COLOR=\"green\">Success</FONT></H2>\n";
			} else {
				echo "<!-- Query: Result             = FAILED -->\n";
				echo "<BODY>\n";
				echo "<H2 ALIGN=\"center\">$PageFunction</H2>\n";
				echo "<H2 ALIGN=\"center\">Submit Pattern: <FONT COLOR=\"red\">FAILED</FONT></H2>\n";
				echo "<P ALIGN=\"center\"><B>ERROR:</B> " . mysql_error() . "</P>\n";
				echo "<P ALIGN=\"center\">Click <B>back</B>, and correct.</P>\n";
			}
		}
	} else {
		echo "<!-- Variables Undefined       = Title and Submitter -->\n";
		echo "<BODY>\n";
		echo "<H2 ALIGN=\"center\">$PageFunction</H2>\n";
		echo "<H2 ALIGN=\"center\">Submit Pattern: <FONT COLOR=\"red\">FAILED</FONT></H2>\n";
		echo "<P ALIGN=\"center\"><B>ERROR:</B> Missing Required Field(s).</P>\n";
		echo "<P ALIGN=\"center\">Click <B>back</B>, and correct.</P>\n";
	}

	$Query = "UNLOCK TABLES";
	echo "<!-- Query: Submitted          = $Query -->\n";
	mysql_query($Query) or die("<FONT SIZE=\"-1\">Database: <B>ERROR</B>, Table $TableName Unlock -> <B>FAILED</B></FONT><BR>\n");
	echo "<!-- Database: Table           = UnLocked $TableName -->\n";

	include 'db-close.php';
} else {
?>
</HEAD>
<BODY>

<?PHP
	echo "<H2 ALIGN=\"center\">$PageFunction</H2>\n";
?>

<FORM METHOD="post">
<TABLE ALIGN="center" BORDER=0>
<TR VALIGN="top"><TD>

<?PHP
	echo "<TABLE BORDER=0>\n";
	echo "<TR><TD>Submitter:</TD><TD><INPUT TYPE=\"text\" NAME=\"form_submitter\" SIZE=$FieldLength><FONT COLOR=\"red\">*</FONT></TD></TR>\n";
	echo "<TR><TD>Owner:</TD><TD><INPUT TYPE=\"text\" NAME=\"form_owner\" SIZE=$FieldLength></TD></TR>\n";
	echo "<TR><TD>Title (Max Length 60):</TD><TD><INPUT TYPE=\"text\" NAME=\"form_title\" SIZE=$FieldLength MAXLENGTH=60><FONT COLOR=\"red\">*</FONT></TD></TR>\n";
	echo "<TR><TD>Description:</TD><TD><TEXTAREA NAME=\"form_description\" COLS=$DescLength ROWS=4></TEXTAREA></TD></TR>\n";
	echo "<TR><TD>Class:</TD><TD>";
		echo "<SELECT NAME=\"form_class\" CLASS=\"text\">";
		include 'form-class.php';
	echo "</TD>";
	echo "<TR><TD>Category:</TD><TD><INPUT TYPE=\"text\" NAME=\"form_category\" SIZE=$FieldLength><FONT COLOR=\"red\">*</FONT></TD></TR>\n";
	echo "<TR><TD>Component:</TD><TD><INPUT TYPE=\"text\" NAME=\"form_component\" SIZE=$FieldLength><FONT COLOR=\"red\">*</FONT></TD></TR>\n";
	echo "<TR><TD>Status:</TD><TD>";
		echo "<SELECT NAME=\"form_status\" CLASS=\"text\">";
		include 'form-status-add.php';
	echo "</TD>";
	echo "</TABLE>\n";
?>

</TD><TD>

<?PHP
	echo "<TABLE BORDER=0>\n";
	echo "<TR><TD>Pattern ID:</TD><TD>Pending</TD></TR>\n";
	echo "<TR><TD>Pattern File:</TD><TD><INPUT TYPE=\"text\" NAME=\"form_pattern_file\" SIZE=$FieldLength></TD></TR>\n";
	echo "<TR><TD>Pattern Type:</TD><TD>";
		echo "<SELECT NAME=\"form_pattern_type\" CLASS=\"text\">";
		include 'form-pattern-type.php';
	echo "</TD>";
	echo "<TR><TD>TID URL:</TD><TD><INPUT TYPE=\"text\" NAME=\"form_tid\" SIZE=$FieldLength></TD></TR>\n";
	echo "<TR><TD>BUG URL:</TD><TD><INPUT TYPE=\"text\" NAME=\"form_bug\" SIZE=$FieldLength></TD></TR>\n";
	echo "<TR><TD>URL Pair 1:</TD><TD><INPUT TYPE=\"text\" NAME=\"form_url1\" SIZE=$FieldLength></TD></TR>\n";
	echo "<TR><TD>URL Pair 2:</TD><TD><INPUT TYPE=\"text\" NAME=\"form_url2\" SIZE=$FieldLength></TD></TR>\n";
	echo "<TR><TD>URL Pair 3:</TD><TD><INPUT TYPE=\"text\" NAME=\"form_url3\" SIZE=$FieldLength></TD></TR>\n";
	echo "<TR><TD>URL Pair 4:</TD><TD><INPUT TYPE=\"text\" NAME=\"form_url4\" SIZE=$FieldLength></TD></TR>\n";
	echo "<TR><TD>URL Pair 5:</TD><TD><INPUT TYPE=\"text\" NAME=\"form_url5\" SIZE=$FieldLength></TD></TR>\n";
	echo "<TR><TD>Submission:</TD><TD>$Submitted</TD></TR>\n";
	echo "</TABLE>\n\n";
	echo "</TR><TR>\n";
	echo "<TD ALIGN=\"center\" COLSPAN=2>Notes:&nbsp;&nbsp;<TEXTAREA NAME=\"form_notes\" COLS=$NotesLength ROWS=3></TEXTAREA></TD>";

	echo "</TR>\n";
	echo "<TR><TD COLSPAN=2>&nbsp;</TD></TR>\n";
	echo "<TR ALIGN=\"center\"><TD COLSPAN=2>";
	echo "<INPUT TYPE=\"BUTTON\" VALUE=\"Help\" ONCLICK=\"window.open('help-pattern-add.html','_pat-add-help')\">&nbsp;&nbsp;";
	echo "<INPUT TYPE=\"BUTTON\" VALUE=\"Cancel\" ONCLICK=\"window.location.href='patterns.php?by=$OrderBy&dir=$OrderDir&filter=$Filter'\">&nbsp;&nbsp;";
	echo "<INPUT TYPE=\"submit\" NAME=\"add-sdp\" ID=\"add-sdp\" VALUE=\"Submit Pattern\">&nbsp;&nbsp;";
	echo "</TD></TR>\n";
?>

</TABLE>
</FORM>
<?PHP
}

echo "<!-- Variable: OrderBy         = $OrderBy -->\n";
echo "<!-- Variable: OrderDir        = $OrderDir -->\n";
echo "<!-- Variable: ToggleDir       = $ToggleDir -->\n";
echo "<!-- Variable: Filter          = $Filter -->\n";
echo "<!-- Variable: PatternID       = $PatternID -->\n";
?> 

</BODY>
</HTML>

