<?php
# todo:
# add specific field types (sale min and max price, let min and max price, number lists (min to max)
# property types and property requirements (property type currently in ptype function)
# DONE change "default" to "init" (initial value), and then allow default values to be used (i.e. default radio checked)
# move required functions into this class (db_enum, db_lookup etc) and make public so they can still be used in isolation
# allow null values on selects

/*
form constructor

standard form elements: text, textfield, radio, checkbox, select, file
all enclosed by a div, and with a label (blank label is permitted)

hidden form elements are added with no encloseding tags
also posisble to add inline elements, build several elements and then enclose by same tag
create a group of elements and enclose them with a single label and div (group)

advanced form elements: multi-select, multiple text fields (salutation, fname and sname)
may need special treatment, specific functions, to create these
mulitple text fields should appear within the same div and with same label tag

form elements from database: enum (use php function), lookup (use php function)

before we start:
to build the form we need
mysql database field names (to be used as html field names also)
field titles (labels)
required fields (3 states, 1:not required 2:required 3:looks required but isnt - css only)
existing (or default) values, either from existing database record or from querystring (if returning from error)...
...or combination of the two, but we need to choose which takes preference (probably querystring, needs thought)...
...existing obvioulsy overwrites default, and default values should be cleared onfocus

:change to original plan (05 Sept), default values are to be for radio buttons, checkboxes, selects etc and are allowedas value
:initial value are helpers, like Forename, Surname, which are to be cleared onfocus and are not allowed to entered as value

to validate the data we need
mysql database field names (we get these from $_GET or $_POST, along with the new values)
field titles (labels) for use in error_message
required fields
validation
formatting


build the form:
create form tag (open and closing tags) with action, method, name and other attibutes (js, css, etc)
create fieldset tag (open and closing tags) with css, id
create legend with href, css, js etc
create fields
create hidden fields
create buttons - submit, button, reset, cancel (not part of data array)
fields are created with a label and div tag, unless specified otherwise. they can be inline also

once data is posted:
validate requried fields, return user to previous page using http_build_query and error_message functions
add data to db_data array for use with db_query function


IDEAS:
that might or might not make it into the class

null - puts a blank option at the top of a <select> to allow "i dunno" option. only relevant for selects.

*/
$current_user_id = $_SESSION["auth"]["use_id"];
$current_branch = $_SESSION["auth"]["use_branch"];

class Form
{

	var $_fields; // the data array
	var $_querystring; // the $_GET array
	var $_type; // text,textfield,radio,checkbox,select,file,multi-select,submit,reset,cancel,hidden,enum (use php function), lookup (use php function)
	var $_name; // the name of the field
	var $_label; // the label to show next to field - if we want to show 2 fields inline then no label should be used
	var $_value; // value from database
	var $_init; // initial value, not allowed to be saved
	var $_default; // default value
	var $_attr; // array of html attributes and their values
	var $_opt; // list of options for select, radio and checkbox
	var $output; //

	// return an error message if the form is not properly configured
	function errorMessage($_errors)
	{

		echo '<p>There is an error in form configuration, the form cannot be built</p>';
		echo '<p>' . $_errors . '</p>';
		exit;
	}

	/*
	addData
	initial function to process the data array
	overwrite default (or db) values with GET/POST array if matched
	uses class functions to add fields and rows to the output
	*/
	function addData($_fields, $_querystring = null)
	{

		foreach ($_fields AS $field_name => $field_array) {

			#echo $field_name.' = '.$field_array['value'].print_r($field_array)."<br>\n\n\n\n";

			// allow GET/POST value to overwrite $fields[] value (when returning from error page)
			// not permitted for hidden fields
			if ($_querystring[$field_name] && $field_array['type'] !== 'hidden') {
				$field_array['value'] = $_querystring[$field_name];
			}
			// check for requried values
			if (!$field_array['type']) {
				$this->errorMessage("No type for : " . $field_name);
				exit;
			}
			// type=select, radio, checkbox must have options
			if ($field_array['type'] == "select" || $field_array['type'] == "radio" || $field_array['type'] == "checkbox") {
				if (!$field_array['options']) {
					$this->errorMessage("No options for " . $field_array['type'] . ": " . $field_name);
					exit;
				}
			}
			// add required fields to own array
			$this->requiredFields[$field_name] = $field_array['required'];

			// add required fields to javascript
			// this dosen't currently work due to multi forms on one page validating hidden fields which wont be processed
			// probably need to do document.write on showHide function?
			//if ($field_array['required'] == "2") {
			//	$this->requiredJavascript[$field_name] = array($field_name,"req",$field_array['label']." is required");
			//	}

			// get sub-arrays into variables, or sub-sub-arrays into more arrays (for want of a better explanation)
			foreach ($field_array AS $title => $value) {

				// get attributes into named array, if it isnt array it gets ignored
				if ($title == 'attributes' && is_array($value)) {
					$attributes = $value;
				} // get options for select, radio or checkbox into array, if it isnt an array it gets ignored
				elseif ($title == 'options' && $value) {
					$options = $value;
				} else {
					$title = trim($title);
					$value = trim($value);
				}
			}

			// use initial value
			// not allowed to be stored in database, ie for helper items - e.g.  forename(s) / http://
			if (!$field_array['value'] && $field_array['init']) {
				$field_array['value'] = $field_array['init'];
			}

			// use default value
			// allowed to be stored, ie for default radion button value
			if (!$field_array['value'] && $field_array['default']) {
				$field_array['value'] = $field_array['default'];
			}

			// groups - more than one field in a row
			// add items to $group, unless last_in_group is set, in which case add $group to form
			if ($field_array['group']) {
				$group .= $this->makeField($field_array['type'], $field_name, $field_array['label'], $field_array['value'], $attributes, $options) . ' ';
				if ($field_array['last_in_group']) {
					// add the tooltip to $group if present
					if ($field_array['tooltip']) {
						$group = $this->makeTooltip($field_array['tooltip']) . $group;
					}
					$this->addItem($field_name, $field_array['group'], $group);
					$group = '';
				}
			} // or just add the element to the form if not a group
			else {
				// hidden fields get added without any additional html tags or tooltip
				if ($field_array['type'] == "hidden") {
					$this->addHtml($this->makeField($field_array['type'], $field_name, $field_array['label'], $field_array['value'], $attributes, $options));

					// special conditions for telephone number (remove the label)
				} elseif ($field_array['type'] == "tel") {

					$this->addHtml($this->makeField($field_array['type'], $field_name, $field_array['label'], $field_array['value'], $attributes, $options, $field_array['tooltip']));

					// all other fields get added as a complete row
				} else {
					$this->addRow($field_array['type'], $field_name, $field_array['label'], $field_array['value'], $attributes, $options, $field_array['tooltip']);
				}
			}
			// clear values before looping
			$field_array = '';
			$attributes  = '';
			$options     = '';
		}
	}

