<?php
// database connection and functions

// db settings, required on reboot (or ideally /configure)
# SET GLOBAL group_concat_max_len = 2048

// PEAR db class
require_once("DB.php");

$dsn = array(
	'phptype'  => WS_DB_TYPE,
	'database' => WS_DB_DATABASE,
	'username' => WS_DB_USERNAME,
	'password' => WS_DB_PASSWORD,
	'hostspec' => WS_DB_HOST,
);

$db = DB::connect($dsn);

if (DB::isError($db)) {
	die("Fatal error: " . $db->getMessage());
}
$db->query("SET NAMES utf8");
$db->setFetchMode(DB_FETCHMODE_ASSOC);

// db_query 24/03/2006
// insert or update from database and store old and new values in the changelog table
// $_data - array, with database field names as keys, and entered values as values
// $_action - INSERT, UPDATE or DELETE
// $_table - table name
// $_field - field name to do the WHERE (i.e. the name of the id field)
// $_row - id number to complete the WHERE (if INSERT, we use temp_row_id which is replaced with the insert_id)
// e.g. "SELECT ".$_fields." FROM ".$_table." WHERE ".$_field." = ".$_row."";

// 02/04/07 - odd, wrong user id being stored sometimes in changelog table, so changed var name to:
$current_user_id = (isset($_SESSION["auth"]["use_id"]) ? $_SESSION["auth"]["use_id"] : "0");

function db_query($data, $action, $table, $field, $row = "temp_row_id", $return = false)
{
	global $current_user_id;
	// update - select existing values for comparison
	if ($action == "UPDATE") {
		$_fields = '';
		foreach ($data as $_key => $_val) {
			$_fields .= "`" . $_key . "`,";
			$_newvalues[] = format_data($_val);
		}
		$_fields = remove_lastchar(trim($_fields), ",");
		if ($_fields) {
			$_sql_select = "SELECT " . $_fields . " FROM " . $table . " WHERE " . $field . " = " . $row . " LIMIT 1";
			$_result     = mysql_query($_sql_select);
			if (!$_result) {
				die("MySQL Error:  " . mysql_error() . "<pre>db_query: " . $_sql_select . "</pre>");
			}
			while ($_row_select = mysql_fetch_array($_result)) {
				foreach ($data as $_key => $_val) {
					// format old values to make sure sql works, this makes line breaks count as changed
					$_oldvalues[$_key] = addslashes($_row_select[$_key]);
				}
			}
			mysql_free_result($_result);
		}
	} // insert - nothing to compare so no select. create array of old values with NULL
	elseif ($action == "INSERT") {
		foreach ($data as $_key => $_val) {
			$_newvalues[]      = format_data($_val);
			$_oldvalues[$_key] = "NULL";
		}
	}

	// we should now have 2 arrarys of fieldnames as keys and new and old values
	if (count($data) !== count($_oldvalues)) {
		echo 'db_query: array count not matched<p>';
		exit;
	}

	// construct sql for $_table AND changelog table, single query with multiple inserts
	$_sql_log = "INSERT INTO changelog
	(`cha_user`,`cha_session`,`cha_action`,`cha_table`,`cha_row`,`cha_field`,`cha_old`,`cha_new`)
	VALUES
	";

	// loop _data, check if current(old) value is different to new, and create sql statements accordingly
	$_sql_update  = '';
	$_sql_insert1 = '';
	$_sql_insert2 = '';
	$_msg         = '';
	$_changecount = 0;
	foreach ($data as $_key => $_val) {
		if ($_oldvalues[$_key] <> $_val) {
			if ($_val == "NULL") {
				$_sql_update .= $_key . "=NULL,";
			} elseif ($_val == "") {
				$_sql_update .= $_key . "='',";
			} else {
				$_sql_update .= $_key . "='" . format_data($_val) . "',";
			}
			$_sql_insert1 .= $_key . ",";
			if ($table == 'property') {
				$_sql_insert2 .= "'" . addslashes($_val) . "',";
				$_sql_log .= "('" . $current_user_id . "','" . session_id() . "','" . $action . "','" . $table . "','" . $row . "','" . $_key . "','" . $_oldvalues[$_key] . "','" . addslashes($_val) . "'),\n";

			} else {
				$_sql_insert2 .= "'" . format_data($_val) . "',";
				$_sql_log .= "('" . $current_user_id . "','" . session_id() . "','" . $action . "','" . $table . "','" . $row . "','" . $_key . "','" . $_oldvalues[$_key] . "','" . format_data($_val) . "'),\n";

			}
			$_msg .= $_key . " was changed from " . $_oldvalues[$_key] . " to " . $_val . "\n";
			$_changecount++;

			$returnArray[$_key] = array(
				'old' => $_oldvalues[$_key],
				'new' => $_val
			);

		}
	}

	$_sql_log     = remove_lastchar(trim($_sql_log), ",");
	$_sql_update  = remove_lastchar(trim($_sql_update), ",");
	$_sql_insert1 = remove_lastchar(trim($_sql_insert1), ",");
	$_sql_insert2 = remove_lastchar(trim($_sql_insert2), ",");

	if ($action == "UPDATE") {
		$_sql_return = "UPDATE " . $table . " SET " . $_sql_update . " WHERE " . $field . " = " . $row;
	} elseif ($action == "INSERT") {
		$_sql_return = "INSERT INTO " . $table . " (" . $_sql_insert1 . ") VALUES (" . $_sql_insert2 . ")";
	}
	/* elseif ($_action == "DELETE") {
		$_sql_return = "DELETE FROM ".$_table." WHERE ".$_field." = ".$_row;
		}*/

	if ($_changecount) { // only execute the sql queries if a change has been made
		$_result_return = mysql_query($_sql_return);
		if (!$_result_return) {
			die("MySQL Error:  " . mysql_error() . "<pre>db_query RETURN: " . $_sql_return . "</pre>");
		}
		if ($action == "UPDATE") {
			$_insert_id = $row;
		} elseif ($action == "INSERT") {
			$_insert_id = mysql_insert_id();
			$_sql_log   = str_replace("temp_row_id", $_insert_id, $_sql_log); // replace temp_row_id with insert_id
		}

		//mysql_free_result($_result_return);

		$_result_log = mysql_query($_sql_log);
		if (!$_result_log) {
			die("MySQL Error:  " . mysql_error() . "<pre>db_query LOG: " . $_sql_log . "</pre>");
		}
		//mysql_free_result($_result_log);
	}

	// dubug info
	if ($return == true) {

		return array(
			'row'   => $_insert_id,
			'array' => $returnArray
		);

	} else {
		return ($_insert_id); // return the effected row in $_table, not changelog
	}

	unset($data, $action, $table, $field, $row);
}

