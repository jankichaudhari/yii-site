<?php
/**
 * @var $this  OfferController
 * @var $form  AdminForm
 * @var $model Offer
 */
$form = $this->beginWidget('AdminForm');
$clientId = isset($clientId) ? $clientId : 0;
if (!$model->off_deal && $model->instruction->dea_id) {
	$model->off_deal = $model->instruction->dea_id;
}
$acceptedOffer = Offer::model()->findByAttributes([
												  'off_status' => Offer::STATUS_ACCEPTED, 'off_deal' => $model->off_deal
												  ]);

$clientStatus = false;
$differentStatusString = '';
$clientsHaveDifferentStatuses = false;
if ($model->clients) {
	$clientStatus = $model->clients[0]->cli_salestatus;
	foreach ($model->clients as $client) {
		if (!$client->saleStatus) continue;
		$differentStatusString .= '<div>' . $client->getFullName() . ' — ' . $client->saleStatus->cst_title . ' </div>';
		if ($client->cli_salestatus != $clientStatus) {
			$clientsHaveDifferentStatuses = true;
		}

	}
}

?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
				<div class="block-header">
					<?php echo $model->isNewRecord ? 'NEW OFFER' : 'EDIT OFFER' ?>
				</div>
				<div class="content">
					<div class="row-fluid">
						<div class="span8">
							<div class="control-group">
								<div class="controls text force-margin">
									<div class="flash success remove"><?php echo Yii::app()->user->getFlash('offer-updated') ?></div>
									<div class="flash success remove"><?php echo Yii::app()->user->getFlash('offer-restored') ?></div>
									<div class="flash danger"><?php echo $model->off_status == Offer::STATUS_DELETED ? 'Offer is deleted!' : '' ?></div>
									<div class="flash danger"><?php echo $form->errorSummary($model); ?></div>
								</div>
							</div>
						</div>
					</div>
					<?php if ($model->isNewRecord): ?>
						<div class="control-group">
							<label class="control-label"><?php echo $form->controlLabel($model, 'off_price'); ?></label>

							<div class="controls">
								<?php echo $form->textField($model, 'off_price', ['placeholder' => '£']); ?>
							</div>
						</div>
					<?php else: ?>
						<div class="control-group">
							<label class="control-label"><?php echo $form->controlLabel($model, 'off_timestamp'); ?></label>

							<div class="controls">
								<span class="text"><?php echo Date::formatDate('d F Y', $model->off_timestamp) ?></span>
								<?php echo $form->hiddenField($model, 'off_timestamp', ['value' => date('Y-m-d H:i:s')]); ?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label"><?php echo $form->controlLabel($model, 'off_price'); ?></label>

							<div class="controls">
								<span class="text"><?php echo Locale::formatPrice($model->off_price) ?></span>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label"><?php echo $form->controlLabel($model, 'off_status'); ?></label>

							<div class="controls hint">
								<?php echo $form->dropDownList($model, 'off_status', Offer::getStatuses(), ['disabled' => $model->off_status == Offer::STATUS_DELETED]); ?>
								<?php if (!in_array($model->off_status, [
																		Offer::STATUS_DELETED, Offer::STATUS_ACCEPTED
																		])
								): ?>
									<span class="hint">
										If offer is Accepted, other offer(s) will turn to Rejected
									</span>
								<?php endif ?>
							</div>
						</div>
					<?php endif; ?>

					<?php echo $form->hiddenField($model, 'off_deal'); ?>
					<div class="control-group">
						<label class="control-label">Clients</label>

						<div class="controls">
							<div id="client-list">
								<?php foreach ($model->clients as $client): ?>
									<div class="tag" id="client-span-<?php echo $client->cli_id ?>">
										<input type="hidden" name="client[]" value="<?php echo $client->cli_id ?>"/>
										<?php echo Chtml::link($client->getFullName(), Yii::app()->createUrl('admin4/client/update', ['id' => $client->cli_id])) ?>
										<span class="delete client-delete">×</span>
									</div>
								<?php endforeach ?>
							</div>
							<?php
							echo CHtml::link('Add Client', array(
																'client/popupSelect', 'onSelect' => 'addClient',
																'popup'                          => true,
																'for'                            => 'offer_client',
																'offerId'                        => $model->off_id ? : 0,
																'instructionId'                  => $model->instruction->dea_id

														   ), [
															  'class' => 'btn btn-smaller btn-green',
															  'id'    => 'add-client-button'
															  ]); ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Client status</label>

						<div class="controls">
							<?php echo CHtml::dropDownList(
								'clientStatus',
								$clientStatus,
								CHtml::listData(ClientStatus::model()->sales()->findAll(), 'cst_id', 'cst_title'), ['empty' => '']
							) ?>
						</div>
					</div>
					<?php if ($clientsHaveDifferentStatuses): ?>
						<div class="control-group">
							<div class="controls force-margin">
								<?php echo $differentStatusString; ?>
							</div>
						</div>
					<?php endif ?>
					<div class="control-group">
						<?php echo $form->controlLabel($model, 'off_neg'); ?>

						<div class="controls">
							<?php echo $form->dropDownList(
								$model,
								'off_neg',
								CHtml::listData(User::model()->onlyActive()->alphabetically()->findAll(), 'use_id', 'fullName'));
							?>
						</div>
					</div>
					<div class="control-group">
						<?php echo $form->controlLabel($model, 'off_conditions'); ?>

						<div class="controls">
							<?php echo $form->textArea($model, 'off_conditions'); ?>
						</div>
					</div>
					<div class="control-group">
						<?php echo $form->controlLabel($model, 'off_notes'); ?>

						<div class="controls">
							<?php echo $form->textArea($model, 'off_notes'); ?>
						</div>
					</div>

					<div class="control-group form-buttons shaded">
						<div class="controls force-margin">
							<input type="submit" class="btn" value="Save">
							<?php if ($this->popupMode) : ?>
								<!--<input type="submit" class="btn btn-gray" value="Save & Close" name="close">-->
								<input type="button" class="btn btn-gray" value="Close" onclick="window.close()">
							<?php endif ?>
							<?php if ($model->off_status == Offer::STATUS_DELETED): ?>
								<input type="submit" class="btn btn-green" value="Restore" name="restore">
							<?php else: ?>
								<input type="submit" class="btn btn-red" value="Delete" name="delete">
							<?php endif ?>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