	// returns a solitary field
	function makeField($_type, $_name, $_label = null, $_value = null, $_attr = null, $_opt = null)
	{

		global $current_user_id;
		global $current_branch;
		#echo $_type.'='.$_value.'<br>';
		// remove slashes from value for display 14/06/06
		$_value = str_replace("\\", "", $_value);

		// get attributes from array and create html formatted string (key="val")
		if (is_array($_attr)) {
			foreach ($_attr AS $_key => $_val) {
				$_attributes .= ' ' . $_key . '="' . $_val . '"';
			}
		}
		// text field
		if ($_type == "text") {
			$_output .= '<input type="text" name="' . $_name . '" id="' . $_name . '" value="' . $_value . '"' . $_attributes . ' />' . "\n";
		} // textarea
		elseif ($_type == "textarea") {
			$_output .= '<textarea name="' . $_name . '" id="' . $_name . '"' . $_attributes . '>' . $_value . '</textarea>' . "\n";
		} // radio button
		elseif ($_type == "radio") {
			// get options from $_opt array	(required)
			foreach ($_opt AS $_optkey => $_optval) {
				$_output .= '<label for="' . $_name . '' . $_optval . '"><input type="radio" name="' . $_name . '" id="' . $_name . '' . $_optval . '" value="' . $_optval . '"';
				if ($_optkey == $_value) {
					$_output .= ' checked="checked"';
				}
				$_output .= '' . $_attributes . ' />' . $_optkey . '</label>' . "\n";
			}
		} // checkbox
		elseif ($_type == "checkbox") {
			$value_array = explode('|', $_value);
			// get options from $_opt array	(required)
			foreach ($_opt AS $_optkey => $_optval) {
				$_output .= '<label for="' . $_name . '' . $_optval . '"><input type="checkbox" name="' . $_name . '[]" id="' . $_name . '' . $_optval . '" value="' . $_optval . '"';
				if (in_array($_optkey, $value_array)) {
					$_output .= ' checked="checked"';
				}
				$_output .= '' . $_attributes . ' />' . $_optkey . '</label>' . "\n";
			}
		} elseif ($_type == "checkboxSingle") {
			$value_array = explode('|', $_value);
			// get options from $_opt array	(required)
			$_output .= '<label for="' . $_name . '' . $_opt . '"><input type="checkbox" name="' . $_name . '" id="' . $_name . '' . $_opt . '" value="' . $_opt . '"';
			if (in_array($_opt, $value_array)) {
				$_output .= ' checked="checked"';
			}
			$_output .= '' . $_attributes . ' /></label>' . "\n";
		} // checkbox in a table
		elseif ($_type == "checkbox_table") {

			$columns     = 4;
			$count       = 1;
			$value_array = explode('|', $_value);
			// get options from $_opt array	(required)
			foreach ($_opt AS $_optkey => $_optval) {
				$_output .= '<td width="' . (100 / $columns) . '%"><label for="' . $_name . '' . $_optval . '"><input type="checkbox" name="' . $_name . '[]" id="' . $_name . '' . $_optval . '" value="' . $_optval . '"';
				if (in_array($_optkey, $value_array)) {
					$_output .= ' checked="checked"';
				}
				// special function for dea_status
				if ($_optkey == "Under Offer with Other") {
					$_optkey = "U/O with Other";
				}
				$_output .= '' . $_attributes . ' />' . $_optkey . '</label></td>' . "\n";
				if ($count % $columns == 0) {
					$_output .= '</tr><tr>';
				}
				$count++;
			}
			$_output = '<table class="checkbox_table"><tr>' . $_output . '</tr></table>';

		}
		// file
		if ($_type == "file") {
			$_output .= '<input type="file" name="' . $_name . '" id="' . $_name . '" value="' . $_value . '"' . $_attributes . ' />' . "\n";
		} // hidden
		elseif ($_type == "hidden") {
			$_output .= '<input type="hidden" name="' . $_name . '" id="' . $_name . '" value="' . $_value . '"' . $_attributes . ' />' . "\n";
		} // button (submit, reset or button)
		elseif ($_type == "submit" || $_type == "reset" || $_type == "button") {
			$_output .= '<input type="' . $_type . '" name="' . $_name . '" id="' . $_name . '" value="' . $_value . '"' . $_attributes . ' />' . "\n";
		} // select
		elseif ($_type == "select") {
			// adding a blank value to top of list if "blank" is present in options array
			if ($_opt['blank']) {
				$_options .= '  <option value=""></option>' . "\n";
				array_shift($_opt);
			}
			// adding a blank value to top of list if no value is selected (ie stored in db)
			if (!$_value) {
				#$_options .= '  <option value=""></option>'."\n";
			}
			// get options from $_opt array	(required)
			foreach ($_opt AS $_optkey => $_optval) {
				$_options .= '  <option value="' . $_optkey . '"';
				if ($_optkey == $_value) {
					$_options .= ' selected="selected"';
				}
				$_options .= '>' . $_optval . '</option>' . "\n";
			}
			$_output .= '<select name="' . $_name . '" id="' . $_name . '"' . $_attributes . '>' . "\n";
			$_output .= $_options . '</select>' . "\n";
		} // select_multi
		elseif ($_type == "select_multi") {
			// get output from $_opt array
			// the output comes from other functions, which get specific info as required and return html
			$_output .= $_opt['dd1'] . ' ' . $_opt['dd2'];
		} // select_number (simple select field looped from min to max values)
		elseif ($_type == "select_number") {
			// default min and max to 0 => 10
			if (!$_opt['min']) {
				$_opt['min'] = '0';
			}
			if (!$_opt['max']) {
				$_opt['max'] = '10';
			}
			// adding a blank value to top of list if "blank" is present in options array, or if no value is stored
			if ($_opt['blank'] || !$_value) {
				$_options .= '  <option value=""></option>' . "\n";
			}
			for ($_i = $_opt['min']; $_i <= $_opt['max']; $_i++) {
				$_options .= '  <option value="' . $_i . '"';
				if ($_i == "$_value") {
					$_options .= ' selected="selected"';
				}
				$_options .= '>' . $_i . '</option>' . "\n";
			}
			$_output .= '<select name="' . $_name . '" id="' . $_name . '"' . $_attributes . '>' . "\n" . $_options . '</select>' . "\n";
		} // select_price (specific dropdown for min and max price range)
		// options array is used to get default value and sales or lettings price ranges
		elseif ($_type == "select_price") {
			// get default value from options array
			$_scope   = $_opt['scope'];
			$_term    = $_opt['term'];
			$_default = $_opt['default'];

			if ($_default) {
				$_options .= '  <option value="">' . $_default . '</option>';
			}
			// loop through price brackets
			if ($_scope == 'sales') {
				/*
				for ($i = 80000; $i <= 500000;) {
					$_options .= '<option value="'.$i.'"';
					if ($i == $_value) {
						$_options .= ' selected="selected"';
						}
					$_options .= '>'.format_price($i).'</option>
					';
					$i = $i+5000;
					}
				*/
				$t = array_merge([125000, 150000], range(200000, 500000, 50000), range(600000, 1000000, 100 * 1000), range(2000000, 6000000, 1000000));
				$_options .= implode('', array_map(function ($v) use ($_value) {
					return "<option value='{$v}' " . ($_value == $v ? 'selected' : '') . ">" . format_price($v) . "</option>";
				}, $t));

//				for ($i = 0; $i <= 990000;) {
//					$_options .= '  <option value="' . $i . '"';
//					if ($i == $_value) {
//						$_options .= ' selected="selected"';
//					}
//					$_options .= '>' . format_price($i) . '</option>' . "\n";
//					$i = $i + 10000;
//				}
//				for ($i = 1000000; $i <= 3000000;) {
//					$_options .= '  <option value="' . $i . '"';
//					if ($i == $_value) {
//						$_options .= ' selected="selected"';
//					}
//					$_options .= '>' . format_price($i) . '</option>' . "\n";
//					$i = $i + 1000000;
//				}
			} elseif ($_scope == 'lettings') {
				for ($i = 0; $i <= 1000;) {
					$_options .= '  <option value="' . $i . '"';
					if ($i == $_value) {
						$_options .= ' selected="selected"';
					}
					if ($_term == "pcm") {
						$_options .= '>' . format_price(pw2pcm($i)) . '</option>' . "\n";
					} else {
						$_options .= '>' . format_price($i) . '</option>' . "\n";
					}
					$i = $i + 50;
				}
				for ($i = 1000; $i <= 5000;) {
					$_options .= '  <option value="' . $i . '"';
					if ($i == $_value) {
						$_options .= ' selected="selected"';
					}
					if ($_term == "pcm") {
						$_options .= '>' . format_price(pw2pcm($i)) . '</option>' . "\n";
					} else {
						$_options .= '>' . format_price($i) . '</option>' . "\n";
					}
					$i = $i + 250;
				}
			}

			$_output .= '<select name="' . $_name . '" id="' . $_name . '"' . $_attributes . '>' . "\n" . $_options . '</select>' . "\n";
		} // duration for appointments
		elseif ($_type == "select_duration") {

			for ($i = 15; $i <= 55;) {
				$_options .= '  <option value="' . $i . '"';
				if ($i == $_value) {
					$_options .= ' selected="selected"';
				}
				$_options .= '>' . $this->duration($i, (isset($_opt['format']) ? $_opt['format'] : null)) . '</option>' . "\n";
				$i = $i + 5;
			}
			for ($i = 60; $i <= 480;) {
				$_options .= '  <option value="' . $i . '"';
				if ($i == $_value) {
					$_options .= ' selected="selected"';
				}
				$_options .= '>' . $this->duration($i, (isset($_opt['format']) ? $_opt['format'] : null)) . '</option>' . "\n";
				$i = $i + 15;
			}
			$_output .= '<select name="' . $_name . '" id="' . $_name . '"' . $_attributes . '>' . "\n" . $_options . '</select>' . "\n";
		} // branch picker, active branches only
		elseif ($_type == "select_branch") {

			if ($_opt) {
				foreach ($_opt AS $_optkey => $_optval) {
					$_options .= '  <option value="' . $_optkey . '">' . $_optval . '</option>' . "\n";
					#array_shift($_opt);
				}
			}
			// if no value is present, select user's branch by default
			if (!$_value) {
				$_value = $current_branch;
			}

			$_sql_branch    = "SELECT bra_id,bra_colour,bra_title FROM branch
			WHERE branch.bra_status = 'Active'
			ORDER BY bra_id";
			$_result_branch = mysql_query($_sql_branch);
			if (!$_result_branch) {
				die("MySQL Error:  " . mysql_error() . "<pre>db_query: " . $_sql_branch . "</pre>");
			}
			while ($_row_branch = mysql_fetch_array($_result_branch)) {
				$_options .= '<option value="' . $_row_branch["bra_id"] . '" style="background-color: #' . $_row_branch["bra_colour"] . '"';
				if ($_row_branch["bra_id"] == "$_value") {
					$_options .= ' selected="selected"';
				}
				$_options .= '>' . $_row_branch["bra_title"] . '</option>' . "\n";
			}
			$_output .= '<select name="' . $_name . '" id="' . $_name . '"' . $_attributes . '>' . "\n" . $_options . '</select>' . "\n";
		} // branch picker without sydenham lettings to prevent appointments being booked into the unused calendar
		elseif ($_type == "select_branch_2") {

			if ($_opt) {
				foreach ($_opt AS $_optkey => $_optval) {
					$_options .= '  <option value="' . $_optkey . '">' . $_optval . '</option>' . "\n";
					#array_shift($_opt);
				}
			}
			// if no value is present, select user's branch by default
			if (!$_value) {
				$_value = $current_branch;
			}

			$_sql_branch    = "SELECT bra_id,bra_colour,bra_title FROM branch
			WHERE branch.bra_status = 'Active' AND bra_id != 4
			ORDER BY bra_id";
			$_result_branch = mysql_query($_sql_branch);
			if (!$_result_branch) {
				die("MySQL Error:  " . mysql_error() . "<pre>db_query: " . $_sql_branch . "</pre>");
			}
			while ($_row_branch = mysql_fetch_array($_result_branch)) {
				$_options .= '<option value="' . $_row_branch["bra_id"] . '" style="background-color: #' . $_row_branch["bra_colour"] . '"';
				if ($_row_branch["bra_id"] == "$_value") {
					$_options .= ' selected="selected"';
				}
				$_row_branch["bra_title"] = str_replace('Camberwell Lettings', 'Lettings', $_row_branch["bra_title"]);
				$_options .= '>' . $_row_branch["bra_title"] . '</option>' . "\n";
			}
			$_output .= '<select name="' . $_name . '" id="' . $_name . '"' . $_attributes . '>' . "\n" . $_options . '</select>' . "\n";
		} // user picker, currently all staff, but in future upgrade to allow different roles
		elseif ($_type == "select_user") {
			// adding a blank value to top of list if "blank" is present in options array

			if ($_opt) {
				foreach ($_opt AS $_optkey => $_optval) {
					$_options .= '  <option value="' . $_optkey . '">' . $_optval . '</option>' . "\n";
					#array_shift($_opt);
				}
			}
			/*
			if (!$_value) {
				$_options .= '  <option value=""></option>'."\n";
				}
			*/
			$_sql_user    = "SELECT use_id,CONCAT(user.use_fname,' ',user.use_sname) AS use_name,use_colour
			FROM user WHERE user.use_status = 'Active' ORDER BY use_name";
			$_result_user = mysql_query($_sql_user);
			if (!$_result_user) {
				die("MySQL Error:  " . mysql_error() . "<pre>db_query: " . $_sql_user . "</pre>");
			}
			while ($_row_user = mysql_fetch_array($_result_user)) {
				$_options .= '<option value="' . $_row_user["use_id"] . '"';
				if ($_row_user["use_id"] == "$_value") {
					$_options .= ' selected="selected"';
				}
				$_options .= '>' . $_row_user["use_name"] . '</option>' . "\n";
			}
			$_output .= '<select name="' . $_name . '" id="' . $_name . '"' . $_attributes . '>' . "\n" . $_options . '</select>' . "\n";
		} // user picker, currently all staff, but in future upgrade to allow different roles
		elseif ($_type == "select_neg") {
			// adding a blank value to top of list if "blank" is present in options array

			if ($_opt) {
				foreach ($_opt AS $_optkey => $_optval) {
					$_options .= '  <option value="' . $_optkey . '">' . $_optval . '</option>' . "\n";
					#array_shift($_opt);
				}
			}
			/*
			if (!$_value) {
				$_options .= '  <option value=""></option>'."\n";
				}
			*/
			$_sql_user    = "SELECT use_id,CONCAT(user.use_fname,' ',user.use_sname) AS use_name,use_colour
			FROM user
			LEFT JOIN link_user_to_role ON user.use_id = link_user_to_role.u2r_use
			LEFT JOIN role ON link_user_to_role.u2r_rol = role.rol_id
			WHERE user.use_status = 'Active' AND rol_title = 'Negotiator'
			ORDER BY use_name";
			$_result_user = mysql_query($_sql_user);
			if (!$_result_user) {
				die("MySQL Error:  " . mysql_error() . "<pre>db_query: " . $_sql_user . "</pre>");
			}
			while ($_row_user = mysql_fetch_array($_result_user)) {
				$_options .= '<option value="' . $_row_user["use_id"] . '"';
				if ($_row_user["use_id"] == "$_value") {
					$_options .= ' selected="selected"';
				}
				$_options .= '>' . $_row_user["use_name"] . '</option>' . "\n";
			}
			$_output .= '<select name="' . $_name . '" id="' . $_name . '"' . $_attributes . '>' . "\n" . $_options . '</select>' . "\n";
		} // colour picker, get colours from colour table and display in select with background colours
		// not being used, dont like it.
		elseif ($_type == "select_colour") {
			$_sql_colour    = "SELECT col_colour,col_title FROM colour";
			$_sql_colour    = "SELECT
			user.use_colour,
			colour.col_colour
			FROM user
			LEFT JOIN colour ON user.use_colour != colour.col_colour
			GROUP BY colour.col_colour";
			$_result_colour = mysql_query($_sql_colour);
			if (!$_result_colour) {
				die("MySQL Error:  " . mysql_error() . "<pre>db_query: " . $_sql_colour . "</pre>");
			}
			while ($_row_colour = mysql_fetch_array($_result_colour)) {
				$_options .= '<option value="' . $_row_colour["col_colour"] . '" style="background-color: #' . $_row_colour["col_colour"] . '"';
				if ($_row_colour["col_colour"] == "$_value") {
					$_options .= ' selected="selected"';
				}
				$_options .= '>' . $_row_colour["col_title"] . '</option>' . "\n";
			}
			$_output .= '<select name="' . $_name . '" id="' . $_name . '"' . $_attributes . '>' . "\n" . $_options . '</select>' . "\n";
		} // date and time, with javascript calendar popup (http://www.mattkruse.com/javascript/calendarpopup/)
		// requires CalendarPopup.js and document.write(getCalendarStyles());var popcal = new CalendarPopup("popCalDiv");
		elseif ($_type == "datetime") {
			$_output .= '<input type="text" name="' . $_name . '" id="' . $_name . '" value="' . $_value . '"' . $_attributes . ' onClick="popcal' . $_name . '.select(document.forms[0].' . $_name . ',\'' . $_name . '\',\'dd/MM/yyyy\',\'' . $_value . '\'); return false;" />' . "\n";
			#$_output .= '<a href="javascript:void();" onClick="popcal.select(document.forms[0].'.$_name.',\'anchor\',\'dd/MM/yyyy\',\''.$_value.'\'); return false;" name="anchor" id="anchor"><img src="/images/sys/admin/icons/calendar.gif" width="16" height="16" border="0"></a>'."\n";
			$_output .= '<div id="popCalDiv' . $_name . '" style="z-index:1000;position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></div>' . "\n";
		} // time only, 2 selects
		elseif ($_type == "time") {

			$_split = explode(":", $_value);
			// show hours during working day
			for ($_h = 8; $_h <= 20;) {
				$_hours .= '  <option value="' . $_h . '"';
				if ($_h == $_split[0]) {
					$_hours .= ' selected="selected"';
				}
				$_hours .= '>' . str_pad($_h, 2, "0", STR_PAD_LEFT) . '</option>' . "\n";
				$_h++;
			}

			for ($_m = 0; $_m <= 55;) {
				$_minutes .= '  <option value="' . $_m . '"';
				if ($_m == round_to_nearest($_split[1])) {
					$_minutes .= ' selected="selected"';
				}
				$_minutes .= '>' . str_pad($_m, 2, "0", STR_PAD_LEFT) . '</option>' . "\n";
				$_m = $_m + 5;
			}

			$_output .= '<select name="' . $_name . '_hour" id="' . $_name . '_hour"' . $_attributes . '>' . "\n" . $_hours . '</select> : ';
			$_output .= '<select name="' . $_name . '_min" id="' . $_name . '_min"' . $_attributes . '>' . "\n" . $_minutes . '</select>' . "\n";
		} // html editor, using fckeditor
		elseif ($_type == "htmlarea") {
			require_once("inx/fckeditor/fckeditor.php");
			$htmlarea = new FCKeditor($_name, $_value, $_attr);
			$_output .= $htmlarea->CreateHtml();
		} // html editor, using fckeditor-2.6.3
		elseif ($_type == "htmlarea2") {
			require_once("inx/fckeditor-2.6.3/fckeditor_php5.php");
			$htmlarea = new FCKeditor($_name, $_value, $_attr);
			$_output .= $htmlarea->CreateHtml();
		} // special treatment for telephone numbers, allows infinite numbers, expects array of existing numbers
		// as this is a grouped set of fields, the function, requirede and various other vars do not work.
		elseif ($_type == "tel") {

			$count = 1;
			$rows  = count($_value);

			if (is_array($_value)) {
				foreach ($_value AS $key => $val) {

					$_tels .= $this->makeField('text', 'tel' . $count, 'Telephone ' . $count, $val['number']);
					$_tels .= $this->makeField('select', 'tel' . $count . 'type', '', $val['type'], '', db_enum("tel", "tel_type", "array")) . ' ';
					// make the first field look required. it will be validated if required is set in formData
					if ($count == 1) {
						$_tels = '<span class="required">' . "\n" . $_tels . '</span>';
					}
					if ($rows > 1) {
						// delete
						if ($rows > 1) {
							$_tels .= '<a href="tel.func.php?action=delete&tel_id=' . $val['id'] . '"><img src="/images/sys/admin/icons/cross_sm.gif" border="0" alt="Delete this Telephone Number" /></a>';
						} else {
							$_tels .= '<img src="/images/sys/admin/icons/cross_sm_grey.gif" border="0" alt="Delete this Telephone Number" />';
						}
						// move up
						if ($count == 1) {
							$_tels .= '<img src="/images/sys/admin/icons/arrow_up_sm_grey.gif" border="0" alt="I can\'t get no higher" />';
						} else {
							$_tels .= '<a href="tel.func.php?action=reorder&tel_id=' . $val['id'] . '&cur=' . $count . '&new=' . ($count - 1) . '"><img src="/images/sys/admin/icons/arrow_up_sm.gif" border="0" alt="Move Up" /></a>';
						}
						// move down
						if ($count == ($rows)) {
							$_tels .= '<img src="/images/sys/admin/icons/arrow_down_sm_grey.gif" border="0" alt="I\'m as low as I can go" />';
						} else {
							$_tels .= '<a href="tel.func.php?action=reorder&tel_id=' . $val['id'] . '&cur=' . $count . '&new=' . ($count + 1) . '"><img src="/images/sys/admin/icons/arrow_down_sm.gif" border="0" alt="Move Down" /></a>';
						}
					}

					$_output .= $this->addLabel('tel' . $count, 'Telephone ' . $count, $_tels);
					$_tels = '';
					$count++;
				}
			}
			// add blank for new $_type,$_name,$_label=NULL,$_value=NULL
			if (!is_array($_value)) {
				$_singlevalue = $_value;
			}
			$_tels .= $this->makeField('text', 'telnew', 'New', $_singlevalue);
			$_tels .= $this->makeField('select', 'telnewtype', '', '', '', db_enum("tel", "tel_type", "array")) . ' ';

			if ($_value) {
				$newtel_label = 'Add New &raquo;';
			} else {
				$newtel_label = 'Telephone';
			}

			$tel_output .= $this->addLabel('New', $newtel_label, $_tels);

			$_output .= $tel_output;
		}

		//echo $_name." = ".$this->required[$_name]."<br>";
		if ($this->requiredFields[$_name] == "2" || $this->requiredFields[$_name] == "3") {
			$_output = '<span class="required">' . "\n" . $_output . '</span>' . "\n";
		}
		return ($_output);
	}

