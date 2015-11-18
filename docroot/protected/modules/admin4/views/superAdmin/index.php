<?php
/**
 * @var SuperAdminController $this
 */
?>
<style type="text/css">
	hr {
		border-width : 0;
		border       : 1px solid #dedede;
	}
</style>
<div class="row-fluid">
	<div class="span12">
		<?php echo CHtml::link('Manage Users', array('user/'), ['class' => 'button']) ?>
		<hr>
		<?php echo CHtml::link('List Careers', array('Career/'), ['class' => 'button']) ?>
		<hr>
		<?php echo CHtml::link('Add Career', array('Career/create'), ['class' => 'button']) ?>
		<hr>
		<?php echo CHtml::link('Manage offices', array('Office/'), ['class' => 'button']) ?>
	</div>
</div>