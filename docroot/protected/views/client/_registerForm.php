<?php
/**
 * @var $this  SiteController
 * @var $model PublicClientRegisterForm
 * @var $form  CActiveForm  @var $sent
 */
$minPrices = array_merge(range(75000, 150000, 25000), range(200000, 500000, 50000), range(600000, 1000000, 100 * 1000));
$minPrices = array_combine($minPrices, Locale::formatMoneyArray($minPrices));
$maxPrices = array_merge([125000, 150000], range(200000, 500000, 50000), range(600000, 1000000, 100 * 1000), [2000000, 3000000]);
$maxPrices = array_combine($maxPrices, Locale::formatMoneyArray($maxPrices));
?>


<div class="top-widget-container wide register-form-block">
	<div class="inner-padding">

		<div class="row-fluid">
			<div class="form-header no-margin">
				Register With Wooster & Stock
			</div>
		</div>


		<?php $form = $this->beginWidget('CActiveForm', array('id' => 'register-form')) ?>

		<?php if ($result['type'] == 'success') : ?>
			<div class="message success">
				<?php echo $result['html']; ?>
			</div>
		<?php endif; ?>

		<?php if ($result['type'] == 'error'): ?>
			<div class="message error">
				<?php echo $result['html']; ?>
			</div>
		<?php endif; ?>

		<div class="row">
			<div class="half-cell">
				<label class="block-label">Name</label>

				<div class="input-wrapper">
					<?php echo $form->textField($model, 'name', ['class' => 'name', 'placeholder' => 'First Name(s)']) ?>
				</div>
			</div>
			<div class="half-cell">
				<label class="block-label">Surname</label>

				<div class="input-wrapper">
					<?php echo $form->textField($model, 'surname', ['class' => 'surname', 'placeholder' => 'Surname']) ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="half-cell">
				<label class="block-label">Telephone</label>

				<div class="input-wrapper">
					<?php echo $form->textField($model, 'telephone') ?>
				</div>
			</div>
			<div class="half-cell">
				<label class="block-label">Email</label>

				<div class="input-wrapper">
					<?php echo $form->textField($model, 'email') ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="half-cell register_address">
				<label class="block-label">Address</label>

				<div class="input-wrapper">
					<?php echo $form->textArea($model, 'address') ?>
				</div>
			</div>
			<div class="small-cell margin-bottom">
				<label class="block-label">Bedrooms</label>

				<div class="input-wrapper">
					<?php echo $form->dropDownList($model, 'bedrooms', ['Studio', 1, 2, 3, 4, 5, 6], ['empty' => 'Min']); ?>
				</div>
			</div>
			<div class="small-cell margin-bottom">
				<label class="block-label">Branch</label>

				<div class="input-wrapper">
					<?php
					echo $form->dropDownList($model, 'branch',
											 CHtml::listData(Branch::model()->registerClients()->findAll(), 'bra_id', 'office.shortTitle'),
											 ['class' => '', 'separator' => ' ']
					);
					?>
				</div>
			</div>
			<div class="small-cell">
				<label class="block-label">Min Price</label>

				<div class="input-wrapper">
					<?php echo $form->dropDownList($model, 'minPrice', $minPrices, ['empty' => 'No Minimum']); ?>
				</div>
			</div>
			<div class="small-cell">
				<label class="block-label">Max Price</label>

				<div class="input-wrapper">
					<?php echo $form->dropDownList($model, 'maxPrice', $maxPrices, ['empty' => 'No Maximum']); ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="half-cell">
				<label class="block-label">Current Position</label>

				<div class="input-wrapper">
					<?php echo $form->dropDownList($model, 'currentPosition', CHtml::listData(ClientStatus::model()->sales()->findAll(), 'cst_id', 'cst_title'), ['empty' => '']) ?>
				</div>
			</div>
			<div class="half-cell">
				<label class="block-label">&nbsp;</label>
				<input type="submit" value="REGISTER" class="btn full-width">
			</div>
		</div>

		<?php $this->endWidget() ?>
	</div>
</div>

<?php if ($result['type'] == 'callback' && $model->email):
	$email = $model->email;
	$telephone = $model->telephone ? $model->telephone : '';
	$message = $model->errors["registeredInfo"][0] ? $model->errors["registeredInfo"][0] : '';
	?>
<script type="text/javascript">
	callback();
	function callback() {
		$.post("/client/callback/email/<?php echo $email ?>/telephone/<?php echo $telephone ?>/message/<?php echo $message ?>", {  }, function (data) {
			$.fancybox.showActivity();
			$.fancybox(data, {
				'autoDimensions': true,
				'overlayColor': '#FFFFFF',
				'centerOnScroll': true,
				'autoScale': true,
				'transitionIn': 'elastic',
				'transitionOut': 'elastic',
				'padding': 0
			});
		});
		return false;
	}
</script>
<?php endif; ?>