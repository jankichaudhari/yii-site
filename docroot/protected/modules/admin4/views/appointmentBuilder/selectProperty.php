<?php
/**
 * @var $this  AppointmentBuilderController
 * @var $model Client
 */
$havingAddresses = [];
?>
<div class="row-fluid">
	<div class="span12">
		<fieldset style="color:#666;">
			<div class="block-header">Select Property</div>
			<div class="content">
				<?php if ($model->properties): ?>
					<div class="control-group">
						<div class="controls text">
							<p>This client already has some associated properties. please select one:</p>
							<ul>
								<?php foreach ($model->properties as $key => $value): ?>
									<?php $havingAddresses[$value->address->id] = true; ?>
									<li>
										<?php echo CHtml::link(CHtml::image(Icon::EDIT_ICON), [
																							  'property/update', 'id' => $value->pro_id,
																							  'nextStep'              => 'AppointmentBuilder_propertySelected'
																							  ]) ?>
										<?=CHtml::Link($value->getAddressObject()
															   ->getFullAddressString(' '), $this->createUrl('propertySelected', ['propertyId' => $value->pro_id])); ?>
										<?= $value->addressId == $model->addressID ? '- Clients address' : '' ?>
									</li>

								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				<?php endif ?>
				<?php if ($model->hasAddress() && !isset($havingAddresses[$model->addressID])) : ?>
					<?php if ($model->address->properties): ?>
						<div class="control-group">
							<div class="controls text">
								<p>Clients address already has related properties!</p>
								<?php foreach ($model->address->properties as $property): ?>
									<?= CHtml::link($model->address->getFullAddressString(' '), ['propertySelected', 'propertyId' => $property->pro_id]) ?>
									(<?= $property->pro_id ?>)
									<br>
								<?php endforeach; ?>
							</div>
						</div>
					<?php else: ?>
						<div class="control-group">
							<div class="controls text">
								<p>Would you like to use clients address to create new property?</p>
								<a href="<?= $this->createUrl('property/create', [
																				 'addressId' => $model->address->id, 'owner' => $model->cli_id,
																				 'nextStep'  => 'AppointmentBuilder_propertySelected',
																				 ]) ?>" class="btn btn-success"><?= $model->address->getFullAddressString(' ') ?></a>
							</div>
						</div>
					<?php endif ?>
				<?php endif ?></div>

			<div class="block-buttons">
				<a href="<?= $this->createUrl('searchProperty') ?>" class="btn btn-success">Select property to value</a>
			</div>
		</fieldset>
	</div>
</div>

