<?php
// ##### Format String as Phone Number
    Function formatPH($ph)
          {
           $ph = ereg_replace ('[^0-9]+', '', $ph); // ##### Strip all Non-Numeric Characters
           $phlen = strlen($ph);
           switch (TRUE)
             {
              case ($phlen < 7):
                $ext = $ph;
                break;
              case ($phlen == 7):
                sscanf($ph, "%3s%4s", $pfx, $exc);
                break;
              case ($phlen > 7 AND $phlen < 10):
                sscanf($ph, "%3s%4s%s", $pfx, $exc, $ext);
                break;
              case ($phlen == 10):
                sscanf($ph, "%3s%3s%4s", $area, $pfx, $exc);
                break;
              case ($phlen == 11):
                sscanf($ph, "%1s%3s%3s%4s", $cty, $area, $pfx, $exc);
                break;
              case ($phlen > 11):
                sscanf($ph, "%1s%3s%3s%4s%s", $cty, $area, $pfx, $exc, $ext);
                break;
             }
           $out = '';
           $out .= isset($cty) ? $cty.' ' : '';
           $out .= isset($area) ? '('.$area.') ' : '';
           $out .= isset($pfx) ? $pfx.' - ' : '';
           $out .= isset($exc) ? $exc.' ' : '';
           $out .= isset($ext) ? 'x'.$ext : '';
           return $out;
          }

function formatPhone($phone) {
       if (empty($phone)) return "";
       if (strlen($phone) == 7)
               sscanf($phone, "%3s%4s", $prefix, $exchange);
       else if (strlen($phone) == 10)
               sscanf($phone, "%3s%3s%4s", $area, $prefix, $exchange);
       else if (strlen($phone) > 10)
               if(substr($phone,0,1)=='1') {
                                 sscanf($phone, "%1s%3s%3s%4s", $country, $area, $prefix, $exchange);
                             }
                             else{
                                 sscanf($phone, "%3s%3s%4s%s", $area, $prefix, $exchange, $extension);
                                }
       else
               return "unknown phone format: $phone";
       $out = "";
       $out .= isset($country) ? $country.' ' : '';
       $out .= isset($area) ? '(' . $area . ') ' : '';
       $out .= $prefix . '-' . $exchange;
       $out .= isset($extension) ? ' x' . $extension : '';
       return $out;
} 

echo formatPhone('07834 950580');
echo "<br>";
echo formatPH('07834 950580');
?>