<?php
/*
daily portal datafeeds
run nightly by cron and 23:00
*/
// Update the MousePrice Feed
exec("rm -rf " . __DIR__ . '/rightmove/*');
exec("rm -rf " . __DIR__ . '/mouseprice/*');
exec("rm -rf " . __DIR__ . '/nethouseprices/*');
exec("rm -rf " . __DIR__ . '/findaproperty/*');
exec("rm -rf " . __DIR__ . '/globrix/*');
exec("rm -rf " . __DIR__ . '/guildfeed/*');
exec("rm -rf " . __DIR__ . '/allagents/*');
exec("rm -rf " . __DIR__ . '/lettingsearch/*');
exec("rm -rf " . __DIR__ . '/zoomf/*');
exec("rm -rf " . __DIR__ . '/zoopla/*');
exec("rm -rf " . __DIR__ . '/thinkproperty/*');
exec("rm -rf " . __DIR__ . '/homeflow/*');
exec("rm -rf " . __DIR__ . '/needproperty/*');
exec("rm -rf " . __DIR__ . '/propertyplace/*');


exec("php " . dirname(__FILE__) . "/loot.php > /dev/null &"); //@vit complete
sleep(60);
exec("php " . dirname(__FILE__) . "/mouseprice.php > /dev/null &"); //@vit complete
sleep(60);
// Updates the Home Flow Feed

// Updates the feed for Globrix
exec("php " . dirname(__FILE__) . "/globrix.php > /dev/null &"); //@vit complete
sleep(60);
//  updates findaproperty and zoomf
exec("php " . dirname(__FILE__) . "/rightmove.php > /dev/null &");
sleep(60);
// rightmove_2 is rightmove sales and lettingsc
//exec("php " . dirname(__FILE__) . "/zoomf.php > /dev/null &");
//sleep(60);
//exec("php " . dirname(__FILE__) . "/thinkproperty.php > /dev/null &");
//sleep(60);
//exec("php " . dirname(__FILE__) . "/propertyfinder.php > /dev/null &");//@vit complete
//sleep(60);
exec("php " . dirname(__FILE__) . "/lettingsearch.php > /dev/null &"); //@vit complete
sleep(60);
exec("php " . dirname(__FILE__) . "/nethouseprices.php > /dev/null &"); //@vit complete
sleep(60);
//exec("php " . dirname(__FILE__) . "/ezylet_cam.php > /dev/null &");
//sleep(60);
//exec("php " . dirname(__FILE__) . "/ezylet_syd.php > /dev/null &");
//sleep(60);
//exec("php " . dirname(__FILE__) . "/oodle.php > /dev/null &"); // @vit compelte
//sleep(60);
exec("php " . dirname(__FILE__) . "/trovit.php > /dev/null &"); //@vit complete
sleep(120);
//exec("php " . dirname(__FILE__) . "/fish4.php > /dev/null &");
//sleep(60);
exec("php " . dirname(__FILE__) . "/findaproperty.php > /dev/null &");
sleep(60);
//exec("php " . dirname(__FILE__) . "/online-lettings.php > /dev/null &"); //@vit complete
//sleep(60);
exec("php " . dirname(__FILE__) . "/zoopla.php > /dev/null &"); //@vit complete
sleep(60);

exec("php " . dirname(__FILE__) . "/guildfeed.php > /dev/null &"); //@vit complete
sleep(60);
exec("php " . dirname(__FILE__) . "/propertyplace.php > /dev/null &"); //@vit complete
sleep(60);
exec("php " . dirname(__FILE__) . "/needproperty.php > /dev/null &"); //@vit complete
sleep(60);
//exec("php " . dirname(__FILE__) . "/homeflow.php > /dev/null &");//@vit complete
//sleep(60);