// db_enum
// return formatted list from enum field
// $_table is the name of the table
// $_field is the name of the enum field
// $_type can be select, radio or checkbox - or array
// $_pick if you want an option to be selected by default
// $_isnull if you want a blank entry at the top of the list ($_type=select only)
function db_enum($_table, $_field, $_type = "select", $_pick = null, $_isnull = null, $_style = null)
{
	$_render = "";
	$_result = mysql_query("describe $_table $_field");
	$_row    = mysql_fetch_array($_result);
	$_value  = $_row["Type"];
	mysql_free_result($_result);
	preg_match_all("/'([^']+)'/", $_value, $_matches, PREG_SET_ORDER);
	$_count = count($_matches);

	if ($_type == "array") {
		foreach ($_matches as $_v) {
			$_render[$_v[1]] = $_v[1];
		}
	} elseif ($_type == "checkbox") {
		foreach ($_matches as $_v) {
			$_render .= '<input type="checkbox" name="' . $_field . '[]" value="' . $_v[1] . '" id="' . $_field . '_' . $_v[1] . '"';
			if ($_v[1] == $_pick) {
				$_render .= ' checked';
			}
			$_render .= ' /><label for="' . $_field . '_' . $_v[1] . '" class="checkbox">' . $_v[1] . '</label>
			';

		}
	} elseif ($_type == "radio") {
		foreach ($_matches as $_v) {
			$_render .= '<input type="radio" name="' . $_field . '" value="' . $_v[1] . '" id="' . $_field . '_' . $_v[1] . '"';
			if ($_v[1] == $_pick) {
				$_render .= ' checked';
			}
			$_render .= ' /><label for="' . $_field . '_' . $_v[1] . '" class="radio">' . $_v[1] . ' </label>
			';
		}
	} elseif ($_type == "select") {
		// insert a blank option (or allow blank option is no option is previously select)
		if ($_isnull && !isset($_pick)) {
			$_render .= '<option value=""></option>
			';
		}
		foreach ($_matches as $_v) {
			$_render .= '<option value="' . $_v[1] . '"';
			if ($_v[1] == $_pick) {
				$_render .= ' selected';
			}
			$_render .= '>' . $_v[1] . '</option>
			';
		}
		$_render = '<select name="' . $_field . '" ' . $_style . '>' . $_render . '</select>
		';
	}

	return $_render;
	unset($_table, $_where, $_query, $_result, $_row, $_v, $_value, $_matches, $_render, $_count, $_isnull);
}

