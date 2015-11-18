<?php 

// loop 20 times
for ($_i = 0; $_i <= 20; $_i++) {
	
// Get arguments from the argv array 
$arg1 = $_SERVER['argv'][1]; 
$arg2 = $_SERVER['argv'][2]; 

// Build message 
$message = "Hi Dude,\n\n"; 
$message .= "This is the first argument that you have passed to the script $arg1 "; 
$message .= "and this is the second argument: $arg2\n\n"; 
$message .= "This is pretty cool huh?\n\n"; 
$message .= "Congrats, you have now forked PHP!\n"; 

// Send email 
mail("markdw@hotmail.com", "Forking Results $_i", $message, "From: PHP<php@localhost.com>\n"); 

}
?>  