	// add field to the output (no row, label etc, used for buttons etc)
	function addField($_type, $_name, $_label = null, $_value, $_attr = null, $_opt = null)
	{

		$_output = $this->makeField($_type, $_name, $_label, $_value, $_attr, $_opt);
		$this->output .= $_output;
	}

	// add a complete form row (div, label and field)
	function addRow($_type, $_name, $_label = null, $_value = null, $_attr = null, $_opt = null, $_tooltip = null)
	{

		$_field = $this->makeField($_type, $_name, $_label, $_value, $_attr, $_opt);
		if ($_tooltip) {
			$_field = $this->makeTooltip($_tooltip) . $_field;
		}
		$_output = $this->addLabel($_name, $_label, $_field);
		$this->output .= $_output;
	}

	// add any form item to the output, with label
	function addItem($_name, $_label, $_input)
	{

		$_output = $this->addLabel($_name, $_label, $_input);
		$this->output .= $_output;
	}

	// add any snip of code to the output
	function addHtml($_input)
	{

		$this->output .= $_input;
	}

	// add legend to fieldset
	function addLegend($_title, $_attr = null, $_href = null)
	{ //
		if (is_array($_attr)) {
			foreach ($_attr AS $_key => $_val) {
				$_attributes .= ' ' . $_key . '="' . $_val . '"';
			}
		}
		$_output = '<legend';
		//if ($_href) { $_output.=' onClick="'.$_href.'"'; }
		$_output .= $_attributes . '>';
		if ($_href) {
			$_title = '<a href="' . $_href . '" onfocus="if(this.blur)this.blur()">' . $_title . '</a>';
		}
		$_output .= $_title . '</legend>' . "\n";
		$this->output .= $_output;
	}

