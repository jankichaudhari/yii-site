<?php





function ptype2($_ptype=NULL,$_psubtype=NULL,$_querystring=NULL) {

	// add onload statement
	$output['onload'] = 'init(document.forms[0].dea_psubtype); ';
	
	// if subtype is supplied, get js to select it onload	
	// not used. instead we build manual list, which is replaced by js list onChange (first list)
	if ($_psubtype) {	
		$output['onload'] .= '';
		}
	
	// get ptype types to populate javascript drop downs (select current ptype, use js to select subtype)
	$sql = "SELECT pty_id,pty_title,pty_type FROM ptype ORDER BY pty_title";
	$_result = mysql_query($sql);	
	if (!$_result)
	die("MySQL Error:  ".mysql_error()."<pre>ptype: ".$sql."</pre>");
	while($row = mysql_fetch_array($_result)) {	
		if (!$row["pty_type"]) { 
			$render_ptype .= '<option value="'.$row["pty_id"].'"';
			if ($_ptype == $row["pty_id"]) {
				$render_ptype .= ' selected';
				}
			$render_ptype .= '>'.$row["pty_title"].'</option>'."\n";
			}
		elseif ($row["pty_type"] == "1") {
			$_js1 .= "'".$row["pty_title"]."','".$row["pty_id"]."',";
			}
		elseif ($row["pty_type"] == "2") {
			$_js2 .= "'".$row["pty_title"]."','".$row["pty_id"]."',";
			}
		elseif ($row["pty_type"] == "3") {
			$_js3 .= "'".$row["pty_title"]."','".$row["pty_id"]."',";
			}
		}
	
	// create master type drop down
	$output['dd1'] = '<select name="dea_ptype" onchange="populate(document.forms[0].dea_ptype,document.forms[0].dea_psubtype)">'."\n";
	$output['dd1'] .= '<option value="0"></option>'."\n";
	$output['dd1'] .= $render_ptype;
	$output['dd1'] .= '</select>'."\n";
	
	// create subtype drop down (with populated list if a subtype is present)
	if ($_psubtype) {	
		$sql = "SELECT pty_id,pty_type,pty_title FROM ptype WHERE pty_type = ".$_ptype;
		$_result = mysql_query($sql);	
		if (!$_result)
		die("MySQL Error:  ".mysql_error()."<pre>ptype: ".$sql."</pre>");
		while($row = mysql_fetch_array($_result)) {	
			$render_psubtype .= '<option value="'.$row["pty_id"].'"';
			if ($row["pty_id"] == $_psubtype) {
				$render_psubtype .= ' selected';
				}
			$render_psubtype .= '>'.$row["pty_title"].'</option>'."\n";
			}
		}
	else {
		$render_psubtype .= '<option></option>'."\n";
		}
	
	$output['dd2'] = '<select name="dea_psubtype" style="width:140px">'."\n";
	$output['dd2'] .= $render_psubtype."\n";
	$output['dd2'] .= '</select>'."\n";
	
	// format javascript arrays
	$_js1 = "'(select)','',".remove_lastchar($_js1,",");
	$_js2 = "'(select)','',".remove_lastchar($_js2,",");
	$_js3 = "'(select)','',".remove_lastchar($_js3,",");
	
	$output['js'] = '
var thelist = new Array();
thelist[0] = new Array('.$_js0.');
thelist[1] = new Array('.$_js1.');
thelist[2] = new Array('.$_js2.');
thelist[3] = new Array('.$_js3.');
	
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
	box2.options.length = 0;
	for(i=0;i<list.length;i+=2) {
		box2.options[i/2] = new Option(list[i],list[i+1]);
		}
	dd2.focus();
	}		

';
		
	return $output;
	}
?>