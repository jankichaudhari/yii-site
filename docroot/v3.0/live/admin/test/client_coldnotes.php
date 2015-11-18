<?php

$string = 'a:7:{s:10:"Receptions";N;s:9:"Bathrooms";N;s:10:"AdminNotes";N;s:9:"UserNotes";s:101:"sout-west or south facing garden. Big rooms. No windows overlooking garden. Ideally cellar and garage";s:13:"SellingStatus";s:13:"Not Specified";s:12:"FurnishedLet";N;s:7:"TermLet";N;}';
$array = unserialize($string);
print_r($array);
?>