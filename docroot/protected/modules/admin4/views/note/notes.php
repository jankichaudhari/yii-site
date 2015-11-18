<?php
/**
 * @var $notes Deal Notes
 * @var $noteType
 */

ob_start();
foreach ($notes as $note):
	?>
	<div class="note <?php echo $note->not_status ?>">
		<div class="header">
			<div class="author">
				<?php echo $note->creator->getFullUsername(); ?>
			</div>
			<div class="date">
				<?php echo Date::formatDate('d/m/y', $note->not_edited) ?>
			</div>
			<div class="icon">
				<?php echo CHtml::image(Icon::EDIT_ICON, "Edit", ["onClick" => "popupWindow('" . $this->createUrl("note/edit/", ["id"      => $note->not_id,
																																'callback' => "showNotesBlocksById",
																																"popup"    => true
																																]) . "');"
																 ]) ?>
			</div>
			<div class="icon">
				<?php echo CHtml::image(Icon::CROSS_ICON, "Delete", ["onClick" => "deleteNote('" . $note->not_id . "')"]) ?>
			</div>
		</div>
		<div class="content">
			<?php echo nl2br($note->not_blurb) ?>
		</div>
		<input type="hidden" name="noteId-<?php echo $noteType ?>[]" value="<?php echo $note->not_id ?>">
	</div>

<?php
endforeach;
echo json_encode(array('blockType' => $noteType, 'html' => ob_get_clean()));
return;
?>