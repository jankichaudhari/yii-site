<?php

function navbar2($array)
{

	$defaults = array(
		'back'   => array('image' => 'back.gif', 'label' => 'Back', 'link' => 'javascript:history.back(1);', 'target' => '_self', 'title' => ''),
		'home'   => array('image' => 'home.gif', 'label' => 'Home', 'link' => GLOBAL_URL, 'target' => '_top', 'title' => 'Home'),
		//'refresh'=>array('image'=>'refresh.gif','label'=>'','link'=>$_SERVER['SCRIPT_NAME'].'?'.replaceQueryString($_SERVER['QUERY_STRING'],'msg'),'target'=>'_self','title'=>'Refresh this page'),
		'search' => array('image' => 'search.gif', 'label' => 'Search', 'link' => 'client_search.php', 'target' => '_self', 'title' => 'Search')
	);
	#<a href="/phprint.php">Print this page</a> window.print();
	/*,,
	'email'=>array('image'=>'email.gif','label'=>'Message','link'=>'#','target'=>'_self','title'=>'New Message')
	'print'=>array('image'=>'print.gif','label'=>'','link'=>'javascript:framePrint(\'mainFrame\');','title'=>'Print'),
	'print'=>array('image'=>'print.gif','label'=>'','link'=>'javascript:windowPrint();','title'=>'Print')*/

	$i = 0;
	foreach ($defaults AS $button => $attr) {
		if ($array[$button]['image']) {
			$image = '<img src="/images/sys/admin/navbar/' . $array[$button]['image'] . '" />';
		} else {
			$image = '<img src="/images/sys/admin/navbar/' . $attr['image'] . '" />';
		}
		if ($array[$button]['link']) {
			$link = replaceQueryString($array[$button]['link'], 'msg'); // remove msg from any links
		} else {
			$link = $attr['link'];
		}
		if ($array[$button]['title']) {
			$title = $array[$button]['title'];
		} else {
			$title = $attr['title'];
		}
		if ($array[$button]['label']) {
			$label = $array[$button]['label'];
		} else {
			$label = $attr['label'];
		}
		if ($array[$button]['target']) {
			$target = $array[$button]['target'];
		} else {
			$target = $attr['target'];
		}
		if ($link == "NONE") {
			$image = str_replace(".gif", "_off.gif", $image);
		}
		if ($link) {
			$label  = '<a href="' . $link . '" title="' . $title . '" target="' . $target . '" onfocus="this.blur()" id="navbar_' . str_replace(" ", "", $label) . '">' . $image . '' . $label . '</a>';
			$jsLink = ' onClick="trClick(\'' . $link . '\')"';
		} else {
			$label = $image . ' ' . $label;
		}
		$output .= '<li>' . $label . '</li>' . "\n";

		$i++;
		$link  = '';
		$label = '';
		$image = '';
		$title = '';

	}

	// optional buttons
	if ($array['print']) {

		if ($array['print']['image']) {
			$image = '<img src="/images/sys/admin/navbar/' . $array['print']['image'] . '">';
		} else {
			$image = '<img src="/images/sys/admin/navbar/print.gif">';
		}
		if ($array['print']['link']) {
			$link = replaceQueryString($array['print']['link'], 'msg'); // remove msg from any links
		} else {
			$link = 'javascript:windowPrint();';
		}
		if ($array['print']['title']) {
			$title = $array['print']['title'];
		} else {
			$title = $attr['title'];
		}
		if ($array['print']['label']) {
			$label = $array['print']['label'];
		} else {
			$label = $attr['label'];
		}
		if ($array['print']['target']) {
			$target = $array['print']['target'];
		} else {
			$target = $attr['target'];
		}
		if ($link) {
			$label  = '<a href="' . $link . '" title="' . $title . '" target="' . $target . '" onfocus="this.blur()">' . $image . '' . $label . '</a>';
			$jsLink = ' onClick="trClick(\'' . $link . '\')"';
		} else {
			$label = $image . ' ' . $label;
		}
		$output .= '<li>' . $label . '</li>' . "\n";
	}
	if ($array['printOld']) {

		if ($array['printOld']['image']) {
			$image = '<img src="/images/sys/admin/navbar/' . $array['printOld']['image'] . '">';
		} else {
			$image = '<img src="/images/sys/admin/navbar/print.gif">';
		}
		if ($array['printOld']['link']) {
			$link = replaceQueryString($array['printOld']['link'], 'msg'); // remove msg from any links
		} else {
			$link = 'javascript:windowprintOld();';
		}
		if ($array['printOld']['title']) {
			$title = $array['printOld']['title'];
		} else {
			$title = $attr['title'];
		}
		if ($array['printOld']['label']) {
			$label = $array['printOld']['label'];
		} else {
			$label = $attr['label'];
		}
		if ($array['printOld']['target']) {
			$target = $array['printOld']['target'];
		} else {
			$target = $attr['target'];
		}
		if ($link) {
			$label  = '<a href="' . $link . '" title="' . $title . '" target="' . $target . '" onfocus="this.blur()">' . $image . '' . $label . '</a>';
			$jsLink = ' onClick="trClick(\'' . $link . '\')"';
		} else {
			$label = $image . ' ' . $label;
		}
		$output .= '<li>' . $label . '</li>' . "\n";
	}

	// optional buttons
	if ($array['email']) {

		if ($array['email']['image']) {
			$image = '<img src="/images/sys/admin/navbar/' . $array['email']['image'] . '">';
		} else {
			$image = '<img src="/images/sys/admin/navbar/email.gif">';
		}
		if ($array['email']['link']) {
			$link = replaceQueryString($array['email']['link'], 'msg'); // remove msg from any links
		} else {
			$link = 'javascript:windowPrint();';
		}
		if ($array['email']['title']) {
			$title = $array['email']['title'];
		} else {
			$title = $attr['title'];
		}
		if ($array['email']['label']) {
			$label = $array['email']['label'];
		} else {
			$label = $attr['label'];
		}
		if ($array['email']['target']) {
			$target = $array['email']['target'];
		} else {
			$target = $attr['target'];
		}
		if ($link) {
			$label  = '<a href="' . $link . '" title="' . $title . '" target="' . $target . '" onfocus="this.blur()">' . $image . '' . $label . '</a>';
			$jsLink = ' onClick="trClick(\'' . $link . '\')"';
		} else {
			$label = $image . ' ' . $label;
		}
		$output .= '<li>' . $label . '</li>' . "\n";
	}

	// assistance
	//$output .= '<td onMouseOver="trOver(this)" onMouseOut="trOut(this)" onClick="trClick(\'bug.php?bug_page='.$_SERVER['SCRIPT_NAME'].'?'.urlencode($_SERVER['QUERY_STRING']).'\')"><a href="bug.php?bug_page='.$_SERVER['SCRIPT_NAME'].'?'.urlencode($_SERVER['QUERY_STRING']).'" title="Assistance" target="_self"><img src="/images/sys/admin/navbar/help.gif" border="0"> Assistance</a></td>'."\n";
	$output .= '<li><a href="bug.php?bug_page=' . $_SERVER['SCRIPT_NAME'] . '?' . urlencode($_SERVER['QUERY_STRING']) . '" title="Assistance" target="_self" onfocus="this.blur()"><img src="/images/sys/admin/navbar/help.gif">Assistance</a></li>' . "\n";

	// optional buttons
	if ($array['headline']) {

		if ($array['headline']['image']) {
			$image = '<img src="/images/sys/admin/navbar/' . $array['headline']['image'] . '">';
		} else {
			//$image = '<img src="/images/sys/admin/navbar/email.gif">';
		}
		if ($array['headline']['link']) {
			$link = replaceQueryString($array['headline']['link'], 'msg'); // remove msg from any links
		} else {
			$link = 'bug.php?bug_type=Feature+Request&bug_page=' . $_SERVER['SCRIPT_NAME'] . '?' . urlencode($_SERVER['QUERY_STRING']) . '';
		}
		if ($array['headline']['title']) {
			$title = $array['headline']['title'];
		} else {
			$title = '';
		}
		if ($array['headline']['label']) {
			$label = $array['headline']['label'];
		} else {
			$label = '';
		}
		if ($array['headline']['target']) {
			$target = $array['headline']['target'];
		} else {
			$target = $attr['target'];
		}
		if ($link) {
			$label  = '<a href="' . $link . '" title="' . $title . '" target="' . $target . '" onfocus="this.blur()">' . $image . '' . $label . '</a>';
			$jsLink = ' onClick="trClick(\'' . $link . '\')"';
		} else {
			$label = $image . ' ' . $label;
		}
		$output .= '<li class="red">' . $label . '</li>' . "\n";
	}

	$output = '<div id="navbar">
<ul>
' . $output . '</ul>
</div>

';
	return $output;
}

