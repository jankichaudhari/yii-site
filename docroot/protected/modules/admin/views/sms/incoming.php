<?php
/**
 * @var $this  SmsController
 * @var $model Sms
 * @var $form  AdminFilterForm
 */
?>
	<fieldset>

		<div class="block-header">Search</div>
		<div class="content">
			<?php $form = $this->beginWidget('AdminFilterForm', array(
																	 'id'                   => isset($id) && $id ? $id : 'incoming-message-filter-form',
																	 'enableAjaxValidation' => false,
																	 'model'                => $model,
																	 'ajaxFilterGrid'       => 'incoming-messages',
																	 'storeInSession'       => false,

																)) ?>
			<div class="control-group">
				<label class="control-label">Show</label>

				<div class="controls">
					<?php echo $form->checkBoxList($model, 'isRead', [Sms::READ_UNREAD => 'unread', Sms::READ_READ => 'read'], [
																															   'separator' => '', 'class' => 'show-checkbox'
																															   ]) ?></div>
			</div>
		</div>
		<?php $this->endWidget() ?>
		</div>
	</fieldset>
<?php $this->widget('AdminGridView', array(
										  'id'           => 'incoming-messages',
										  'dataProvider' => $model->incoming(),
										  'title'        => 'Incoming Messages',
										  'columns'      => array(
											  array(
												  'header'   => 'Actions',
												  'class'    => 'CButtonColumn',
												  'template' => '{edit}',
												  'buttons'  => array(
													  'edit' => array(
														  'label'    => 'Edit',
														  'url'      => function (Sms $data) {
															  return $this->createUrl('client/textConversation', ['clientId' => $data->client->cli_id, 'messageId' => $data->id]);
														  },
														  'imageUrl' => Icon::EDIT_ICON
													  ),
												  )
											  ),
											  'id',
											  'fromNumber',
											  array(
												  'type'   => 'raw',
												  'header' => 'Client',
												  'value'  => function (Sms $data) {
													  return $data->client
															  ? CHtml::link($data->client->getFullName(), ['client/update', 'id' => $data->client->cli_id])
															  : '';
												  }
											  ),
											  array(
												  'name'   => 'created',
												  'header' => 'Received',
												  'value'  => function (Sms $data) {
													  return Date::formatDate('d/m/Y H:i', $data->created);
												  }
											  ),
											  'isRead'
										  )
									 ));