	function addFieldset()
	{

		$this->output .= '<fieldset>';
	}

	function addForm($_name = "form", $_method = "get", $_action = null, $_enctype = "application/x-www-form-urlencoded", $_js = null)
	{

		$this->output .= '<form name="' . $_name . '" id="' . $_name . '" method="' . $_method . '" action="' . $_action . '" enctype="' . $_enctype . '" ' . $_js . '>' . "\n";
	}

	// add label (and div) - label cocks up up selects (resets to default), so i have added an x to the label "for"
	// found js fix for above problem, but causes js error for non-select fields - 25/09/06
	function addLabel($_name, $_label, $_input, $_href = null)
	{

		if ($_href) {
			$_label_ref = '<a href="' . $_href . '" onfocus="if(this.blur)this.blur()">' . $_label . '</a>';
		} else {
			$_label_ref = $_label;
		}
		// this fixes the reset to default, but causes an error for <> select
		//  onmousedown="backup=this.form.elements[this.htmlFor].options.selectedIndex" onmouseup="this.form.elements[this.htmlFor].focus();this.form.elements[this.htmlFor].options.selectedIndex=backup"
		$_output = '<label for="' . $_name . 'x" class="formLabel">' . $_label_ref . '</label>' . "\n" . $_input;
		$_output = $this->addDiv($_output);
		return $_output;
	}