# build navbar
function navbar($options)
{

	if (is_array($options)) {
		if ($options['back']) {
			$back = urldecode($options['back']);
		} else {
			$back = 'javascript:history.go(-1);';
		}
		if ($options['forward']) {
			$forward = urldecode($options['forward']);
		} else {
			//javascript:window.history.forward();">
			$forward = 'javascript:window.history.forward();';
		}
		if ($options['home']) {
			$home = urldecode($options['home']);
		} else {
			$home = 'home.php';
		}
		if ($options['refresh']) {
			$refresh = urldecode($options['refresh']);
		} else {
			$refresh = 'javascript:history.go(0);';
		}
		if ($options['search']) {
			$search = urldecode($options['search']);
		} else {
			$search = 'client_search.php';
		}
		if ($options['email']) {
			$email = urldecode($options['email']);
		} else {
			$email = '#';
		}
		if ($options['print']) {
			$print = urldecode($options['print']);
		} else {
			$print = 'javascript:window.print();';
		}
	}

	$buttons = array(
		# name (alt and title) = > label
		'Back'    => 'Back',
		//'Forward'=>'',
		'Home'    => '',
		'Refresh' => '',
		'Search'  => 'Search',
		'Email'   => 'New Message',
		'Print'   => 'Print'
	);

	foreach ($buttons AS $button => $value) {
		$option = strtolower($button);
		if ($options[$option]) {
			$link = urldecode($options[$option]);
		} else {
			$link = $$option;
		}
		$output .= '<td onMouseOver="trOver(this)" onMouseOut="trOut(this)"><a href="' . $link . '" title="' . $button . '"><img src="img/navbar/' . $option . '.gif" border="0" width="21" height="21" alt="' . $button . '"> ' . $value . '</a></td>' . "\n";
	}

	return '
<table cellspacing="0" cellpadding="0">
<tr>
' . $output . '
</tr>
</table>';
}

