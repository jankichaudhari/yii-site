<?php
/**
 * @var Branch[] $branches
 * @var          $this  BookAViewing
 * @var          $model Deal
 */
/** @var $branch Branch */
$branch = Branch::model()->findByPk($model->dea_branch);
$property = new PropertyController($model->dea_id);
$propertyStatus = $property->getStatusString($model->dea_status, $model->dea_type);
$name = isset($_POST['contact']['name']) ? $_POST['contact']['name'] : "";
$email = isset($_POST['contact']['email']) ? $_POST['contact']['email'] : "";
$phoneNumber = isset($_POST['contact']['tel']) ? $_POST['contact']['tel'] : "";
$message = isset($_POST['contact']['message']) ? $_POST['contact']['message'] : "";
?>

<div class="top-widget-container narrow book-viewing-widget">
	<div class="inner-padding">
		<div class="row-fluid">
			<div class="form-header">Arrange Viewing</div>
			<div class="branch-name"><?php echo $branch->bra_title ?></div>
			<div class="branch-telephone"><?php echo Locale::formatPhone($branch->bra_tel) ?></div>
		</div>
		<?php if ($propertyStatus && ($propertyStatus == 'Under Offer')): ?>
			<div class="row-fluid property-under-offer">
				This property is currently <?php echo $propertyStatus ?>
			</div>
		<?php endif; ?>
		<?php if ($model->dea_status != 'Available') : ?>
			<div class="row-fluid property-not-available">
				It is not possible to view this property but if you fill in the form below we will let you know if this property becomes available again.
			</div>
		<?php endif ?>

		<form action="" method="post">

			<?php echo $this->errorMessage ? '<div class="message error">' . $this->errorMessage . '</div>' : '' ?>
			<?php echo $this->successMessage ? '<div class="message success">' . $this->successMessage . '</div>' : "" ?>

			<div class="row">
				<div class="cell">
					<label for="<?php echo $this->name ?>_name" class="block-label">Name*</label>

					<div class="input-wrapper">
						<input id="<?php echo $this->name ?>_name" type="text" name="<?php echo $this->name ?>[name]" class="text" value="<?php echo $name ?>"/>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="cell">
					<label for="<?php echo $this->name ?>_email" class="block-label">Email*</label>

					<div class="input-wrapper">
						<input id="<?php echo $this->name ?>_email" type="text" name="<?php echo $this->name ?>[email]" class="text" value="<?php echo $email ?>"/>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="cell">
					<label for="<?php echo $this->name ?>_phone" class="block-label">Telephone number</label>

					<div class="input-wrapper">
						<input id="<?php echo $this->name ?>_phone" type="text" name="<?php echo $this->name ?>[tel]" class="text" value="<?php echo $phoneNumber ?>"/>
					</div>
				</div>
			</div>
			<?php if (strtolower($model->dea_status) == 'available'): ?>
				<div class="row">
					<div class="cell">
						<label for="<?php echo $this->name ?>_message" class="block-label">Preferred Date and Time</label>

						<div class="input-wrapper">
							<textarea id="<?php echo $this->name ?>_message" name="<?php echo $this->name ?>[message]"><?php echo $message ?></textarea>
						</div>
					</div>
				</div>
			<?php endif ?>
			<div class="row">
				<div class="cell right">
					<input type="submit" value="SEND" name="<?php echo $this->name ?>[send]" class="btn half-width"/>
				</div>
			</div>
		</form>
	</div>
</div>