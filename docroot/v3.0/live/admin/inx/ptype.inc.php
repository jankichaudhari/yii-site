<?php

# build table of property types

// $_scope - sale / letting
// $_array - array of existing values
// $_columns - number of columns to show

// future: $_style - style of the output for use in different forms

function ptype($_scope,$_array=array(),$_columns=5) {

	$_sql_select = "SELECT pty_id,pty_type,pty_title FROM ptype";
	$_result = mysql_query($_sql_select);	
	if (!$_result)
	die("MySQL Error:  ".mysql_error()."<pre>ptype: ".$_sql_select."</pre>");
	while($row = mysql_fetch_array($_result)) {	
		if (!$row["pty_type"]) { 
			$master[] = $row["pty_title"].'s';
			}
		elseif ($row["pty_type"] == 1) { // house
			if (in_array($row["pty_id"],$_array)) { $checked = ' checked'; } else { $checked = ''; }
			$house[] = '<label for="'.$_scope.'pty_'.$row["pty_id"].'"><input type="checkbox" name="cli_'.$_scope.'ptype[]" value="'.$row["pty_id"].'" id="'.$_scope.'pty_'.$row["pty_id"].'" class="'.$_scope.''.$row["pty_type"].'" '.$checked.'>'.$row["pty_title"].'</label> ';
			}
		elseif ($row["pty_type"] == 2) { // apartment
			if (in_array($row["pty_id"],$_array)) { $checked = ' checked'; } else { $checked = ''; }
			$apartment[] = '<label for="'.$_scope.'pty_'.$row["pty_id"].'"><input type="checkbox" name="cli_'.$_scope.'ptype[]" value="'.$row["pty_id"].'" id="'.$_scope.'pty_'.$row["pty_id"].'" class="'.$_scope.''.$row["pty_type"].'"'.$checked.'>'.$row["pty_title"].'</label> ';
			}
		elseif ($row["pty_type"] == 3) { // other
			if (in_array($row["pty_id"],$_array)) { $checked = ' checked'; } else { $checked = ''; }
			$other[] = '<label for="'.$_scope.'pty_'.$row["pty_id"].'"><input type="checkbox" name="cli_'.$_scope.'ptype[]" value="'.$row["pty_id"].'" id="'.$_scope.'pty_'.$row["pty_id"].'" class="'.$_scope.''.$row["pty_type"].'"'.$checked.'>'.$row["pty_title"].'</label> ';
			}
		}
	
	$i = 0;
	foreach ($house as $subtype) {
		$render['house'] .= '<td class="small" width="100">'.$subtype.'</td>'."\n";
		$i++;
		if ($i % $_columns == 0) {
			$render['house'] .= '</tr>
			<tr>';
			}
		}
	$render['house'] = '<div id="innerTable">
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
	'.$render['house'].'
	</tr>
	</table>
	</div>';
		
	
	$i = 0;
	foreach ($apartment as $subtype) {
		$render['apartment'] .= '<td class="small" width="100">'.$subtype.'</td>'."\n";
		$i++;
		if ($i % $_columns == 0) {
			$render['apartment'] .= '</tr>
			<tr>';
			}
		}
	$render['apartment'] = '<div id="innerTable">
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
	'.$render['apartment'].'
	</tr>
	</table>
	</div>';
	
	$i = 0;
	foreach ($other as $subtype) {
		$render['other'] .= '<td class="small" width="100">'.$subtype.'</td>'."\n";
		$i++;
		if ($i % $_columns == 0) {
			$render['other'] .= '</tr>
			<tr>';
			}
		}
	$render['other'] = '<div id="innerTable">
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
	'.$render['other'].'
	</tr>
	</table>
	</div>';
	
	return $render;
	}
	

?>