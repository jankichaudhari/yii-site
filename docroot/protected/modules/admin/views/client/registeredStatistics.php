<?php
/**
 * @var $this  ClientController
 * @var $model RegisteredStatistics
 * @var $form  AdminFilterForm
 */
if (isset($_GET['ajax']) && $_GET['ajax']) {
	ob_start();
	$this->widget('AdminFilterForm', Array(
										  'model'          => $model,
										  'ajaxFilterGrid' => 'plot',
										  'id'             => 'plot-filter',
									 ));
	ob_end_clean();
	$data = $model->search();
	$res  = [];
	foreach ($data as $key => $value) {
		$res[$value['year']][$value['period']] = $value['count'];
	}
	$res2 = [];

	foreach ($res as $year => $data) {
		$t         = ['label' => $year];
		$t['data'] = [];

		foreach ($data as $period => $count) {
			$t['data'][] = [$period, (int)$count];
		}
		$res2[] = $t;

	}
	echo json_encode($res2);
	Yii::app()->end();
}
Yii::app()->getClientScript()->registerScriptFile('/js/flot/jquery.flot.js');
$form = $this->beginWidget('AdminFilterForm', Array(
												   'model'          => $model,
												   'ajaxFilterGrid' => 'plot',
												   'id'             => 'plot-filter',
												   //												   'storeInSession' => false,
											  ));
$data = $model->search();
$res = [];
foreach ($data as $key => $value) {
	$res[$value['year']][$value['period']] = $value['count'];
}
$res2 = [];


foreach ($res as $year => $data) {
	$t         = ['label' => $year];
	$t['data'] = [];

	foreach ($data as $period => $count) {
		$t['data'][] = [$period, (int)$count];
	}
	$res2[] = $t;

}


?>
<fieldset>
	<div class="block-header">
		FILTER
	</div>
	<div class="content">
		<div class="control-group">
			<div class="control-label">Start date</div>
			<div class="controls">
				<?php echo $form->textField($model, 'startDate', ['class' => 'datepicker']) ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">end date</div>
			<div class="controls">
				<?php echo $form->textField($model, 'endDate', ['class' => 'datepicker']) ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">Granularity</div>
			<div class="controls">
				<?php echo $form->radioButtonList($model, 'granulation', RegisteredStatistics::getGranularity(), ['separator' => ' ']) ?>
			</div>
		</div>
	</div>
</fieldset>
<?php
$this->endWidget();
?>

<div id="plot" style="height: 400px; "></div>

<script type="text/javascript">
	var data = <?php echo json_encode($res2); ?>;
	var options = {
		series : {
			lines  : { show : true },
			points : { show : true }
		},
		xaxis  : {
			ticks :<?php echo json_encode(range(1,366)) ?>
		}
	};
	var plot = $.plot('#plot', data, options);
	$('.datepicker').datepicker({onSelect : function ()
	{
		$(this).trigger('keyup');
	}});
	;

	AdminFilterForm('plot-filter').attachEvent('onBeforeAjaxFilter', function (_data)
	{
		$.getJSON('/admin4/Client/registeredStatistics/ajax/true', _data, function (result)
		{
//			console.log(result);
			plot = $.plot('#plot', result, options);
		})
		return false;
	});
</script>