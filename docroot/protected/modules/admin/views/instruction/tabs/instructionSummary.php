<?php
/**
 * @var $value        Deal
 * @var $model        Deal
 * @var $form         AdminFilterForm
 * @var $clientScript CClientScript
 * @var $otherInstructions
 */
?>

<div class="content"><div class="control-group">
    <label class="control-label"><?= $owner = ($model->dea_type == 'Sales') ? 'Vendor(s)' : 'Landlord(s)'; ?></label>

    <div class="controls text">
		<?= $model->getOwnersNames(','); ?>
    </div>
</div>

<div class="control-group">
    <label class="control-label">Tenant(s)</label>

    <div class="controls text">
		<?= $model->getTenantNames(',') ?>
    </div>
</div>

<div class="control-group">
    <label class="control-label">Property Type</label>

    <div class="controls text">
		<?php $propertyFloor = ($model->dea_floor && $model->dea_floor!='NA') ? ' on ' . $model->dea_floor . ' floor' : '';
		$pType =  isset($model->propertyType->pty_title) ? $model->propertyType->pty_title : 'N/A';
		$pSubType = isset($model->propertySubtype->pty_title) ? $model->propertySubtype->pty_title : 'N/A';
		echo $pType . ' / ' . $pSubType . $propertyFloor;
		?>
    </div>
</div>

<div class="control-group">
    <label class="control-label">Room(s) & Floor(s)</label>

    <div class="controls text">
		<?php $bedRooms   = (!$model->dea_bedroom) ? : $model->dea_bedroom . ' Bedroom, ';
		$receptions = (!$model->dea_reception) ? : $model->dea_reception . ' Reception, ';
		$bathRooms  = (!$model->dea_bathroom) ? : $model->dea_bathroom . ' Bathroom & ';
		$floors     = (!$model->dea_floors) ? : $model->dea_floors . ' Floor';
		echo $bedRooms . $receptions . $bathRooms . $floors;
		?>
    </div>
</div>

<div class="control-group">
    <label class="control-label"><?= $form->controlLabel($model, 'dea_tenure'); ?></label>
    <div class="controls text">
		<?= $model->dea_tenure ?>
    </div>
</div>
<div class="control-group">
    <label class="control-label"><?= $form->controlLabel($model, 'dea_leaseend'); ?></label>
    <div class="controls text">
		<?= Date::formatDate('d/m/Y', $model->dea_leaseend) ?>
    </div>
</div>
<div class="control-group">
    <label class="control-label"><?= $form->controlLabel($model, 'dea_servicecharge'); ?></label>
    <div class="controls text">
		<?= $model->dea_servicecharge ?>
    </div>
</div>
<div class="control-group">
    <label class="control-label"><?= $form->controlLabel($model, 'dea_groundrent'); ?></label>
    <div class="controls text">
		<?= $model->dea_groundrent ?>
    </div>
</div></div>