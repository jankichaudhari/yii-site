<?php

# build table of property types

// $_array - array of existing values
// $_columns - number of columns to show
// $_style - style of output

// future: $_style - style of the output for use in different forms

function area($_array=NULL,$_scope="cli_",$_columns=4,$_style=NULL) {
	// if array is empty, make an empty array to supress in_array errors
	if (!is_array($_array)) {
		$_array = array();
		}
	
	
	// takes too long, not sure why
	if ($_style == 'Sales' || $_style == 'Lettings') {
	
		$_sql_select = "SELECT are_id,are_title,are_postcode,are_branch,COUNT(dea_id) as deal_count FROM area 
		LEFT JOIN property ON pro_area = area.are_id
		LEFT JOIN deal ON deal.dea_prop = property.pro_id AND (dea_status = 'Available' OR dea_status = 'Under Offer') AND dea_type = '$_style'
		GROUP BY are_id
		ORDER BY are_title";
		$_result = mysql_query($_sql_select);	
		if (!$_result)
		die("MySQL Error:  ".mysql_error()."<pre>area: ".$_sql_select."</pre>");
		while($row = mysql_fetch_array($_result)) {	
			if (in_array($row["are_id"],$_array)) { $checked = ' checked'; } else { $checked = ''; }
			$render[$row["are_branch"]] .= '<td width="150" class="small"><label for="'.$row["are_id"].'"><input type="checkbox" name="'.$_scope.'area[]" value="'.$row["are_id"].'" id="'.$row["are_id"].'" class="branch'.$row["are_branch"].'"'.$checked.'>'.$row["are_title"].' ('.$row["deal_count"].')</label></td>'."\n";
			$i[$row["are_branch"]]++;
			if ($i[$row["are_branch"]] % $_columns == 0) {
				$render[$row["are_branch"]] .= "</tr>\n<tr>\n";
				}
			}
		}
	else {
		$_sql_select = "SELECT are_id,are_title,are_postcode,are_branch FROM area 
		ORDER BY are_title";
		$_result = mysql_query($_sql_select);	
		if (!$_result)
		die("MySQL Error:  ".mysql_error()."<pre>area: ".$_sql_select."</pre>");
		while($row = mysql_fetch_array($_result)) {				
			if (in_array($row["are_id"],$_array)) { $checked = ' checked'; } else { $checked = ''; }
			$render[$row["are_branch"]] .= '<td width="150" class="small"><label for="'.$row["are_id"].'"><input type="checkbox" name="'.$_scope.'area[]" value="'.$row["are_id"].'" id="'.$row["are_id"].'" class="branch'.$row["are_branch"].'"'.$checked.'>'.$row["are_title"].' / '.$row["are_postcode"].'</label></td>'."\n";
			$i[$row["are_branch"]]++;
			if ($i[$row["are_branch"]] % $_columns == 0) {
				$render[$row["are_branch"]] .= "</tr>\n<tr>\n";
				}
			}
		}
			
			

		
	/*	
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
	*/
	return $render;
	}
	

?>