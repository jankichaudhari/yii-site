<?php

class fish
{
    function swim($speed)
    {
        // critical error, fish could explode if faster
        if($speed>120) throw new Exception('swim limit reached.');
    }
}


// new fish
$turbofish = new fish;

// try to speedup
try
{
    // try to be a turbo-fish
    $turbofish->swim(121);
}
catch (Exception $e)
{
	// go deeper, to gain speed
    echo "Exception caught!\n";

}

?>