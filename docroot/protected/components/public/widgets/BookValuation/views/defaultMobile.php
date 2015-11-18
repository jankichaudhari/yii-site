<?php
/**
 * @var  $department string
 * @var  $isMobile
 * @var  $cs         CClientScript
 */
$cs = Yii::app()->clientScript;
//if ($isMobile) {
$cs->registerScriptFile('/js/dateTimePicker/picker.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile('/js/dateTimePicker/picker.date.js', CClientScript::POS_BEGIN);
$cs->registerScriptFile('/js/dateTimePicker/picker.time.js', CClientScript::POS_BEGIN);
echo '<link type="text/css" href="/css/public/dateTimePicker/default.css" rel="stylesheet"/>';
echo '<link type="text/css" href="/css/public/dateTimePicker/default.date.css" rel="stylesheet"/>';
echo '<link type="text/css" href="/css/public/dateTimePicker/default.time.css" rel="stylesheet"/>';
//}
?>

<div class="top-widget-container wide book-valuation-widget">
	<div class="inner-padding">
		<div class="row-fluid">
			<div class="form-header">Arrange Valuation</div>
		</div>
		<form action="" method="post">
			<?php echo $this->errorMessage ? '<div class="message bold red">' . $this->errorMessage . '</div>' : '' ?>
			<?php echo $this->successMessage ? '<div class="message green bold">' . $this->successMessage . '</div>' : "" ?>
			<input type="hidden" name="<?php echo $this->name ?>[department]" value="<?= $department ?>"
				   id="<?= $this->name ?>_department_<?= $department ?>">


			<div class="row">
				<div class="half-cell">
					<label class="block-label">Preferred Date</label>
					<div class="input-wrapper">
						<input type="text" name="bookValuation[date]" placeholder="Select Date" class="" id="bookValuation_date"/>
					</div>
				</div>
				<div class="half-cell">
					<label class="block-label">Preferred Time</label>
					<div class="input-wrapper">
						<input type="text" name="bookValuation[time]" placeholder="Select Time" class="" id="bookValuation_time"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="cell">
					<label class="block-label">Type of property</label>
					<div class="input-wrapper">
						<select name="<?php echo $this->name ?>[type]">
							<option value="house">House</option>
							<option value="apartment">Apartment</option>
							<option value="other">Other</option>
						</select>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="cell">
					<label class="block-label">Name*</label>
					<div class="input-wrapper">
						<input id="<?php echo $this->name ?>_name" type="text" name="<?php echo $this->name ?>[name]"
							   class="text"
							   value="<?php echo(isset($_POST[$this->name]['name']) ? $_POST[$this->name]['name'] : "") ?>"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="cell">
					<label class="block-label">Email*</label>
					<div class="input-wrapper">
						<input id="<?php echo $this->name ?>_email" type="text" name="<?php echo $this->name ?>[email]"
							   class="text"
							   value="<?php echo(isset($_POST[$this->name]['email']) ? $_POST[$this->name]['email'] : "") ?>"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="cell">
					<label class="block-label">Telephone number</label>
					<div class="input-wrapper">
						<input id="<?php echo $this->name ?>_phone" type="text" name="<?php echo $this->name ?>[tel]"
							   class="text"
							   value="<?php echo(isset($_POST[$this->name]['tel']) ? $_POST[$this->name]['tel'] : "") ?>"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="cell">
					<label class="block-label">Full address*</label>

					<div class="input-wrapper">
						<textarea id="<?php echo $this->name ?>_address"
								  name="<?php echo $this->name ?>[address]"><?php echo(isset($_POST[$this->name]['address']) ? $_POST[$this->name]['address'] : "") ?></textarea>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="cell right">
					<div class="input-wrapper">
						<input type="submit" value="SEND" name="<?php echo $this->name ?>[send]" class="btn full-width"/>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	(function () {
		var options = {
//			editable: true,
			selectYears: true,
			selectMonths: true,
			firstDay: 1,
			min: new Date()
		};
		$('#bookValuation_date').pickadate(options);
		$('#bookValuation_time').pickatime(options)
	})();
</script>