<?php

// DATABASE CONNECTION
require_once("DB.php");
$dsn = array(
    'phptype'  => "mysql",
    'database' => "wsv3_live",
    'username' => "wsv3_db_user",
    'password' => "CHe9adru+*=!a!uC7ubRad!TRu#raN"
);
$db = DB::connect($dsn);
if (DB::isError($db)) {  die("Fatal error: ".$db->getMessage()); }
$db->setFetchMode(DB_FETCHMODE_ASSOC);







function db_query($_data,$_action,$_table,$_field,$_row="temp_row_id") {	

	global $current_user_id;
	// update - select existing values for comparison
	if ($_action == "UPDATE") {
		foreach($_data as $_key=>$_val) {
			$_fields .= $_key.",";
			$_newvalues[] = format_data("$_val");
			}
		$_fields = remove_lastchar(trim($_fields),",");	
		if ($_fields) {			
			$_sql_select = "SELECT ".$_fields." FROM ".$_table." WHERE ".$_field." = ".$_row." LIMIT 1";						
			$_result = mysql_query($_sql_select);	
			if (!$_result)
			die("MySQL Error:  ".mysql_error()."<pre>db_query: ".$_sql_select."</pre>");
			while($_row_select = mysql_fetch_array($_result)) {	
				foreach($_data as $_key=>$_val) {
					// format old values to make sure sql works
					$_oldvalues[$_key] = format_data($_row_select[$_key]);
					}
				}
			mysql_free_result($_result);
			}
		}
	// insert - nothing to compare so no select. create array of old values with NULL
	elseif ($_action == "INSERT") {
		foreach($_data as $_key=>$_val) {	
			$_newvalues[] = format_data($_val);
			$_oldvalues[$_key] = "NULL";
			}
		}
	/* // delete
	elseif ($_action == "DELETE") {
		foreach($_data as $_key=>$_val) {			
			$_newvalues[] = "NULL";
			$_oldvalues[$_key] = trim("$_val");
			}
		}*/
	
	
	// we should now have 2 arrarys of fieldnames as keys and new and old values
	if (count($_data) !== count($_oldvalues)) {
		echo 'db_query: array count not matched<p>';
		exit;
		}

	// construct sql for $_table AND changelog table, single query with multiple inserts
	$_sql_log = "INSERT INTO changelog 
	(cha_user,cha_session,cha_action,cha_table,cha_row,cha_field,cha_old,cha_new)
	VALUES 
	";	
	
	// loop _data, check if current(old) value is different to new, and create sql statements accordingly
	foreach($_data as $_key=>$_val) {
		if ($_oldvalues[$_key] !== $_val) { 
			// enter NULL value without quotes (09/05/06)
			if ($_val == "NULL"){				
				$_sql_update .= $_key."=NULL,";
				} elseif ($_val == ""){		
				$_sql_update .= $_key."='',";
				} else {
				$_sql_update .= $_key."='".format_data($_val)."',";
				}
			$_sql_insert1 .= $_key.",";
			$_sql_insert2 .= "'".format_data($_val)."',";				
			$_sql_log .= "('".$current_user_id."','".session_id()."','".$_action."','".$_table."','".$_row."','".format_data($_key)."','".format_data($_oldvalues[$_key])."','".format_data($_val)."'),\n";
			$_msg .= $_key." was changed from ".$_oldvalues[$_key]." to ".$_val."\n";
			$_changecount++;
			}		
		}
		
	$_sql_log = remove_lastchar(trim($_sql_log),",");
	$_sql_update = remove_lastchar(trim($_sql_update),","); 
	$_sql_insert1 = remove_lastchar(trim($_sql_insert1),","); 
	$_sql_insert2 = remove_lastchar(trim($_sql_insert2),","); 
	
	if ($_action == "UPDATE") {
		$_sql_return = "UPDATE ".$_table." SET ".$_sql_update." WHERE ".$_field." = ".$_row;
		} 	
	elseif ($_action == "INSERT") {
		$_sql_return = "INSERT INTO ".$_table." (".$_sql_insert1.") VALUES (".$_sql_insert2.")";
		}
	/* elseif ($_action == "DELETE") {
		$_sql_return = "DELETE FROM ".$_table." WHERE ".$_field." = ".$_row;
		}*/
	
	if ($_changecount) { // only execute the sql queries if a change has been made
		$_result_return = mysql_query($_sql_return);			
		if (!$_result_return)
		die("MySQL Error:  ".mysql_error()."<pre>db_query RETURN: ".$_sql_return."</pre>");		
		if ($_action == "UPDATE") {
			$_insert_id = $_row;
			} elseif  ($_action == "INSERT") {
			$_insert_id = mysql_insert_id();
			$_sql_log = str_replace("temp_row_id",$_insert_id,$_sql_log); // replace temp_row_id with insert_id
			}
		
		//mysql_free_result($_result_return);
		
		$_result_log = mysql_query($_sql_log);	
		if (!$_result_log)
		die("MySQL Error:  ".mysql_error()."<pre>db_query LOG: ".$_sql_log."</pre>");				
		//mysql_free_result($_result_log);
		}
	
	/* // dubug info
	echo "<pre><b>Current values</b>\n";
	print_r($_oldvalues);
	echo "<pre><b>New values</b>\n";
	print_r($_data);
	echo "<hr>sql_log: ".$_sql_log;
	echo "<hr>msg: ".$_msg;	*/
	
	return($_insert_id); // return the effected row in $_table, not changelog
	unset($_data,$_action,$_table,$_field,$_row);
	}