function columnHeader($column_headers, $query_string)
{

	global $global_url;
	/*
	order by requires two variables, the field name and direction.
	to make querystring we ned to remove both varbailes from the given query string
	and replace them with new values.
	*/

# get existing values (orderby and direction) or set defaults
	parse_str($query_string, $qs);

// order by first column in array
	if (!$qs['orderby']) {
		$orderby = $column_headers[0]['column'];
	} else {
		$orderby = $qs['orderby'];
	}
	if (!$qs['direction']) {
		$direction = "ASC";
	} else {
		$direction = $qs['direction'];
	}
# remove values from querystring
	$query_string = replaceQueryString($query_string, '&orderby');
	$query_string = replaceQueryString($query_string, '&direction');

	/*
	if current direction is ASC, we need to show icon and link to DESC, and visa-versa
	only show the icons on the column we are sorting on
	*/
	foreach ($column_headers AS $column_title => $column_params) {
		# $column_params['title']
		# $column_params['column']
		# $column_params['direction']

		// default direction
		if (!$column_params['direction']) {
			$column_params['direction'] = "ASC";
		}

		if ($column_params['column']) {
			if ($direction == "ASC") {
				$column_direction = "DESC";
				$direction_image  = '<img src="/images/sys/admin/asc.gif" width="16" height="16" border="0" align="absmiddle">';
			} else {
				$column_direction = "ASC";
				$direction_image  = '<img src="/images/sys/admin/desc.gif" width="16" height="16" border="0" align="absmiddle">';
			}

			$title = '<a href="?' . $query_string . '&orderby=' . $column_params['column'] . '&direction=';
			if ($orderby == $column_params['column']) {
				$title .= $column_direction;
			} else {
				$title .= 'ASC';
			}
			$title .= '" title="Sort by ' . $column_params['title'] . '">' . $column_params['title'] . '</a>';

			// only show image on column we are sorting by
			if ($orderby == $column_params['column']) {
				$title .= $direction_image;
			}
			$column_params['title'] = $title;
		}
		if ($column_params['colspan']) {
			$colspan = ' colspan="' . $column_params['colspan'] . '"';
		}
		$columns .= '<th' . $colspan . '>' . $column_params['title'] . '</th>
	';
		$colspan         = '';
		$column_links    = '';
		$direction_image = '';
		$column_title    = '';
		$title           = '';
	}
	# end array loop

	return $columns;
}

# end of function

?>