<?php
/**
 * @var $this  CController
 * @var $model Client
 */

$havingAddresses = [];
?>

<div class="row-fluid">
	<div class="span12">
		<fieldset style="color:#666;">
			<div class="block-header">Select property</div>
			<div class="clearfix"></div>
			<?php if ($model->properties): ?>
				<div class="control-group">
					<div class="controls text">
						<p>This client already has some associated properties. please select one:</p>
						<?php foreach ($model->properties as $key => $value): ?>
							<?php $havingAddresses[$value->address->id] = true; ?>
							<?php echo CHtml::Link($value->getAddressObject()->getFullAddressString(' '), $this->createUrl('propertySelected', ['proeprtyId' => $value->pro_id])) ?>
								<?php echo $value->addressId == $model->addressID ? '- Clients address' : '' ?>

							<br>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif ?>
			<?php if ($model->hasAddress() && !isset($havingAddresses[$model->addressID])) : ?>
				<?php if ($model->address->properties): ?>
					<div class="control-group">
						<div class="controls text">
							<p>Clients address already has related properties!</p>
							<?php foreach ($model->address->properties as $property): ?>
								<a href="<?php echo $this->createUrl('property/update', [
																						'id'       => $property->pro_id,
																						'clientId' => $model->cli_id,
																						]) ?>" class="btn btn-success"><?php echo $model->address->getFullAddressString(' ') ?>
									(<?php echo $property->pro_id ?>)</a>
								<br>
							<?php endforeach; ?>


						</div>
					</div>
				<?php else: ?>
					<div class="control-group">
						<div class="controls text">
							<p>Would you like to use clients address to create new property?</p>
							<a href="<?php echo $this->createUrl('property/create', [
																					'addressId' => $model->address->id, 'owner' => $model->cli_id, 'nextStep' => 'AppointmentBuilder_propertySelected'
																					]) ?>" class="btn btn-success"><?php echo $model->address->getFullAddressString(' ') ?></a>
						</div>
					</div>
				<?php endif ?>
			<?php endif ?>
			<div class="control-group form-buttons shaded">
				<div class="controls">
					<a href="<?php echo $this->createUrl('property/Select', [
																			'nextStep' => 'AppointmentBuilder_propertySelected',
																			'owner'    => $model->cli_id
																			]) ?>" class="btn btn-success">Search property</a>
				</div>
			</div>
		</fieldset>
	</div>
</div>

