<?php
/**
 * @var $dealNotes Deal Notes
 * @var $noteType
 */

ob_start();
foreach ($dealNotes as $note):
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
			<?php echo CHtml::image(Icon::EDIT_ICON, "Edit", ["onClick" => "popupWindow('" . $this->createUrl("note/edit/", ["id" => $note->not_id, 'callback' => "showNotesBlocksById", "popup" => true]) . "');"]) ?>
		</div>
		<div class="icon">
			<?php echo CHtml::image(Icon::CROSS_ICON, "Delete", ["onClick" => "deleteNote('" . $note->not_id . "')"]) ?>
		</div>
	</div>
	<div class="content">
		<?php echo nl2br($note->not_blurb) ?>
	</div>
</div>

<?php
endforeach;
echo json_encode(array('blockType' => $noteType, 'html' => ob_get_clean()));
return;
/**
 * $data = '';
 foreach ($dealNotes as $note) {
 $thisUrl = $this->createUrl("note/edit/", ["id" => $note->not_id, 'callback' => "showNotesBlocksById", "popup" => true]);

 $data .= '
 <div class="noteInfo">';
 					  $data .= $note->creator->getFullUsername();
 					  $data .= "Edited:" . date("d/m/Y", strtotime($note->not_edited));
 					  $data .= CHtml::link(
 					  CHtml::image(
 					  Icon::EDIT_ICON, "Edit"
 					  ),
 					  '#',
 					  ["onClick" => "popupWindow('" . $thisUrl . "');"]
 					  );
 					  if ($note->not_status != 'Deleted') {
 					  $data .= CHtml::link(
 					  CHtml::image(
 					  Icon::CROSS_ICON, "Delete"
 					  ),
 					  '#',
 					  ["onClick" => "deleteNote('" . $note->not_id . "')"]
 					  );
 					  }
 					  $data .= '
 </div>
 <div class="noteBlurb ' . $note->not_status . '">';
 												 $data .= Util::strapString($note->not_blurb, 0, 200);
 												 $data .= '
 </div>';
 }

 */

?>