	// enclose data in div tag
	function addDiv($_input)
	{

		$_output = '<div>' . "\n" . $_input . "\n" . '</div>' . "\n";
		return $_output;
	}

	// javascript validation, unfinished
	function addJsValidation($_fields, $formName)
	{

		foreach ($_fields AS $field_name => $field_array) {
			if ($field_array['required'] == "2") {
				$this->requiredJavascript[$formName][$field_name] = '"' . $field_name . '","req","' . $field_array['label'] . ' is required"'; //array($field_name,"req",$field_array['label']." is required");
			}
		}
		return $this->requiredJavascript;
	}

	// return html formatted tooltip tag
	function makeTooltip($_tooltip)
	{

		// remove apostrophes to prevent js error
		$_tooltip = str_replace("'", "\'", $_tooltip);
		//return '<div id="tooltip"><a href="javascript:void(0);" onMouseover="showhint(\''.$_tooltip.'\', this, event, \'180px\')">[?]</a></div>';
		return '<div id="tooltip"><a href="javascript:void(0);" onMouseover="showhint(\'' . $_tooltip . '\', this, event, \'180px\')"><img src="/images/sys/admin/icons/help.gif" width="16" height="16" border="0"></a></div>';
	}

	// add ajax postcode lookup fields
	// requires ajax page (ajax_postcode.php), js functions and loading image (ajax_loader.gif)
	function ajaxPostcode($_lookup_type = "by_freetext", $_scope = "pro")
	{

		$_output .= $this->addHtml('<div id="lookup">');

		$_output .= $this->addField("hidden", "lookup_type", "", $_lookup_type);
		#$_type,$_name,$_label=NULL,$_value,$_attr=NULL,$_opt=NULL
		#$_output .= $this->addField("radio","lookup_type","",$_lookup_type,"",array('by_freetext'=>'by_freetext','by_postcode'=>'by_postcode'));
		$_output .= $this->addField("hidden", "scope", "", $_scope);

		if ($_lookup_type == "by_freetext") {
			$_output .= $this->addRow("text", "number", "Building Number", "", array('style' => 'width:320px;'));
			$_output .= $this->addRow("text", "street", "Street", "", array(
																		   'style' => 'width:320px;',
																		   'class' => 'required'
																	  ));
			$ajax_fields = '<span class="required">' . $this->makeField("text", "postcode", "", "", array('style' => 'width:100px;')) . '</span>';
			$ajax_fields .= $this->makeField("button", "getAddress", "", "Get Address", array(
																							 'class'   => 'button',
																							 'onClick' => 'javascript:ajax_lookup();'
																						));
			$ajax_fields .= '<div id="systemWorking" style="display:none" class="inline"><img src="/images/sys/admin/ajax-loader.gif" width="16" height="16" alt="Loading..." title="" /></div>';
			$_output .= $this->addHtml($this->addLabel("postcode", "Postcode", $ajax_fields));
		} elseif ($_lookup_type == "by_postcode") {
			$_output .= $this->addRow("text", "number", "Building Number", "", array('style' => 'width:320px;'));
			$ajax_fields = $this->makeField("text", "postcode", "", "", array('style' => 'width:100px;'));
			$ajax_fields .= $this->makeField("button", "getAddress", "", "Get Address", array(
																							 'class'   => 'button',
																							 'onClick' => 'javascript:ajax_lookup();'
																						));
			$ajax_fields .= '<div id="systemWorking" style="display:none" class="inline"><img src="/images/sys/admin/ajax-loader.gif" width="16" height="16" alt="Loading..." title="" /></div>';
			$_output .= $this->addHtml($this->addLabel("postcode", "Postcode", $ajax_fields));
		}

		$_output .= $this->addHtml('<div id="inset">To enter the address manually click <a href="javascript:ajax_manual();">here</a></div>');
		$_output .= $this->addHtml('</div>');

		# ajax results (hidden div)
		$_output .= $this->addHtml('<div id="placeholder" style="display:none">');
		$_output .= $this->addHtml('</div>');

		$this->output .= $_output;
	}

