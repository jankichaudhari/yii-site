<?php
/**
 * @var $this  MailshotTypeController
 * @var $model MailshotType
 */
?>
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
				<div class="block-header">Actions</div>
				<div class="block-buttons">
					<?php echo CHtml::link('Create new type', ['mailshotType/create'], ['class' => 'btn']) ?>
				</div>
			</fieldset>
		</div>
	</div>
<?php
$this->widget('AdminGridView', array(
									'dataProvider' => $model->search(),
									'columns'      => array(
										array(
											'class'    => 'CButtonColumn',
											'template' => '{edit}',
											'buttons'  => array(
												'edit' => array(
													'label'    => 'Edit',
													'url'      => function (MailshotType $data) {

																return $this->createUrl('update', ['name' => $data->name]);
															},
													'imageUrl' => Icon::EDIT_ICON
												)

											)
										),
										'name',
										'subject',
										'description',
										array(
											'header' => 'Created at',
											'value'  => function (MailshotType $data) {
														return Date::formatDate('d/m/Y H:i', $data->created);
													}
										),
										'creator.fullName::Created By'
									)
							   ));