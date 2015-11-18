<?php
/**
 * @package application.LocalEvent.views
 * @var $data LocalEvent
 * @see     LocalEventController::actionIndex()
 * @see     views/localEvent/index.php
 *
 */
?>
<div class="view" style="vertical-align: top;">
	<table style="width:100%">
		<tr>
			<td style="vertical-align: top; width:1%;">
				<?php
				if ($data->mainImage) {
					echo CHtml::link(CHtml::image(Yii::app()->params['imgUrl'] . "/LocalEvent/" . $data->id . "/" . $data->mainImage->smallName), array(
																																					   'update',
																																					   'id' => $data->id
																																				  ));
				}
				?>
			</td>
			<td style="vertical-align: top; padding-left: 10px;">
				<b style="font-size: 18px;"><?php echo CHtml::link(CHtml::encode($data->heading), array(
																									   'update',
																									   'id' => $data->id
																								  )); ?></b>
				<br/>

				<div style="width:70%;">
					<div style="margin:5px 0; border-bottom: 1px dotted #555;"><?php echo CHtml::encode($data->strapline); ?></div>
					<?php if ($data->dateFrom): ?>
						<b><?php echo "Starts"; ?>:</b>
						<?php echo CHtml::encode(Date::formatDate("d/m/Y", $data->dateFrom)); ?>
						<?php echo $data->timeFrom ? " " . CHtml::encode($data->timeFrom) : "" ?>
						<br/>
					<?php endif ?>

					<?php if ($data->dateTo) : ?>
						<b><?php echo "Finishes" ?>:</b>
						<?php echo CHtml::encode(Date::formatDate("d/m/Y", $data->dateTo)); ?>
						<?php echo $data->timeTo ? " " . CHtml::encode($data->timeTo) : "" ?>
						<br/>
					<?php endif ?>
					<b>Status: </b><?php echo $data->statusValue ? $data->statusValue->ListItem : "" ?>
					<br>
					<?php
					echo CHtml::link(
						'Delete',
						$this->createUrl('delete', ['id' => $data->id]),
						array(
							 'onclick' => ' {' .
							 CHtml::ajax(array(
											  'type'       => 'POST',
											  'beforeSend' => 'js:function(){if(confirm("Are you sure you want to delete?"))return true;else return false;}',
											  'url'        => $this->createUrl('delete', array('id'  => $data->id,
																							  'ajax' => 'delete'
																						 )),
											  'complete'   => "js:function(jqXHR, textStatus){ location.reload(); }"
										 )) .
							 'return false;}',
						)
					);
					?>
				</div>
			</td>
		</tr>
	</table>
</div>