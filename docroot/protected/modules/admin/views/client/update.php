<?php
/**
 * @var    $this         ClientController
 * @var    $model        Client
 * @var    $form         AdminForm
 * @var    $offices      Office[]
 * @var    $features     Feature[]
 * @var    $types        PropertyType[]
 * @var    $tabbedView   TabbedView
 * @var    $clientScript CClientScript
 */
$clientScript = Yii::app()->clientScript;
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/adminUtilHead.js', CClientScript::POS_HEAD);
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/adminUtil.js', CClientScript::POS_END);
$clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/note.js', CClientScript::POS_HEAD);

$matchingPostcodes = [];
$subtypes = [];
$offices = Office::model()->enabledClientMatching()->findAll();
foreach ($offices as $value) {
	$matchingPostcodes[$value->id] = LinkOfficeToPostcode::model()->getPostcodeList($value->id);
}

$saveProceedBtn = '';
if (isset($_GET['useClient']) && $_GET['useClient']) {
	$saveProceedBtn = CHtml::submitButton('Save & Proceed', ['name' => 'Client[saveProceed]', 'class' => 'btn',]);
}
$form = $this->beginWidget('AdminForm', ['id' => 'client-edit-' . $model->cli_id, 'htmlOptions' => ['class' => 'client-edit-form']]); // id is strange. had to add extra class.
?>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header">Update Client</div>
			<?php if (!$model->isNewRecord): ?>
				<div class="content">
					<?php echo $model->getFullName(true); ?>
				</div>
			<?php endif ?>
			<div class="block-buttons">
				<?php echo CHtml::submitButton('Save', ['class' => 'btn']) ?>
				<?php echo $saveProceedBtn ?>
			</div>
		</fieldset>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<div>
			<?php if (Yii::app()->user->hasFlash('client-update-success')) : ?>
				<div class="flash success remove"><?= Yii::app()->user->getFlash('client-update-success') ?></div>
			<?php endif ?>
			<?php if (!$model->isNewRecord && !$model->telephones): ?>
				<div class="flash warning">
					This client does not have a valid phone number. please try to fix it in order to keep the database
					clean.
				</div>
			<?php endif ?>
			<?php if (!$model->isNewRecord && $model->addressID == 0): ?>
				<div class="flash warning">
					This client doesn't have a proper primary address. please try to fix it in order to keep the
					database
					clean.
				</div>
			<?php endif ?>
			<?php if ($model->hasErrors()): ?>
				<div class="flash danger">
					<?= $form->errorSummary($model) ?>
				</div>
			<?php endif ?>
		</div>
		<?php $tabbedView = $this->beginWidget('TabbedLayout', [
				'id'        => "client-" . $model->cli_id,
				'activeTab' => 'generalInfo'
		]); ?>

		<?php $tabbedView->beginTab("General Client info", ['id' => 'generalInfo']) ?>
		<?php $this->renderPartial('tabs/generalInfo', compact('form', 'model')) ?>
		<?php $tabbedView->endTab(); ?>

		<?php $tabbedView->beginTab('Preferences', ['id' => 'salesPreference']) ?>
		<?php $this->renderPartial('tabs/salesPreference', compact('form', 'model', 'types')) ?>
		<?php $tabbedView->endTab(); ?>

		<?php $tabbedView->beginTab('Areas', ['id' => 'areas']) ?>
		<?php $this->renderPartial('tabs/areas', compact('form', 'model', 'offices', 'matchingPostcodes')) ?>
		<?php $tabbedView->endTab() ?>

		<?php $tabbedView->beginTab('Specifics', ['id' => 'specifics']) ?>
		<?php $this->renderPartial('tabs/specifics', compact('form', 'model', 'features')) ?>
		<?php
		$this->renderPartial("application.modules.admin4.views.note.addNote", array(
				'noteTypeId'   => $model->cli_id,
				'noteType'     => Note::TYPE_CLIENT_REQ,
				'title'        => 'Requirements',
				'textBoxTitle' => 'Special Requirement'
		));
		?>
		<?php $tabbedView->endTab() ?>

		<?php $tabbedView->beginTab('Other Information', ['id' => 'otherInfo']) ?>
		<?php $this->renderPartial('tabs/otherInfo', compact('form', 'model')) ?>
		<?php $tabbedView->endTab() ?>

		<?php if ($model->viewings) : ?>
			<?php $tabbedView->beginTab('Viewings', ['id' => 'viewings']) ?>
			<?php $this->renderPartial('tabs/viewings', compact('form', 'model')) ?>
			<?php $tabbedView->endTab() ?>
		<?php endif; ?>

		<?php if ($model->instructions) : ?>
			<?php $tabbedView->beginTab('Instructions', ['id' => 'instructions']) ?>
			<div class="content">
				<table class="small-table">
					<tr>
						<th></th>
						<th>Type</th>
						<th>Status</th>
						<th>Date</th>
						<th>id</th>
					</tr>
					<?php foreach ($model->instructions as $instruction): ?>
						<tr>
							<td class="icon-column">
								<?php echo CHtml::link(CHtml::image(Icon::EDIT_ICON, 'Edit appointment'), InstructionController::generateLinkToInstruction($instruction->dea_id), ['class' => 'shaded']) ?>
							</td>
							<td><?php echo $instruction->dea_type ?></td>
							<td><?php echo $instruction->dea_status ?></td>
							<td><?php echo Date::formatDate("d/m/Y", $instruction->dea_created) ?></td>
							<td><?php echo $instruction->dea_id ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
			<?php $tabbedView->endTab() ?>
		<?php endif; ?>

		<?php $this->endWidget() ?>

	</div>
	<fieldset>
		<div class="block-buttons force-margin">
			<?php echo CHtml::submitButton('Save', ['class' => 'btn']) ?>
			<?php echo $saveProceedBtn ?>
		</div>
	</fieldset>
</div>
<?php $this->endWidget() ?>

<script type="text/javascript">

	(function ()
	{
		$('body').on('click', '.client-notes', function ()
		{
			(new Popup('/admin4/Note/edit/callback/updateNotes/popup/true/id/' + this.id.replace('client-note-', ''))).open();
		});

		function updateNotes(noteID)
		{
			$.get(document.location.href, function (data)
			{
				$('#client-note-' + noteID).replaceWith($('#client-note-' + noteID, data));
			})
		}

		var checkEmailUpdate = function ()
		{
			if (confirm('You are about to register a new vendor.\n\nHe will automatically receive email updates. If you have not confirmed this with him, please click cancel to stop this from happening.\n\n\nThank you')) {
				$('.email-updates-radio[value=Yes]').attr('checked', true)
				$('.email-updates-radio[value=No]').attr('checked', false)
			} else {
				$('.email-updates-radio[value=Yes]').attr('checked', false)
				$('.email-updates-radio[value=No]').attr('checked', true)
			}
		}

		var isNew = <?php echo $model->isNewRecord ? 'true' : 'false' ?>;

		$('.client-edit-form').on('submit', function ()
		{
			if (isNew && $('.email-updates-radio[value=Yes]').is(':checked')) checkEmailUpdate()
		})
	})();

</script>


