<?php
/**
 * @var $this  PropertyController
 * @var $model Property
 */
?>
<div class="form wide">
	<fieldset>
		<div class="block-header">Property Info</div>
		<div class="row">
			<label>Full Address:</label><?php echo $model->getFullAddressString() ?>
		</div>
		<div class="row">
			<label>Current landlords:</label> Landlord 1, Landlord 2, Landlord 3
		</div>
		<div class="row">
			<label>Key:</label> #key3
		</div>
		<div class="row">
			<label>Rooms:</label> bedroom, bedroom, bedroom, reception
		</div>
		<div class="row">
			<label>Property type:</label> Apartment / room
		</div>
		<div class="row">
			<label>Area:</label> 104.22 m<sup>2</sup>
		</div>
		<div class="row">
			<label>Notes:</label> Some area here
		</div>
		<div class="row">
			<label>Area:</label> 104.22 m<sup>2</sup>
		</div>
	</fieldset>

	<?php $this->widget('AdminGridView', array(
											  'dataProvider' => new CActiveDataProvider('Deal'),
											  'columns'      => ['dea_id', 'dea_status', 'dea_created', 'dea_launchdate'],
											  'title'        => 'Instructions',

										 )) ?>
	<fieldset style="margin-top:10px; ">
		<div class="row">
			<input type="button" value="Create new instruction">
		</div>
	</fieldset>

	<fieldset style="margin-top:10px; ">
		<div class="block-header">Production</div>
		<div class="row">
			Here comes the section for production (what appears on the public website).
			<br>
			Means that property may be available on the website if we don't have any instructions to sale. NOT FOOLPROOF
		</div>
	</fieldset>
</div>