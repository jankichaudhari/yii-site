<?php
include ('Archive/Zip.php');        // imports

$obj = new Archive_Zip('test.zip'); // name of zip file

$files = array('client2area.php',
               'edit_property.php',
               'find_property_orphan.php');   // files to store

if ($obj->create($files)) {
    echo 'Created successfully!';
} else {
    echo 'Error in file creation';
}
?>