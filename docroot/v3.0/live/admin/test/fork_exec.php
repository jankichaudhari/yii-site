<?php 
// execute a php script using cli, requiring no hanging around by the user.
// intended use = mailing list

$ip = $_SERVER['REMOTE_ADDR']; 
exec("/usr/local/bin/php /home/woosterstock/htdocs/v3.0/live/admin/test/fork_test.php Value1 $ip > /dev/null &"); 
exit;


$dea_id = 75;
$subject = 'A new property has been added';


// send emails, works
exec("/usr/local/bin/php /home/woosterstock/htdocs/v3.0/test/admin/fork_test.php"); 

mail("markdw@hotmail.com", "Forking Exec Errors", print_r($output,true), "From: PHP<php@localhost.com>\n"); 

echo "<center>Hmm, maybe it forked, maybe it didn't get forked! Check your email to see!</center>"; 
?>