<?php
/**
 * @var $this              MailController
 * @var $model             MandrillMessage
 * @var $dataProviderModel MandrillEmail
 * @var $form              AdminFilterForm
 */

?>

<fieldset>
	<div class="block-header">Message Information</div>
	<div class="control-group">
		<label class="control-label">Queued</label>

		<div class="controls">
			<?php echo $model->emailsQueued ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Sent</label>

		<div class="controls">
			<?php echo $model->emailsSent ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Rejected</label>

		<div class="controls">
			<?php echo $model->emailsRejected ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Bounced</label>

		<div class="controls">
			<?php echo $model->emailsBounced ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Opened</label>

		<div class="controls">
			<?php echo $model->emailsOpened ?>
		</div>
	</div>


</fieldset>

<?php
$form = $this->beginWidget('AdminFilterForm', array(
												   'id'             => 'email-list-form',
												   'model'          => $dataProviderModel,
												   'ajaxFilterGrid' => 'email-list',
											  ));
?>

<fieldset>
	<div class="block-header">Search</div>
	<div class="content">
		<div class="control-group">
			<label class="control-label">Email</label>

			<div class="controls">
				<?php echo $form->textField($dataProviderModel, 'email') ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Status</label>

			<div class="controls">
				<?php echo $form->checkBoxList($dataProviderModel, 'status', MandrillEmail::getStatuses(), ['separator' => ' ']) ?>
			</div>
		</div>

	</div>
</fieldset>
<?php
$this->endWidget();
$this->widget('AdminGridView', array(
									'dataProvider' => $dataProviderModel->search(),
									'id'           => 'email-list',
									'columns'      => array(
										array(
											'class'    => 'CButtonColumn',
											'template' => '{details}',
											'buttons'  => array(
												'details' => array(
													'label'    => 'Details',
													'url'      => function ($data) {
																return $this->createUrl('mail/details', ['id' => $data->id]);
															},
													'imageUrl' => "/images/sys/admin/icons/edit-icon.png",
												)
											)
										),
										'status',
										array(
											'value' => function (MandrillEmail $data) {
														return Date::formatDate('d/m/Y H:i', $data->sent);
													}
										),
										'email',
									)
							   ))
?>
