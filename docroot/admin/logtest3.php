<?php

// here is where the function should begin
// function can do inserts, updates and deletes, tracking all in changelog
// updates will hold new and old values for each changed field
// inserts and deletes will not have new and old values, 
// instead we will just populate both with a value (created, deleted, etc)

// input the following vars to get results
// $fieldnames - array of database field names, which is transforned into $_fields
// $newvalues - array of new values from post
// $_table - table name
// $_field - field name to do the WHERE
// $_row - id number to complete the WHERE 
// $_action - INSERT, UPDATE or DELETE
// e.g. "SELECT ".$_fields." FROM ".$_table." WHERE ".$_field." = ".$_row."";
function queryLog($fieldnames,$newvalues,$_table,$_field,$_row,$_action) {
	for($i=0; $i<count($fieldnames); $i++) {
		// construct sql select field names from POST array
		$_fields .= $fieldnames[$i].",";
		}
	$_fields = removeCharacter(trim($_fields),",");
	if ($_fields) {
	
	$sql = "SELECT ".$_fields." FROM ".$_table." WHERE ".$_field." = ".$_row."";
	echo $sql;
	$result = mysql_query($sql);	
	if (!$result)
		die("MySQL Error:  ".mysql_error()."<pre>".$sql."</pre>");
		
	while($row = mysql_fetch_array($result)) {
		for($i=0; $i<count($fieldnames); $i++) {
			$sql_field = $fieldnames[$i];
			$oldvalues[] = $row[$sql_field];
			}
		}
	//print_r($fieldnames);
	//print_r($newvalues);
	//print_r($oldvalues);
	
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
			$sql_log .= "('".$_SESSION["s_userid"]."','".$PHPSESSID."','".$_table."','".$_row."','".$fieldnames[$i]."','".$oldvalues[$i]."','".$newvalues[$i]."'),\n";
			// message for debug
			$_msg .= $fieldnames[$i]." was changed from ".$oldvalues[$i]." to ".$newvalues[$i]."\n";
			}
		}
	if ($_action == "Update") {
		$sql_return = "UPDATE ".$_table." SET ".removeCharacter(trim($sql_update),",")." WHERE ".$_field." = ".$_row;
		} elseif ($_action == "Insert") {
		$sql_return = "INSERT INTO ".$_table." (".removeCharacter(trim($sql_insert1),",").") VALUES (".removeCharacter(trim($sql_insert2),",").")";
		}
	$sql_log = removeCharacter(trim($sql_log),",");
	
	return($sql_return."\n\n".$sql_log);
	}
}
?>