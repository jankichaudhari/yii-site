<?php
session_start();
// here is where the function should begin

// $fieldnames - array of database field names, which is transforned into $_fields
// $newvalues - array of new values from post
// $_table - table name
// $_field - field name to do the WHERE
// $_row - id number to complete the WHERE 
// e.g. "SELECT ".$_fields." FROM ".$_table." WHERE ".$_field." = ".$_row."";
function queryLog($fieldnames,$newvalues,$_table,$_field,$_row) {
	for($i=0; $i<count($fieldnames); $i++) {
		// construct sql select field names from POST array
		$_fields .= $fieldnames[$i].",";
		}
	$_fields = removeCharacter(trim($_fields),",");
	// update query - first select existing values
	if ($_fields) {
		$_sql_select = "SELECT ".$_fields." FROM ".$_table." WHERE ".$_field." = ".$_row."";
		$_result = mysql_query($_sql_select);	
		if (!$_result)
			die("MySQL Error:  ".mysql_error()."<pre>SELECT: ".$_sql_select."</pre>");
			while($row_select = mysql_fetch_array($_result)) {
			for($i=0; $i<count($fieldnames); $i++) {
			$sql_field = $fieldnames[$i];
			$oldvalues[] = $row_select[$sql_field];
			}
		}
	// we should now have 3 arrarys, all with the same number of values
	// count each and compare before proceeding
	if (count($fieldnames) <> count($newvalues) || count($fieldnames) <> count($oldvalues)) {
		echo 'array count not matched';
		exit;
		}
	// contruct sql for $_table AND changelog table, single query with multiple inserts
	$sql_log = "INSERT INTO changelog 
	(cha_user,cha_session,cha_table,cha_row,cha_field,cha_old,cha_new)
	VALUES 
	";
	for($i=0; $i<count($fieldnames); $i++) {		
		// if the new value is different from the old
		if ($newvalues[$i] <> $oldvalues[$i]) { 
			// the UPDATE query to be run on $_table WHERE $_field = $_row
			$sql_update .= $fieldnames[$i]."='".$newvalues[$i]."',";
			// the INSERT query to be run on $_table WHERE $_field = $_row
			$sql_insert1 .= $fieldnames[$i].",";
			$sql_insert2 .= "'".$newvalues[$i]."',";
			// the log query insert
			$sql_log .= "('".$_SESSION["s_userid"]."','".session_id()."','".$_table."','".$_row."','".$fieldnames[$i]."','".$oldvalues[$i]."','".$newvalues[$i]."'),\n";
			// message for debug
			$_msg .= $fieldnames[$i]." was changed from ".$oldvalues[$i]." to ".$newvalues[$i]."\n";
			// count the number of changes
			$_changecount++;
			}
		}
	$sql_log = removeCharacter(trim($sql_log),",");	
	$sql_return = "UPDATE ".$_table." SET ".removeCharacter(trim($sql_update),",")." WHERE ".$_field." = ".$_row;
		
	if ($_changecount) { // only execute the sql queries if a change has been made
		$result_return = mysql_query($sql_return);	
		if (!$result_return)
		die("MySQL Error:  ".mysql_error()."<pre>RETURN: ".$sql_return."</pre>");
		$result_log = mysql_query($sql_log);	
		}
	return($sql_return);
	}
}



// undo change
// select all values form log table
// update table, row, field
// update changelog
function queryLogUndo($_cha_id,$_index) {
// TODO: loop through ids and construcy sql for multiple undo
$_sql = "SELECT * FROM changelog WHERE cha_id = ".$_cha_id."";
//echo $sql;
$_result = mysql_query($_sql);	
if (!$_result)
die("MySQL Error:  ".mysql_error()."<pre>".$_sql."</pre>");
while($row_undo = mysql_fetch_array($_result)) {
$_table = $row_undo["cha_table"];
$_row = $row_undo["cha_row"];
$_field = $row_undo["cha_field"];
$_old = $row_undo["cha_old"];
$_new = $row_undo["cha_new"];
}
// update the table with the old value
$_sql = "UPDATE ".$_table." SET ".$_field." = '".$_old."' WHERE ".$_index." = ".$_row;		
$_result = mysql_query($_sql);	
if (!$_result)
die("MySQL Error:  ".mysql_error()."<pre>".$_sql."</pre>");
// create a new entry in the log
$sql_log = "INSERT INTO changelog 
(cha_user,cha_session,cha_table,cha_row,cha_field,cha_old,cha_new)
VALUES 
('".$_SESSION["s_userid"]."','".session_id()."','".$_table."','".$_row."','".$_field."','".$_new."','".$_old."')";
$_result = mysql_query($sql_log);	
if (!$_result)
die("MySQL Error:  ".mysql_error()."<pre>".$sql_log."</pre>");
}

?>