	// add a seperator <hr>
	function addSeperator()
	{

		$this->output .= '<hr>';
	}

	// add multi tel number fields
	function addTel($_data)
	{

		$count = 1;
		$rows  = count($_data);

		if ($rows) {
			foreach ($_data AS $key => $val) {

				$_tels .= $this->makeField('text', 'tel' . $count, 'Telephone ' . $count, $val['number']);
				$_tels .= $this->makeField('select', 'tel' . $count . 'type', '', $val['type'], '', db_enum("tel", "tel_type", "array")) . ' ';

				// delete
				if ($rows > 1) {
					$_tels .= '<a href="tel.func.php?action=delete&tel_id=' . $val['id'] . '"><img src="/images/sys/admin/icons/cross_sm.gif" border="0" alt="Delete this Telephone Number" /></a>';
				} else {
					$_tels .= '<img src="/images/sys/admin/icons/cross_sm_grey.gif" border="0" alt="Delete this Telephone Number" />';
				}
				// move up
				if ($count == 1) {
					$_tels .= '<img src="/images/sys/admin/icons/arrow_up_sm_grey.gif" border="0" alt="Move Up" />';
				} else {
					$_tels .= '<a href="tel.func.php?action=reorder&tel_id=' . $val['id'] . '&cur=' . $count . '&new=' . ($count - 1) . '"><img src="/images/sys/admin/icons/arrow_up_sm.gif" border="0" alt="Move Up" /></a>';
				}
				// move down
				if ($count == ($rows)) {
					$_tels .= '<img src="/images/sys/admin/icons/arrow_down_sm_grey.gif" border="0" alt="Move Down" />';
				} else {
					$_tels .= '<a href="tel.func.php?action=reorder&tel_id=' . $val['id'] . '&cur=' . $count . '&new=' . ($count + 1) . '"><img src="/images/sys/admin/icons/arrow_down_sm.gif" border="0" alt="Move Down" /></a>';
				}

				$_output .= $this->addLabel('tel' . $count, 'Telephone ' . $count, $_tels);
				$_tels = '';
				$count++;
			}
		}
		// add blank for new
		$_tels .= $this->makeField('text', 'telnew', 'New', '');
		$_tels .= $this->makeField('select', 'telnewtype', '', '', '', db_enum("tel", "tel_type", "array")) . ' ';

		if ($rows) {
			$newtel_label = 'New Telephone';
		} else {
			$newtel_label = 'Telephone';
		}

		$_output .= $this->addLabel('New', $newtel_label, $_tels);
		$this->output .= $_output;
	}

