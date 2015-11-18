<?php
/**
 * @var $this FeedController
 * @var $form AdminForm
 */

$form = $this->beginWidget('AdminForm', array(
											 'method' => 'post',
											 'id'     => 'feed-form',
										));
$columns = array();
$columns[] = array(
	'class'               => 'CCheckBoxColumn',
	'name'                => 'portal_id',
	'value'               => '$data->portal_id',
	'header'              => '',
	'checked'             => 'true',
	'checkBoxHtmlOptions' => array(
		'name' => 'feed[]',
	),
);
if (Yii::app()->user->is('superAdmin')) {
	$columns[] = array(
		'type'        => 'raw',
		'htmlOptions' => array(
			'style' => 'width:50px;'
		),
		'value'       => function ($data) {

			return CHtml::link(CHtml::image(Icon::EDIT_ICON, 'Edit Feed'), array(
																				'feed/update',
																				'id' => $data->portal_id
																		   ));
		}
	);
}
$columns[] = array(
	'name'        => 'portal_id',
	'htmlOptions' => array(
		'style' => 'width:50px;'
	)
);
$columns[] = 'portal_name';
?>

	<div class="row-fluid">
		<div class="span6">
			<?php $this->widget('AdminGridView', array(
													  'dataProvider'   => $model->search(),
													  'title'          => 'Select Feeds to run',
													  'actions'        => array(
														  '<div class="block-buttons">
														  <input type="submit" class="btn" value="Send Feeds">
														  ' . (Yii::app()->user->is('superAdmin') ? CHtml::link('Create New', ['feed/create'], ['class' => 'btn btn-green']) : '') . '
															  </div>'
													  ),

													  'columns'        => $columns,
													  'selectableRows' => count($model->search()->data),
												 )) ?>
		</div>
	</div>
<?php
$this->endWidget();