<?php
/**
 * @var $noteTypeId
 * @var $noteType
 * @var $textBoxTitle
 * @var $title
 */
?>
<div class="control-group">
	<label class="control-label" for="<?php echo $noteType ?>_not_blurb"><?php echo $textBoxTitle ?></label>

	<div class="controls">
		<div class="noteBlock">
			<textarea name="<?php echo $noteType ?>[not_blurb]" id="<?php echo $noteType ?>_not_blurb" placeholder="Add note here ..."></textarea>
			<input type="hidden" name="<?php echo $noteType ?>[not_id]" id="<?php echo $noteType ?>_not_id" value="">

			<div style="text-align: right">
				<input type="button" value="Add" name="saveNote" class="btn btn-gray" onclick="saveNoteBlurb('<?php echo $noteType ?>','<?php echo $noteTypeId ?>');" id="saveNote">
			</div>
			<div class="noteList" id="<?php echo $noteType ?>"></div>
		</div>
	</div>
</div>
<script>
	(function ()
	{
		showNotesBlocksByType('<?php echo $noteTypeId ? $noteTypeId : 0 ?>', '<?php echo $noteType ?>');
	})();
</script>
