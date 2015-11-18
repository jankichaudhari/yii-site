<?php
/**
 * @var    $this      ClientController
 * @var    $model     Client
 * @var    $form      AdminForm
 */
?>

<div class="content">
	<table class="small-table">
		<tr>
			<th colspan="2">Date</th>
			<th>Neg</th>
			<th>Property</th>
			<th>Feedback</th>
			<th>Edit Viewing</th>
		</tr>
		<?php
		$totalViewed = 0;
		$positiveFeedback = 0;
		$indifferentFeedback = 0;
		$negativeFeedback = 0;
		$noFeedback = 0;
		$activeViewing = false;
		foreach ($model->viewings as $viewing):
			if (($viewing->app_start <= date('Y-m-d H:i:s')) && ($viewing->app_status == Appointment::STATUS_ACTIVE)) {
				$activeViewing = true;
			}
			?>
			<tr class="<?php echo strtotime($viewing->app_start) > time() ? "highlight green" : "" ?>">
				<td><?php echo Date::formatDate("d/m/Y", $viewing->app_start) ?></td>
				<td><?php echo Date::formatDate("H:i", $viewing->app_start) ?></td>
				<td>
					<?php if ($viewing->user): ?>
					<span class="negotiator-color"
						  style="background-color:#<?php echo $viewing->user->use_colour ?> "></span><?php echo $viewing->user->fullName ?>
					<?php endif ?>
				</td>
				<td>
					<?php
					$feedbackIds = [];
					foreach ($viewing->instructions as $instruction) {
						if ($activeViewing) {
							$totalViewed++;
						}
						echo '<div style="padding: 4px 0">';
						echo CHtml::link($instruction->property->address->getFullAddressString(', '), $this->createUrl('instruction/summary', ['id' => $instruction->dea_id]));
						$feedbackIds[$instruction->feedbackId] = $instruction->feedback;
						echo '</div>';
					} ?>
				</td>

				<td>
					<?php
					if ($viewing->app_status == Appointment::STATUS_CANCELLED || $viewing->app_status == Appointment::STATUS_DELETED) {
						echo '(' . $viewing->app_status . ')';
					} else {

						foreach ($feedbackIds as $id => $feedback) {
							if ($activeViewing) {
								switch ($feedback) {
									case LinkDealToAppointment::FEEDBACK_POSITIVE :
										$positiveFeedback++;
										break;
									case LinkDealToAppointment::FEEDBACK_INDIFFERENT :
										$indifferentFeedback++;
										break;
									case LinkDealToAppointment::FEEDBACK_NEGATIVE :
										$negativeFeedback++;
										break;
									default :
										$noFeedback++;
								}
							}
							echo '<div style="padding: 4px 0">';
							echo CHtml::link($feedback ? $feedback : "(not entered)", $this->createUrl('appointment/feedback', ['id' => $id]));
							echo '</div>';
						}

					}
					?>
				</td>
				<td><?php echo CHtml::link(CHtml::image(Icon::EDIT_ICON), AppointmentController::createAppointmentUpdateLink($viewing->app_id)) ?></td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td colspan="5">
				(
				Total Properties Viewed:<b><?php echo $totalViewed ?></b>&nbsp;
				Positive:<b><?php echo $positiveFeedback ?></b>&nbsp;
				Indifferent:<b><?php echo $indifferentFeedback ?></b>&nbsp;
				Negative:<b><?php echo $negativeFeedback ?></b>&nbsp;
				None :<b> <?php echo $noFeedback ?>
					)
			</td>
		</tr>
	</table>
</div>