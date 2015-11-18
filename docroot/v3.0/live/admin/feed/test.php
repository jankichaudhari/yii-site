<?php


function XMLEntities($string)
    {
        $string = preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '_privateXMLEntities("$0")', $string);
        return $string;
    }

    function _privateXMLEntities($num)
    {
    $chars = array(
        128 => '&#8364;',
        130 => '&#8218;',
        131 => '&#402;',
        132 => '&#8222;',
        133 => '&#8230;',
        134 => '&#8224;',
        135 => '&#8225;',
        136 => '&#710;',
        137 => '&#8240;',
        138 => '&#352;',
        139 => '&#8249;',
        140 => '&#338;',
        142 => '&#381;',
        145 => '&#8216;',
        146 => '&#8217;',
        147 => '&#8220;',
        148 => '&#8221;',
        149 => '&#8226;',
        150 => '&#8211;',
        151 => '&#8212;',
        152 => '&#732;',
        153 => '&#8482;',
        154 => '&#353;',
        155 => '&#8250;',
        156 => '&#339;',
        158 => '&#382;',
        159 => '&#376;');
        $num = ord($num);
        return (($num > 127 && $num < 160) ? $chars[$num] : "&#".$num.";" );
    } 
	
	
	$string = '<p>&Eacute; Having recently undergone a precise and positive refurbishment, this two double bedroom apartment has an immaculate interior, lots of living space and still retains some original features. It sits above commercial premises on Streatham High Road - famous for its abundant shopping parade and buzzing transport options.</p><p>Gain entry by walking along a cleverly hidden passage on Norfolk House Road (a residential side street running of Streatham High Road). You enter at the rear of the building, where a communal iron staircase will lead you up to the first floor. Nip through the communal hallway and up to the second floor to your flat. Gorgeous wooden floors run underfoot and continue into the bedrooms and the reception. Your first stop toward the front of the property is the reception room; it&#039;s a good size with two sash windows, a feature chimney breast and those signature brilliant-white walls. A swanky bathroom sits just next door. It has a pristine white suite including shower attachment, frosted sash window and attractive wall tiles up to picture rail level. Both bedrooms are good size doubles and the separate kitchen is large enough to fulfill your culinary desires. Find an integrated oven, four ring has hob, masses of wooden cabinets and black work surfaces too.</p><p>Take care of the commute at nearby Streatham Hill station (Victoria) which is within a 10 minute walk from your door. If you&#039;d rather travel into London Bridge, then walk a little further in the other direction and use Streatham station. Buses aplenty whizz along Streatham High Street, for around-the-clock trips into town. Believed to be the longest High Street in the country, you couldn&#039;t possibly be bored! There&#039;s a cinema, bowling centre and ice rink plus many good places to eat. Neighbouring Brixton and Crystal Palace offer many more nightspots, restaurants and bars. Take the Sunday papers to beautiful Tooting Bec Common, a short stroll away.</p>';
	
	echo XMLEntities($string);
	?>