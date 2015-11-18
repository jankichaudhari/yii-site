<?php
/**
 * This is a template SMS message to be sent to clients
 * @var        $client Client
 * @var        $model  Appointment
 * @var Deal[] $instructions
 */
$instructions = $model->instructions;
$instruction = array_pop($instructions);

$address = array(
	$instruction->address->line1,
	$instruction->address->line2,
	$instruction->address->line3,
	$instruction->address->postcode,

);
$address = implode(' ', array_filter($address));

$i = count($instructions);
$viewings = $i ? 'viewings are' : 'viewing is';
?>
Your <?php echo $viewings ?> booked in for <?php echo Date::formatDate("d/m/Y \a\\t g:ia", $model->app_start) ?>. Please meet at <?php echo $address ?>.
Wooster & Stock

