<?php
session_start();
?>
<form name="form1" method="post">
  Branch<input type="text" name="Branch"><br>
  Neg<input type="text" name="Neg"><br>
  Address1<input type="text" name="Address1"><br>
  house_number<input type="text" name="house_number"><br>
  osx<input type="text" name="osx"><br>
  osy<input type="text" name="osy"><br>
  <input type="submit" name="Submit" value="Submit">
</form>

<pre>
<?php
$_row = 1840;
$_table = "property";
$_field = "prop_ID";



$dsn = array(
    'phptype'  => "mysql",
    'database' => "ws_db",
    'username' => "root",
    'password' => "changeoninstall"//"a345uyv"
);

include("DB.php");
$db = DB::connect($dsn);
if (DB::isError($db)) {  die("connection error: ".$db->getMessage()); }
$db->setFetchMode(DB_FETCHMODE_ASSOC);

function removeCharacter($whichData,$whichString) {
	$whichData = trim($whichData);
	if(substr($whichData,strlen($whichData)-1) == $whichString) {
		$whichData = substr($whichData,0,strlen($whichData)-1);		
		}
	return $whichData;	
	} 
	
// 3 arrays, first the list of fieldnames, then the list of old values, then the list of new values.
// only add to the arrays if data is present



// validation - POST names do not have to be the same as field names, but it helps
// perform all validation on post values, and add them to the 2 arrays
if (!$_POST["Branch"]) {
	$errors[] = "Branch is a required field";
	} else {
	$fieldnames[] = "Branch";
	$newvalues[] = trim($_POST["Branch"]);
	}

if (!$_POST["Neg"]) {
	$errors[] = "Negotiator is a required field";
	} else {
	$fieldnames[] = "Neg";
	$newvalues[] = trim($_POST["Neg"]);
	}	
	
if (!$_POST["Address1"]) {
	$errors[] = "Street Name is a required field";
	}
else {
	$fieldnames[] = "Address1";
	$newvalues[] = trim($_POST["Address1"]);
	}
	
if (!$_POST["house_number"]) {
	$errors[] = "House Number is a required field";
	}
else {
	$fieldnames[] = "house_number";
	$newvalues[] = trim($_POST["house_number"]);
	}
	
if (!$_POST["Postcode"]) {
	$errors[] = "Postcode is a required field";
	}
else {
	$fieldnames[] = "Postcode";
	$newvalues[] = trim($_POST["Postcode"]);
	}

if (!$_POST["osx"]) {
	$osx = 0;
	}
else {
	$fieldnames[] = "osx";
	$newvalues[] = trim($_POST["osx"]);
	}
	
if (!$_POST["osy"]) {
	$osy = 0;
	}
else {
	$fieldnames[] = "osy";
	$newvalues[] = trim($_POST["osy"]);
	}





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

for($i=0; $i<count($fieldnames); $i++) {
	// construct sql select field names from POST array
	$_fields .= $fieldnames[$i].",";
	}
$_fields = removeCharacter(trim($_fields),",");
if ($_fields) {

$sql = "SELECT ".$_fields." FROM ".$_table." WHERE ".$_field." = ".$_row."";
$result = mysql_query($sql);	
if (!$result)
	die("MySQL Error:  ".mysql_error()."<pre>".$sql."</pre>");
	
while($row = mysql_fetch_array($result)) {
	for($i=0; $i<count($fieldnames); $i++) {
		$sql_field = $fieldnames[$i];
		$oldvalues[] = $row[$sql_field];
		}
	}
print_r($fieldnames);
print_r($newvalues);
print_r($oldvalues);

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

// 
echo "\n\nUPDATE ".$_table." SET ".removeCharacter(trim($sql_update),",")." WHERE ".$_field." = ".$_row."";
echo "\n\nINSERT INTO ".$_table." (".removeCharacter(trim($sql_insert1),",").") VALUES (".removeCharacter(trim($sql_insert2),",").")";
echo "\n\n".removeCharacter(trim($sql_log),",");
}
?>
</pre>
