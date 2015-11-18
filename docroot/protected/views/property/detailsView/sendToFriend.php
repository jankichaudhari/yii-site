<?php
/**
 * @var $this  SiteController
 * @var $model Deal
 * @var $message
 * @var $errorMessage
 */
$price = Locale::formatPrice($model->dea_marketprice, $model->dea_type == 'Sales' ? false : true);
if ($model->dea_type == 'Lettings') {
	$price .= ' - ' . Locale::formatPrice($model->getPrice('pcm'), true, true);
}
$fullUrl = 'http://' . Yii::app()->request->getServerName() . '/details/' . $model->dea_id;
$this->pageTitle = "Send to Friend";
?>

<div class="send-to-friend">
<?php if (isset($message) && $message) : ?>
	<div class="row-fluid sent-message">
		<?php echo $message; ?>
	</div>
<?php else : ?>
	<div class="top-widget-container wide">
		<div class="inner-padding">

			<div class="row-fluid">
				<div class="form-header">
					Send To Friend
				</div>
			</div>

			<div class="row">
				<div class="cell information">
					<p>I'm visiting the Wooster and Stock Web Site and I thought this property might be of interest to
						you:</p>

					<p><?php echo $model->dea_strapline ?></p>

					<p class="property-address"><?php echo $model->property->getShortAddressString(', ') . ' - ' . $price ?></p>

					<p><a href="<?php echo $fullUrl ?>" class="property-link"><?php echo $fullUrl ?></a></p>
				</div>
			</div>

			<?php echo CHtml::beginForm('', 'post', ['id' => 'send-to-friend-form']) ?>

			<?php if (isset($errorMessage) && $errorMessage) { ?>
				<div class="row">
					<div class="cell error-message">
						<?php echo $errorMessage; ?>
					</div>
				</div>
			<?php } ?>

			<div class="row">
				<div class="cell">
					<label class="block-label"><?php echo CHtml::label('Your Email Address(optional)', 'SendToFriend[email]', ['class' => 'bold']); ?></label>

					<div class="input-wrapper">
						<?php echo CHtml::textField('SendToFriend[email]', '', ['class' => 'email', 'size' => '60']); ?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="cell">
					<label class="block-label"><?php echo CHtml::label('Your Friend\'s Email Address', 'SendToFriend[friendEmail]', ['class' => 'bold']); ?></label>

					<div class="input-wrapper">
						<?php echo CHtml::textField('SendToFriend[friendEmail]', '', [
								'class' => 'email', 'size' => '60'
						]); ?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="cell">
					<label class="block-label"><?php echo CHtml::label('Optional Comment', 'SendToFriend[comment]', ['class' => 'bold']); ?></label>

					<div class="input-wrapper">
						<?php echo CHtml::textArea('SendToFriend[comment]', '', [
								'class' => 'comment', 'cols' => '44', 'rows' => '3'
						]); ?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="cell">
					<input type="submit" value="SEND" name="SendToFriend[send]" class="btn half-width"/>
				</div>
			</div>


			<?php echo CHtml::endForm(); ?>

		</div>
	</div>
	</div>
<?php endif; ?>