<?php
/**
 * @var $model User[]
 * @var $dealStatus
 */
?>

<?php
$dealStatuses = array_combine($t = [
	Deal::STATUS_COMPLETED,
	Deal::STATUS_AVAILABLE,
	Deal::STATUS_UNDER_OFFER,
	Deal::STATUS_PRODUCTION,
	Deal::STATUS_PROOFING,
	Deal::STATUS_EXCHANGED,
	Deal::STATUS_WITHDRAWN,
], $t);
foreach ($dealStatuses as $dealStatus) {
	?>
	<div class="control-group">
		<label class="control-label"
			   for="<?php echo 'User_emailAlertForDealStatus_' . $dealStatus ?>"><?php echo $dealStatus ?></label>

		<div class="controls">
			<?php echo CHtml::checkBox('User[emailAlertForDealStatus][' . $dealStatus . ']', $model->userEmailAlertForDealStatus($dealStatus)); ?>
		</div>

	</div>
<?php
}
?>