// db_enum
// return formatted list from enum field
// $_table is the name of the table 
// $_field is the name of the enum field 
// $_type can be select, radio or checkbox - or array
// $_pick if you want an option to be selected by default 
// $_isnull if you want a blank entry at the top of the list ($_type=select only)
function db_enum($_table,$_field,$_type="select",$_pick=null,$_isnull=null,$_style=NULL) {
	$_render = "";
	$_result = mysql_query("describe $_table $_field");
    $_row = mysql_fetch_array($_result); 
    $_value = $_row["Type"];
	mysql_free_result($_result);
    preg_match_all("/'([^']+)'/", $_value, $_matches, PREG_SET_ORDER); 
	$_count = count($_matches);
		
	if ($_type == "array") {	
		foreach($_matches as $_v) {	
			$_render[$_v[1]] = $_v[1];			
			}
		} 
	elseif ($_type == "checkbox") {
		foreach($_matches as $_v) {	
			$_render .= '<input type="checkbox" name="'.$_field.'[]" value="'.$_v[1].'" id="'.$_field.'_'.$_v[1].'"';
			if ($_v[1] == $_pick) { $_render .= ' checked'; }		
			$_render .= ' /><label for="'.$_field.'_'.$_v[1].'" class="checkbox">'.$_v[1].'</label>
			'; 
			
			}
		} 
	elseif ($_type == "radio") {
		foreach($_matches as $_v) {	
			$_render .= '<input type="radio" name="'.$_field.'" value="'.$_v[1].'" id="'.$_field.'_'.$_v[1].'"';
			if ($_v[1] == $_pick) { $_render .= ' checked'; }
			$_render .= ' /><label for="'.$_field.'_'.$_v[1].'" class="radio">'.$_v[1].' </label>
			';			
			}
		} 
	elseif ($_type == "select") {
		// insert a blank option (or allow blank option is no option is previously select)
		if ($_isnull && !isset($_pick)) {
			$_render .= '<option value=""></option>
			';
			}
		foreach($_matches as $_v) {	
			$_render .= '<option value="'.$_v[1].'"';
			if ($_v[1] == $_pick) { $_render .= ' selected'; }
			$_render .= '>'.$_v[1].'</option>
			'; 
			}
		$_render = '<select name="'.$_field.'" '.$_style.'>'.$_render.'</select>
		';
		}
	
	return $_render;
	unset($_table,$_where,$_query,$_result,$_row,$_v,$_value,$_matches,$_render,$_count,$_isnull);
	} 
?>