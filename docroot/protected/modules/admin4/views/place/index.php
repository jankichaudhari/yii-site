<?php
/**
 * @var $this         PlaceController
 * @var $dataProvider CActiveDataProvider
 * @var $form         AdminFilterForm
 * @var $model        Place [ ]
 */
?>

<?php
$form = $this->beginWidget('AdminFilterForm', array(
												   'id'                   => 'place-filter-form',
												   'enableAjaxValidation' => false,
												   'model'                => $model,
												   'ajaxFilterGrid'       => 'place-list',
											  ));
?>
<fieldset>
	<div class="block-header">Park Search</div>
	<div class="content">
		<div class="control-group">
			<label class="control-label">Title</label>

			<div class="controls">
				<?php echo $form->textField($model, 'title'); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Address</label>

			<div class="controls">
				<?php echo $form->textField($model, 'address'); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Status</label>

			<div class="controls">
				<?php echo $form->checkBoxList($model, 'statusId', Lists::model()->getList("PublicPlacesStatus"), ['separator' => '']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Type</label>

			<div class="controls">
				<?php echo $form->checkBoxList($model, 'typeId', Lists::model()->getList("PublicPlacesParkType"), ['separator' => '']); ?>
			</div>
		</div>

	</div>
	<div class="block-buttons force-margin"><?php echo $form->filterResetButton('Reset', ['class' => 'btn']) ?></div>
</fieldset>
<?php $this->endWidget(); ?>
<?php
$buttonColumn = array(
	'header'   => 'Action',
	'class'    => 'CButtonColumn',
	'template' => '{preview}{edit}{delete}',
	'buttons'  => array(
		'preview' => array(
			'label'    => 'preview',
			'url'      => function ($data) {

				return $data->id;
			},
			'imageUrl' => Icon::PREVIEW_ICON,
			'click'    => "popUpPreview",
		),
		'edit'    => array(
			'label'    => 'Edit',
			'url'      => function ($data) {

				return $this->createUrl('update', array('id' => $data->id));
			},
			'imageUrl' => Icon::EDIT_ICON,
		),
		'delete'  => array(
			'label'    => 'Delete',
			'url'      => function ($data) {

				return $this->createUrl('delete', array('id' => $data->id));
			},
			'imageUrl' => Icon::CROSS_ICON
		)
	)
);
$this->widget('AdminGridView', array(
									'dataProvider' => $model->search(),
									'id'           => 'place-list',
									'title'        => 'Parks',
									'actions'      => array(
										'add' => array($this->createUrl("Create")),
									),
									'columns'      => array(
										$buttonColumn,
										'title::Park Name',
										'location.postcode::Postcode',
										array(
											'header' => 'Main Image', 'name' => 'mainViewImage',
											'value'  => function ($data) {

												$thisImage = $data->mainViewImage;
												if (isset($thisImage) && count($thisImage)) {
													$fullImagePath = Yii::app()->params['imgUrl'] . "/Place/" . $thisImage['recordId'] . "/" . $thisImage['recordType'] . "/" . $thisImage->smallName;
													echo CHtml::image($fullImagePath, '', array('width' => '75'));
												}
											}
										),
										'placeType.ListItem::Type',
										'statusValue.ListItem::Status',
										$buttonColumn,
									)
							   )); ?>
<script type="text/javascript">
	function popUpPreview() {
		var thisUrl = $(this).attr('href');
		window.open('<?php echo $this->createUrl("../park/") ?>/' + thisUrl, 'popUpWin', 'status=1,scrollbars=1,menubar=1,resizable=1,width=1200,height=1000');
		return false;
	}
</script>