<?php
/**
 * This is a template SMS message to be sent to vendors
 * @var $client      Client
 * @var $model       Appointment
 * @var $instruction Deal
 */
$address = array(
	$instruction->address->line1,
	$instruction->address->line2,
	$instruction->address->line3,
);
$address = implode(' ', array_filter($address));
ob_start();
?>
<?php if ($model->app_type === Appointment::TYPE_VALUATION): ?>
	A valuation has been booked in for your property <?php echo $address ?> on the <?php echo Date::formatDate("d/m/y \a\\t g:ia", $model->app_start) ?>.
<?php else: ?>
	A viewing has been booked in for your property <?php echo $address ?> on the <?php echo Date::formatDate("d/m/y \a\\t g:ia", $model->app_start) ?>.
<?php endif ?>
<?php echo trim(ob_get_clean()); // this is the most stupid workaround I could come up with. phpstorm adds intendation on formatting, so we need to trim output ?>

Wooster & Stock