// db_lookup
// return formatted list from a lookup table
// a lookup table consist of at least 2 columns (0=id and 1=title)
// $_name is the name of the form element
// $_table is the name of the table
// $_type can be select, radio or checkbox
// $_pick if you want an option to be selected by default
// $_isnull if you want a blank entry at the top of the list ($_type=select only)
// $_where - array (in form of WHERE $key = '$val' AND) to add to the sql statement (added 02/03/07)
function db_lookup($_name, $_table, $_type = "select", $_pick = null, $_order = null, $_isnull = null, $_where = null)
{
	$_render = "";
	$_query  = "SELECT * FROM $_table";
	if (is_array($_where)) {
		foreach ($_where AS $key => $val) {
			$_wheresql .= $key . " = '" . $val . "' AND ";
		}
		$_query .= " WHERE " . remove_lastchar(trim($_wheresql), 'AND');
	}
	if ($_order) {
		$_query .= " ORDER BY $_order";
	}
	$_result = mysql_query($_query);

	if (!$_result) {
		die("MySQL Error:  " . mysql_error());
	}

	if ($_type == "array") {
		while ($_v = mysql_fetch_array($_result)) {
			$_render[$_v[0]] = $_v[1];
		}
	} elseif ($_type == "checkbox") {
		while ($_v = mysql_fetch_array($_result)) {
			$_render .= '<input type="checkbox" name="' . $_table . '" value="' . $_v[0] . '"';
			if ($_v[0] == $_pick) {
				$_render .= ' checked';
			}
			$_render .= ' />' . $_v[1] . '
			';
		}
	} elseif ($_type == "radio") {
		while ($_v = mysql_fetch_array($_result)) {
			$_render .= '<input type="radio" name="' . $_table . '" value="' . $_v[0] . '"';
			if ($_v[0] == $_pick) {
				$_render .= ' checked';
			}
			$_render .= ' />' . $_v[1] . '
			';
		}
	} elseif ($_type == "select") {
		// insert a blank option
		if ($_isnull && !isset($_pick)) {
			$_render .= '<option value=""></option>
			';
		}
		while ($_v = mysql_fetch_array($_result)) {
			$_render .= '<option value="' . $_v[0] . '"';
			if ($_v[0] == $_pick) {
				$_render .= ' selected';
			}
			$_render .= '>' . $_v[1] . '</option>
			';
		}
		$_render = '<select name="' . $_name . '">' . $_render . '</select>
		';
	}

	mysql_free_result($_result);
	return $_render;
	unset($_table, $_where, $_query, $_sqlresult, $_sqlrow, $_v, $_value, $_matches, $_render, $_count, $_isnull);
}

// db_dropdown
// return a simple dropdown of numbers
// $_field is the name of the field which is used for the name=""
// $_max number to stop at
// $_pick if you want an option to be selected by default
// $_min number to start at
// $_isnull if you want a blank entry at the top of the list ($_type=select && !$_pick)
function db_dropdown($_field, $_max = "10", $_pick = null, $_min = "0", $_isnull = null)
{
	$_render = "";
	$_render = '<select name="' . $_field . '">';
	// insert a blank option
	if ($_isnull && !isset($_pick)) {
		$_render .= '<option value=""></option>
		';
	}
	for ($_i = $_min; $_i <= $_max; $_i++) {
		$_render .= '<option value="' . $_i . '"';
		if ($_i == "$_pick") {
			$_render .= ' selected';
		}
		$_render .= '>' . $_i . '</option>
		';
	}
	$_render .= '</select>';

	return $_render;
	unset($_field, $_max, $_pick, $_min, $_render, $_i, $_isnull);
}

// return the branch id from given deal id
function getBranch($dea_id)
{
	$_sql    = "SELECT dea_branch FROM deal WHERE dea_id = " . $dea_id . " LIMIT 1";
	$_result = mysql_query($_sql);
	if (!$_result) {
		die("MySQL Error:  " . mysql_error() . "<pre>getBranch: " . $_sql . "</pre>");
	}
	while ($row = mysql_fetch_array($_result)) {
		$branch = $row["dea_branch"];
	}
	mysql_free_result($_result);
	return $branch;
}

?>