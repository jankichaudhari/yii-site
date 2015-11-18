<?php
/**
 * @var $this  BranchControllerBase
 * @var $model Branch
 *
 */
?>
<div>
	<fieldset id="branch-info-<?php echo $model->bra_id ?>">
		<div class="block-header"><?php echo $model->bra_title ?></div>
		<div class="content">
			<div class="control-group">
				<label class="control-label">Title</label>

				<div class="controls"><span class="text"><?php echo $model->bra_title ?></span></div>
			</div>
			<div class="control-group">
				<label class="control-label">Email</label>

				<div class="controls"><span class="text"><?php echo $model->bra_email ?></span></div>
			</div>
			<div class="control-group">
				<label class="control-label">Telephone</label>

				<div class="controls"><span class="text"><?php echo $model->bra_tel ?></span></div>
			</div>
			<div class="control-group">
				<label class="control-label">Fax</label>

				<div class="controls"><span class="text"><?php echo $model->bra_fax ?></span></div>
			</div>
			<div class="control-group">
				<label class="control-label">Status</label>

				<div class="controls"><span class="text"><?php echo $model->bra_status ?></span></div>
			</div>
			<div class="control-group">
				<label class="control-label">Colour</label>

				<div class="controls">
					<?php if ($model->bra_colour): ?>
						<div style="border:1px solid #666; width:150px; background: #<?php echo $model->bra_colour ?>;" class="text">#<?php echo $model->bra_colour ?></div>
					<?php else: ?>
						<span class="text">Not set</span>
					<?php endif ?>
				</div>
			</div>
			<?php  if ($model->businessUnit): ?>
				<div class="control-group">
					<label class="control-label">Business Unit</label>

					<div class="controls"><span class="text"><?php echo $model->businessUnit->ListItem; ?></span></div>
				</div>
			<?php endif ?>
		</div>
		<div class="block-buttons force-margin">
			<input type="button" class="btn editBranch" value="Edit" data-branch-id="<?php echo $model->bra_id ?>">
		</div>
</div>
</fieldset>
</div>