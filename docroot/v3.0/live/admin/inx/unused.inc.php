<?php

// unused functions, depreciated etc


// create drop down for sales price range
// $_type = min or max
// $_field is the title of the drop-down, which relates to the field name (usually cli_salemin)
function price_sale($_type,$_field,$_pick="0") {
	if ($_type == "min") {
		$_default = '<option value="">Minimum</option>';
		//$_js = ' onChange="minp(this.form,0);"';
		} elseif ($_type == "max") {
		$_default = '<option value="">Maximum</option>';
		//$_js = ' onChange="maxp(this.form,0);"';
		}
	$_render = '
	<select name="'.$_field.'"'.$_js.'>	
    '.$_default.'
	';
	for ($i = 80000; $i <= 500000;) { 
		$_render .= '<option value="'.$i.'"';
		if ($i == $_pick) {
			$_render .= ' selected';
			}
		$_render .= '>'.format_price($i).'</option>
		'; 
		$i = $i+5000;
		}
	for ($i = 510000; $i <= 990000;) { 
		$_render .= '<option value="'.$i.'"';
		if ($i == $_pick) {
			$_render .= ' selected';
			}
		$_render .= '>'.format_price($i).'</option>
		'; 
		$i = $i+10000;
		}
	for ($i = 1000000; $i <= 3000000;) { 
		$_render .= '<option value="'.$i.'"';
		if ($i == $_pick) {
			$_render .= ' selected';
			}
		$_render .= '>'.format_price($i).'</option>
		'; 
		$i = $i+1000000;
		}		
		$_render .= '</select>
		';
	return $_render;
	}
	

// create drop down for lettings price range
// $_type = min or max
// $_field is the title of the drop-down, which relates to the field name 
// $_term = pm or pcm
function price_let($_type,$_field,$_pick="0",$_term="pw") {
	if ($_type == "min") {
		$_default = '<option value="">Minimum</option>';
		} elseif ($_type == "max") {
		$_default = '<option value="">Maximum</option>';
		}
	$_render = '
	<select name="'.$_field.'">	
    '.$_default.'
	';
	for ($i = 100; $i <= 1000;) { 
		$_render .= '<option value="'.$i.'"';
		if ($i == $_pick) {
			$_render .= ' selected';
			}
		if ($_term == "pcm") {
			$_render .= '>'.format_price(pw2pcm($i)).'</option>
			'; 
			} else {
			$_render .= '>'.format_price($i).'</option>
			'; 
			}
		$i = $i+50;
		}
	for ($i = 1000; $i <= 5000;) { 
		$_render .= '<option value="'.$i.'"';
		if ($i == $_pick) {
			$_render .= ' selected';
			}
		if ($_term == "pcm") {
			$_render .= '>'.format_price(pw2pcm($i)).'</option>
			'; 
			} else {
			$_render .= '>'.format_price($i).'</option>
			'; 
			}
		$i = $i+250;
		}		
		$_render .= '</select>
		';
	return $_render;
	}


?>