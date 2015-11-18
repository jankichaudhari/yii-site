<?php
/**
 * @var $model        QuickReport
 * @var $dataProvider CSqlDataProvider
 */
$this->pageTitle = $model->title;

$this->widget('AdminGridView', array(
									'id'               => 'quickReportTable',
									'title'            => $model->title,
									'actions'          => array('export'),
									'dataProvider'     => $dataProvider,
									'htmlOptions'      => array()
							   ));

?>
<?php if($model->actionLink) : ?>
<script type="text/javascript">
	$("#quickReportTable").on("dblclick", "tr", function ()
	{
		var columns = document.getElementById("quickReportTable").getElementsByTagName("TR")[0].cells;
		var c = columns.length;
		for (var i = 0; i < c; i++) {
			if(columns[i].innerHTML == '<?php echo $model->keyField ?>') {
				document.location.href = ('<?php echo $model->actionLink ?>').replace("{<?php echo $model->keyField ?>}", this.cells[i].innerHTML);
			}
		}
	})
</script>
<?php endif ?>