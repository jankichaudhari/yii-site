<?php


function source($_source=NULL,$_querystring) {
	// if a source is supplied, don't show the muti drop downs.
	if ($_source) {
		$ref = '?'.replaceQueryString($_querystring,'cli_source');
		$sql = "SELECT sou_id,sou_title,sou_type FROM source WHERE sou_id = $_source LIMIT 1";
		$_result = mysql_query($sql);	
		if (!$_result)
		die("MySQL Error:  ".mysql_error()."<pre>source: ".$sql."</pre>");
		while($row = mysql_fetch_array($_result)) {	
			$output['dd1'] = '<input type="text" readonly="readonly" value="'.$row["sou_title"].' ('.$row["sou_type"].')" class="inputInvisible">'."\n"; 			
			$output['dd1'] .= '<input type="button" value="Change" class="button" onClick="javascript:document.location.href=\''.$ref.'\';">'."\n"; 
			$output['dd1'] .= '<input type="hidden" name="cli_source" value="'.$row["sou_id"].'">';
			}
		}
	else {
		// only add onload statement if source is not specified
		$output['onload'] = 'init(document.forms[0].cli_source)';
		
		// get source types to populate javascript drop downs
		$sql = "SELECT sou_id,sou_title,sou_type FROM source ORDER BY sou_title";
		$_result = mysql_query($sql);	
		if (!$_result)
		die("MySQL Error:  ".mysql_error()."<pre>source: ".$sql."</pre>");
		while($row = mysql_fetch_array($_result)) {	
			if (!$row["sou_type"]) { 
				$render_source .= '<option value="'.$row["sou_id"].'"';
				if ($cli_source1 == $row["sou_id"]) {
					$render_source .= ' selected';
					}
				$render_source .= '>'.$row["sou_title"].'</option>'."\n";
				// array of types to loop through later
				$types[] = $row["sou_id"];
				}
			else {
				${'_js' . $row["sou_type"]} .= "'".$row["sou_title"]."','".$row["sou_id"]."',";	
				}
				/*~
			elseif ($row["sou_type"] == "Portal") {
				$_js1 .= "'".$row["sou_title"]."','".$row["sou_id"]."',";
				}
			elseif ($row["sou_type"] == "Press") {
				$_js2 .= "'".$row["sou_title"]."','".$row["sou_id"]."',";
				}
			elseif ($row["sou_type"] == "Search Engine") {
				$_js3 .= "'".$row["sou_title"]."','".$row["sou_id"]."',";
				}
			elseif ($row["sou_type"] == "Referral") {
				$_js4 .= "'".$row["sou_title"]."','".$row["sou_id"]."',";
				}*/
			}
		
		// create master type drop down
		$output['dd1'] = '<select name="cli_source1" style="width:200px" onchange="populate(document.forms[0].cli_source1,document.forms[0].cli_source)">'."\n";
		$output['dd1'] .= '<option value="0">(click here)</option>'."\n";
		$output['dd1'] .= $render_source;
		$output['dd1'] .= '</select>'."\n";
		$output['dd2'] = '<select name="cli_source" style="width:200px;display:none;">'."\n";
		$output['dd2'] .= '<option></option>'."\n";
		$output['dd2'] .= '</select>'."\n";
		
				// format javascript arrays (add "other" option to each?)
		foreach ($types AS $type) {
			if (${'_js' . $type}) {
				${'_js' . $type} = "'(select)','',".remove_lastchar(${'_js' . $type},",");
				$render_js .= 'thelist['.$type.'] = new Array('.${'_js' . $type}.');'."\n";
				}
			}
		
		$output['js'] = '
var thelist = new Array();
thelist[0] = new Array();
'.$render_js.'
	
function init(dd1) 	{
	optionTest = true;
	lgth = dd1.options.length;
	dd1.options[lgth] = null;
	if (dd1.options[lgth]) optionTest = false;
	}
	
// dd1 is the first drop down, dd2 is the second
function populate(dd1,dd2) 	{
	if (!optionTest) return;
	dd2.style.display = "";
	/* dd1.style.display = "none"; */
	var box = dd1;
	var number = box.options[box.selectedIndex].value;
	if (!number) return;
	var list = thelist[number];
	var box2 = dd2;
	box2.options.length = 0;
	for(i=0;i<list.length;i+=2) {
		box2.options[i/2] = new Option(list[i],list[i+1]);
		}
	dd2.focus();
	}		
';
		}
	return $output;
	}
?>