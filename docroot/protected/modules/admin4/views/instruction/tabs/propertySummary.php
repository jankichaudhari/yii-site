<?php
/**
 * @var $this              InstructionController
 * @var $value             Deal
 * @var $model             Deal
 * @var $form              AdminFilterForm
 * @var $clientScript      CClientScript
 * @var $otherInstructions Deal[]
 */
?>
<div class="content">
	<?php if ($model->hasErrors()): ?>
		<div class="flash danger">
			<?php echo $form->errorSummary($model) ?>
		</div>
	<?php endif ?>


	<?php if ($model->address): ?>
		<div class="control-group">
			<label class="control-label">Address</label>

			<div class="controls text">
				<?= $model->address->getFullAddressString(', '); ?>
			</div>
		</div>
	<?php endif; ?>


	<?php echo $form->beginControlGroup($model, 'dea_status'); ?>
	<?php echo $form->controlLabel($model, 'dea_status'); ?>
	<div class="controls text">
		<?= $model->dea_status; ?>
		<?php
		if ($model->dea_type == 'Lettings') {
			$tenantList = $model->property->getClientNames('tenants', ',');
			if (isset($tenantList)) {
				echo ' — ' . $tenantList . ' at ';
				echo Locale::formatPrice($model->getPrice('pcm'), true, 'pcm');
			}
		} else {
			$acceptedOffer = Offer::model()->findByAttributes(['off_status' => 'Accepted', 'off_deal' => $model->dea_id]);
			if (in_array($model->dea_status, [Deal::STATUS_UNDER_OFFER, Deal::STATUS_EXCHANGED, Deal::STATUS_EXCHANGED]) && $acceptedOffer) {
				$clientName = ($acceptedOffer->clients) ? ' — ' . $acceptedOffer->clients[0]->fullName : '';
				$offerPrice = is_double($acceptedOffer->off_price) ? ' at ' . Locale::formatPrice($acceptedOffer->off_price) : '';
				echo $clientName . $offerPrice;
			}
		}
		?>
	</div>
	<?php echo $form->endControlGroup(); ?>

	<div class="control-group">
		<label class="control-label">Price</label>

		<div class="controls text">
			<?php
			if ($model->dea_type == 'Lettings') {
				echo Locale::formatPrice($model->dea_marketprice, true, 'pcm') . ' - ' . Locale::formatPrice($model->getPrice('p/w'), true, 'p/w');
			} else {
				$thisPrice = $model->getPrice();
				echo Locale::formatCurrency($thisPrice);
				echo $tenure = (empty($thisPrice)) ? ' (Valuation)' : ' ' . $model->dea_tenure;
			}
			?>
		</div>
	</div>

	<?php if ($model->owner): ?>
		<div class="control-group">
			<label class="control-label">Vendors</label>

			<div class="controls text">
				<?php
				$o = [];
				foreach ($model->owner as $owner) $o[] = CHtml::link($owner->fullName, ['client/update', 'id' => $owner->cli_id]);
				echo implode(', ', $o);
				?>
			</div>
		</div>
	<?php endif; ?>
	<?php if ($model->tenant): ?>
		<div class="control-group">
			<label class="control-label">Tenants</label>

			<div class="controls text">
				<?php
				$o = [];
				foreach ($model->tenant as $tenant) {
					$o[] = CHtml::link($tenant->fullName, ['client/update', 'id' => $tenant->cli_id]);
				}
				echo implode(', ', $o);
				?>
			</div>
		</div>
	<?php endif; ?>

	<div class="control-group">
		<label class="control-label">Property Type</label>

		<div class="controls text">
			<?php
			$propertyFloor = ($model->dea_floor && $model->dea_floor != 'NA') ? ' on ' . $model->dea_floor . ' floor' : '';
			$pType = isset($model->propertyType) ? $model->propertyType->pty_title : 'N/A';
			$pSubType = isset($model->propertySubtype) ? $model->propertySubtype->pty_title : 'N/A';
			echo $pType . ' / ' . $pSubType . $propertyFloor;;
			?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			Floor
		</label>

		<div class="controls text">
			<?php echo $model->dea_floor ? $model->dea_floor . ' floor' : 'N/A' ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Rooms</label>

		<div class="controls text">
			<?php echo $model->getPropertyRoomString('<br>'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Floors</label>

		<div class="controls">
			<?php echo $model->dea_floors ? : 'N/A' ?>
		</div>
	</div>


	<div class="control-group">
		<label class="control-label">Internal Area</label>

		<div class="controls text">
			<?php echo $model->getInternalArea() ? $model->getInternalArea() . ' m&sup2; - these are approximate figures' : ''; ?>
		</div>
	</div>

	<?php foreach ($otherInstructions as $instruction): ?>
		<div class="control-group">
			<label class="control-label">Also on with <?= $instruction->dea_type ?></label>

			<div class="controls text">
				<?php echo CHtml::link(
								$instruction->property->address->getFullAddressString(', ') . "   (" . $instruction->dea_type . " / " . $instruction->dea_status . ")",
								$this->createUrl('instruction/summary', ['id' => $instruction->dea_id]),
								['target' => '_blank']);
				?>
			</div>
		</div>
	<?php endforeach; ?>

	<?php $this->renderPartial("application.modules.admin4.views.note.addNote", array(
			'noteTypeId'   => $model->dea_id,
			'noteType'     => Note::TYPE_DEAL_GENERAL,
			'title'        => 'General note(s)',
			'textBoxTitle' => 'General Note'
	)) ?>
</div>
