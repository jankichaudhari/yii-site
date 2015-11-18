<?php /**
 * @var $department string
 * @var $isMobile
 */
?>

<div class="top-widget-container wide">
	<div class="inner-padding">
		<div class="row-fluid" style="margin-bottom: 8px">
			<div class="form-header">Arrange Valuation</div>
		</div>
		<form action="" method="post">
			<?php echo $this->errorMessage ? '<div class="message bold red">' . $this->errorMessage . '</div>' : '' ?>
			<?php echo $this->successMessage ? '<div class="message green bold">' . $this->successMessage . '</div>' : "" ?>
			<input type="hidden" name="<?php echo $this->name ?>[department]" value="<?= $department ?>"
				   id="<?= $this->name ?>_department_<?= $department ?>">

			<div class="row">
				<div class="half-cell">
					<label class="block-label">Type of property</label>

					<div class="input-wrapper">
						<label>
							<input type="radio" name="<?php echo $this->name ?>[type]" value="house"
								   id="<?php echo $this->name ?>_type_house" checked="checked" class="no-margin">
							<label class="radio-label right-margin"
								   for="<?php echo $this->name ?>_type_house">House</label>
						</label>
						<label>
							<input type="radio" name="<?php echo $this->name ?>[type]" value="apartment"
								   id="<?php echo $this->name ?>_type_house">
							<label class="radio-label right-margin" for="<?php echo $this->name ?>_type_house">Apartment</label>
						</label>
						<label>
							<input type="radio" name="<?php echo $this->name ?>[type]" value="other"
								   id="<?php echo $this->name ?>_type_other">
							<label class="radio-label no-margin"
								   for="<?php echo $this->name ?>_type_other">Other</label>
						</label>
					</div>
				</div>
				<div class="half-cell">
					<label class="block-label">Name*</label>

					<div class="input-wrapper">
						<input id="<?php echo $this->name ?>_name" type="text" name="<?php echo $this->name ?>[name]"
							   class="text"
							   value="<?php echo(isset($_POST[$this->name]['name']) ? $_POST[$this->name]['name'] : "") ?>"/>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="half-cell">
					<label class="block-label">Email*</label>

					<div class="input-wrapper">
						<input id="<?php echo $this->name ?>_email" type="text" name="<?php echo $this->name ?>[email]"
							   class="text"
							   value="<?php echo(isset($_POST[$this->name]['email']) ? $_POST[$this->name]['email'] : "") ?>"/>
					</div>
				</div>
				<div class="half-cell">
					<label class="block-label">Telephone number</label>

					<div class="input-wrapper">
						<input id="<?php echo $this->name ?>_phone" type="text" name="<?php echo $this->name ?>[tel]"
							   class="text"
							   value="<?php echo(isset($_POST[$this->name]['tel']) ? $_POST[$this->name]['tel'] : "") ?>"/>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="half-cell">
					<label class="block-label">Preferred Date/Time</label>

					<div class="input-wrapper">
						<textarea id="<?php echo $this->name ?>_date"
								  name="<?php echo $this->name ?>[date]"><?php echo(isset($_POST[$this->name]['date']) ? $_POST[$this->name]['date'] : "") ?></textarea>
					</div>
				</div>
				<div class="half-cell">
					<label class="block-label">Full address*</label>

					<div class="input-wrapper">
						<textarea id="<?php echo $this->name ?>_address"
								  name="<?php echo $this->name ?>[address]"><?php echo(isset($_POST[$this->name]['address']) ? $_POST[$this->name]['address'] : "") ?></textarea>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="cell right">
					<input type="submit" value="SEND" name="<?php echo $this->name ?>[send]" class="btn half-width"/>
				</div>
			</div>
		</form>
	</div>
</div>