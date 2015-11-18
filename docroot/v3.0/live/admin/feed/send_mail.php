<!--#!/usr/local/bin/php -q-->
<?php
$i=1;
do {
   mail('richard.smith@alaincharles.com',$i,$i);
   $i++;
   sleep(2);
} while ($i < 15)
?>