<!--	--><?php //if ($model->conditionsChanges): ?>
<!--		<div class="row-fluid">-->
<!--			<div class="span12">-->
<!--				<fieldset>-->
<!--					<div class="block-header">History</div>-->
<!--					<div class="content">-->
<!--						<div class="control-group">-->
<!--							<table class="small-table">-->
<!--								<tr>-->
<!--									<th>Conditions</th>-->
<!--									<th>Notes</th>-->
<!--								</tr>-->
<!--								<tr>-->
<!--									<td width="50%">-->
<!--										--><?php //foreach ($model->conditionsChanges as $key => $conditionsChange) {
//											if ($conditionsChange->cha_new) {
//												?>
<!--												<div class="control-group">-->
<!--											<span class="control-label">--><?php //echo $conditionsChange->creator->getFullName() ?>
<!--												<br>--><?php //echo Date::formatDate('d/m/Y H:i', $conditionsChange->cha_datetime) ?><!--</span>-->
<!---->
<!--													<div class="controls">-->
<!--														<span class="text">--><?php //echo nl2br($conditionsChange->cha_new) ?><!--</span>-->
<!--													</div>-->
<!--												</div>-->
<!--												--><?php //echo $form->separator();
//											}
//										}
//										?>
<!--									</td>-->
<!--									<td>-->
<!--										--><?php //foreach ($model->notesChanges as $key => $notesChange) {
//											if ($notesChange->cha_new) {
//												?>
<!--												<div class="control-group">-->
<!--											<span class="control-label">--><?php //echo $notesChange->creator->getFullName() ?>
<!--												<br>--><?php //echo Date::formatDate('d/m/Y H:i', $notesChange->cha_datetime) ?><!--</span>-->
<!---->
<!--													<div class="controls">-->
<!--														<span class="text">--><?php //echo nl2br($notesChange->cha_new) ?><!--</span>-->
<!--													</div>-->
<!--												</div>-->
<!--											--><?php
//											}
//										}
//										?>
<!--									</td>-->
<!--								</tr>-->
<!--							</table>-->
<!--						</div>-->
<!--					</div>-->
<!--				</fieldset>-->
<!--			</div>-->
<!--		</div>-->
<!--	--><?php //endif ?>
</div>
<?php $this->endWidget() ?>
<script id="client-tag-template" type="text/client-tag-template">
	<div class="tag" id="client-span-{cli_id}">
		<input type="hidden" name="client[]" value="{cli_id}">
		<?php echo CHtml::link('{fullName}', Yii::app()->createUrl('admin4/client/update', ['id' => '{cli_id}'])); ?>
		<span data-id="{cli_id}" class="delete client-delete">×</span>
</script>
<script type="text/javascript">
	$(".datePicker").datepicker();

	var addClient = function (id) {
		$.getJSON('<?php echo $this->createUrl('client/info') ?>', {format: 'JSON', 'id': id },
				function (data) {
					var tpl = $('#client-tag-template').html();
					for (key in data) {
						var regexp = new RegExp('{' + key + '}', 'gi');
						tpl = tpl.replace(regexp, data[key]);
					}
					$('#client-list').append(tpl);
				}

		);
	}

	function popupWindow(url) {
		new Popup(url).open();
		return false;
	}

	$('#add-client-button').on('click', function () {
		(new Popup(this.href)).open();
		return false;
	});

	$('body').on('click', '.client-delete', function () {
		if (confirm('Are you sure you want to delete this client?')) {
			this.parentNode.parentNode.removeChild(this.parentNode);
		}
	})
</script>
