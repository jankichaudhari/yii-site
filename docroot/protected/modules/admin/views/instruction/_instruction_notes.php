<?php
/**
 * @var $dealNotes Deal Notes
 * @var $model     Deal
 * @var $noteType
 * @var $textBoxTitle
 * @var $title
 */
?>
<div class="control-group">
	<label class="control-label" for="<?= $noteType ?>_not_blurb"><?= $textBoxTitle ?></label>
	<div class="controls">
		<div class="noteBlock">
			<textarea name="<?= $noteType ?>[not_blurb]" id="<?= $noteType ?>_not_blurb" placeholder="Add note here ..."></textarea>
			<input type="hidden" name="<?= $noteType ?>[not_id]" id="<?= $noteType ?>_not_id" value="">
			<div style="text-align: right">
		<input type="button" value="Add" name="saveNote" class="btn btn-gray" onclick="saveNoteBlurb('<?= $noteType ?>','<?= $model->dea_id ?>');" id="saveNote" style="background: #888888">
			</div>
			<div class="noteList" id="<?= $noteType ?>"></div>
		</div>
	</div>
</div>

<script type="text/javascript">showNotesBlocksByType('<?= $model->dea_id ?>', '<?= $noteType ?>');</script>
