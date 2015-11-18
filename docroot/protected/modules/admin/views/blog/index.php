<?php
/**
 * @var $this  BlogController
 * @var $model Blog
 */
$this->beginWidget('AdminFilterForm', array());
?>
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
				<div class="block-header">Search Blog Articles</div>
				<div class="content">
				</div>
				<div class="block-buttons">
					<?php echo CHtml::link('Create Post', ['blog/create'], ['class' => 'btn btn-large']) ?>
					<?php echo CHtml::link('Preview all posts', ['/blog/index', 'preview' => true], ['class' => 'btn btn-large']) ?>
				</div>
			</fieldset>
		</div>
	</div>

<?php
$this->endWidget();
$this->widget('AdminGridView', array(
		'dataProvider' => $model->search(),
		'columns'      => array(
				array(
						'class'    => 'CButtonColumn',
						'template' => '{edit}',
						'buttons'  => array(
								'edit' => array(
										'imageUrl' => Icon::EDIT_ICON,
										'url'      => function (Blog $data) {
													return $this->createUrl('update', ['id' => $data->id]);
												}
								)
						)

				),
				'id',
				'title',
				array(
						'name'   => 'created',
						'header' => 'Created',
						'value'  => function (Blog $data) {
									return Date::formatDate('d/m/Y H:i', $data->created);
								}
				),
				'creator.fullName::Created By',
				'status',
		)
));
?>