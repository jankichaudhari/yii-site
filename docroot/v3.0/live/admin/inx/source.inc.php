<?php


function source($_source=NULL,$_querystring,$_readonly=NULL) {
	
	$otherText = '>> create new';	
	
	// if a source is supplied, don't show the muti drop downs.
	if ($_source && $_source <> 'x') {
		$ref = '?'.replaceQueryString($_querystring,'cli_source');
		$sql = "SELECT 
		source.sou_id,source.sou_title,source.sou_type,source2.sou_title AS sou_title2 
		FROM source 
		LEFT JOIN  source AS source2 ON source2.sou_id = source.sou_type
		WHERE source.sou_id = $_source LIMIT 1";
		$_result = mysql_query($sql);	
		if (!$_result)
		die("MySQL Error:  ".mysql_error()."<pre>source: ".$sql."</pre>");
		while($row = mysql_fetch_array($_result)) {	
			$output['dd1'] = '<input type="text" readonly="readonly" value="'.$row["sou_title"].' ('.$row["sou_title2"].')" class="inputInvisible">'."\n"; 			
			if (!$_readonly) {
				$output['dd1'] .= '<input type="button" value="Change" class="button" onClick="javascript:document.location.href=\''.$ref.'\';">'."\n"; 
				}
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
			
			if ($row["sou_type"] == 0) { 
				// types
				$render_source .= '<option value="'.$row["sou_id"].'"';
				if ($cli_source1 == $row["sou_id"]) {
					$render_source .= ' selected';
					}
				$render_source .= '>'.$row["sou_title"].'</option>'."\n";
				// array of types to loop through later
				$types[] = $row["sou_id"];
				}
			else {
				// sub-types
				${'_js' . $row["sou_type"]} .= "'".$row["sou_title"]."','".$row["sou_id"]."',";	
				}
			}
		
		// create master type drop down
		$output['dd1'] = '<select name="cli_source1" id="source1" onchange="populate(document.forms[0].cli_source1,document.forms[0].cli_source)">'."\n";
		$output['dd1'] .= '<option value="0"></option>'."\n";
		$output['dd1'] .= $render_source;
		$output['dd1'] .= '</select>'."\n";
		$output['dd2'] = '<select name="cli_source" id="source" style="width:200px" onChange="sourceOther(this);">'."\n";
		$output['dd2'] .= '<option></option>'."\n";
		$output['dd2'] .= '</select>'."\n";
		$output['dd2'] .= '<input type="text" name="sourceNew" id="sourceNew" style="display:none" />'."\n";
		
		// format javascript arrays (add "other" option to each?)
		foreach ($types AS $type) {
			if (${'_js' . $type}) {
				${'_js' . $type} .= "'$otherText','x',";
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
	var box = dd1;
	var number = box.options[box.selectedIndex].value;
	if (!number) return;
	var list = thelist[number];
	var box2 = dd2;
	
	// make sure dd2 is visible
	if (box2.style.display == "none") {
		box2.style.display = "";
		var sourceNew = document.getElementById(\'sourceNew\');
		sourceNew.style.display = "none";
		}
	box2.options.length = 0;
	for(i=0;i<list.length;i+=2) {
		box2.options[i/2] = new Option(list[i],list[i+1]);
		}
	dd2.focus();
	}	

// replaced dd2 with text field and hidden field for type	
function sourceOther(selectedItem) {

	if (selectedItem.value == \'x\') {
		var source = document.getElementById(\'source\');
		source.style.display = "none";
		var sourceNew = document.getElementById(\'sourceNew\');
		sourceNew.style.display = "";
		sourceNew.focus();
		}
	}	
';
		}
	return $output;
	}
?>