	// format duration (hours and minutes)
	function duration($_input, $_format = null)
	{

		$_hour   = floor($_input / 60);
		$_minute = floor($_input % 60);

		if ($_format == 'long') {
			$_hour_text   = ' hour';
			$_minute_text = ' minute';
		} elseif ($_format == 'short') {
			$_hour_text   = 'h';
			$_minute_text = 'm';
		} else {
			$_hour_text   = ' hr';
			$_minute_text = ' min';
		}

		if ($_hour > 0) {
			if ($_hour > 1 && $_format !== 'short') {
				$_hour_text .= 's ';
			}
			$_output = "$_hour$_hour_text";
		}
		if ($_minute > 0) {
			if ($_minute > 1 && $_format !== 'short') {
				$_minute_text .= 's ';
			}
			$_output .= " $_minute$_minute_text";
		}
		return trim($_output);
	}

	function renderForm()
	{

		return $this->output . "\n</form>";
	}
} // end of class

class Validate
{

	function process($_fields, $_querystring = null)
	{

		// check the input is an array
		if (!is_array($_fields)) {
			echo 'Fatal error: Validate - input is not an array';
			exit;
		}

		#print_r($_fields);
		// now compare the fields array against the $_querystring array and validate
		foreach ($_fields AS $field_name => $field_array) {

			// make sure init value is not used, or clear init is field if not required
			if ($_fields[$field_name]['init']) {
				if ($_querystring[$field_name] == $_fields[$field_name]['init']) {
					if ($_fields[$field_name]['required'] == 2 || $_fields[$field_name]['required'] == 4) {
						$errors[$field_name] = $_fields[$field_name]['label'] . " is required";
					}
					// clear, so defaults dont get added to db_data
					$_querystring[$field_name] = '';
				}
			}

			// email fields are not required, but if supplied must be valid
			if ($field_name == 'cli_email') {
				if ($_querystring[$field_name]) {
					if (!check_email($_querystring[$field_name])) {
						$errors[$field_name] = $_fields[$field_name]['label'] . " must be valid";
					}
				}
			}

			// we only process fields included in the GET/POST array	 (allow 0 values, but not empty)
			// if ($_querystring[$field_name] || $_querystring[$field_name] === "0") {

			// if a function is applied to the value, run the function before validation (added 12/06/07)
			if (function_exists($_fields[$field_name]['function'])) {
				$_querystring[$field_name] = call_user_func($_fields[$field_name]['function'], $_querystring[$field_name]);
			}

			// check for required fields
			if ($_fields[$field_name]['required'] == 2 || $_fields[$field_name]['required'] == 4) { // && !$_querystring[$field_name] || !$_querystring[$field_name] === "0"
				// if no label is given, use field name
				if (!$_fields[$field_name]['label']) {
					$_fields[$field_name]['label'] = $field_name;
				}
				if ($_querystring[$field_name] === "0") {
					// do nothing (allows 0 value)
				} elseif (count($_querystring[$field_name]) < 1) {

					$errors[$field_name] = $_fields[$field_name]['label'] . " is required";
				}
			}
			//else {
			if (isset($_querystring[$field_name])) {
				// convert any arrays to strings for database
				if (is_array($_querystring[$field_name])) {
					$_querystring[$field_name] = array2string($_querystring[$field_name]);
				}
				// get the format function title so we can run that function on the string, but only if that function exists
				if (function_exists($_fields[$field_name]['function'])) {
					$db_data[$field_name] = call_user_func($_fields[$field_name]['function'], $_querystring[$field_name]);
				} else {
					$db_data[$field_name] = trim($_querystring[$field_name]);
				}
			}

			//}

		}
		return array(
			'Errors'  => $errors,
			'Results' => $db_data
		);
	}

} // end of Validate class
?>
