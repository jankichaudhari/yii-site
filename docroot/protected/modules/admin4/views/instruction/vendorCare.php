<style type="text/css">
	.saveLastContacted {
		cursor: pointer;
	}
</style>
<?php
/**
 * @var $this         InstructionController
 * @var $dataProvider CSqlDataProvider
 * @var $form         AdminFilterForm
 */
$this->pageTitle = 'Vendor Care';
$form = $this->beginWidget('AdminForm', array(
											 'action' => $this->createUrl('vendorCare'),
											 'id'     => 'vendor-care-filter-form',
											 'method' => 'get'
										));
$activeBranches = CHtml::listData(Branch::model()->active()->findAll(), 'bra_id', 'bra_title');

?>
<fieldset>
	<div class="block-header">Search</div>
	<div class="content">
		<div class="control-group">
			<label class="control-label">
				Branch
				<input type="checkbox" id="branch-trigger" checked="checked">
			</label>

			<div class="controls">
				<?php echo CHtml::checkBoxList('Deal[dea_branch]', isset($_GET['Deal']['dea_branch']) ? $_GET['Deal']['dea_branch'] : array_keys($activeBranches), $activeBranches, [
																																													'class'     => 'branch-checkbox deal_branch',
																																													'separator' => ' '
																																													]);
				?>
			</div>
			<div class="block-buttons force-margin">
				<?php echo CHtml::submitButton('Search', ['class' => 'btn']); ?>
			</div>
		</div>


	</div>
</fieldset>
<?php
$this->endWidget();

$this->widget('AdminGridView', array(
									'title'           => 'Vendor Care - Viewings',
									'id'              => 'vendor-care-list',
									'dataProvider'    => $dataProvider,
									'afterAjaxUpdate' => 'reinstallDatePicker',
									'columns'         => array(
										array(
											'name'   => 'propertyAddress',
											'header' => "Property",
											'value'  => function ($data) {

												$address = $data['propertyAddress'] ? $data['propertyAddress'] : '';
												echo CHtml::link($address, [
																		   'instruction/summary',
																		   'id' => $data['dea_id']
																		   ], ['class' => 'table-link']);
											}
										),
										array(
											'name'   => 'vendorsNames',
											'header' => "Vendor",
											'value'  => function ($data) {

												if ($data['vendorsNames']) {
													$vendors   = explode(',', $data['vendorsNames']);
													$vendorsId = explode(',', $data['vendorsIds']);
													for ($i = 0; $i < count($vendorsId); $i++) {
														echo CHtml::link($vendors[$i], [
																					   'client/update',
																					   'id' => $vendorsId[$i]
																					   ], ['class' => 'table-link']);
														echo ($i + 1 == count($vendorsId)) ? '' : ',';
													}
												} else {
													echo 'No vendor found';
												}
											}
										),
										array(
											'name'   => 'vendorLastContacted',
											'header' => "Last Contacted",
											'value'  => function ($data) {

												if ($data['vendorsIds']) {
													$date     = "";
													$dateLink = "-- -- ----";
													if ($data['vendorLastContacted'] && ($data['vendorLastContacted'] != '0000-00-00 00:00:00')) {
														$date = $dateLink = Date::formatDate('d/m/Y', $data['vendorLastContacted']);
													}
													echo CHtml::textField("Vendor[lastContacted][" . str_replace(',', '_', $data['vendorsIds']) . "]",
																		  $date,
																		  [
																		  'class'       => 'datepicker',
																		  'placeholder' => 'dd/mm/yyyy',
																		  'style'       => 'width:66px;',
																		  'data-id'     => $data['vendorsIds']
																		  ]);
													echo CHtml::label($dateLink, '#', [
																					  'class'   => 'saveLastContacted',
																					  'data-id' => $data['vendorsIds'],
																					  'onclick' => 'saveContactedDt(this.id)',
																					  'id'      => str_replace(',', '_', $data['vendorsIds']),
																					  ]);
												}
											}
										),
										array(
											'name'   => 'totalOffers',
											'header' => "Offers",
											'value'  => function ($data) {

												echo CHtml::link($data['totalOffers'], ['instruction/summary/id/' . $data['dea_id'] . '##offers'], ['class' => 'table-link']);
											}
										),
										array(
											'name'   => 'dea_board',
											'header' => "Board",
											'value'  => function ($data) {

												$boardText = $data['dea_board'] . ' - ' . ($data['dea_boardtype'] ? $data['dea_boardtype'] : "Not specified");
												echo CHtml::link($boardText, ['instruction/summary/id/' . $data['dea_id'] . '##marketingDetails'], ['class' => 'table-link']);
											}
										),
										array(
											'name'   => 'latestViewing',
											'header' => "Last Viewing",
											'type'   => 'raw',
											'value'  => function ($data) {

												if ($data['latestViewing']) {
													return CHtml::link(date("d/m/Y", strtotime($data['latestViewing'])), [
																														 'instruction/latestApp',
																														 'id' => $data['dea_id']
																														 ], ['class' => 'table-link']);
												} else {
													return 'No viewing found';
												}
											}
										),
										array(
											'name'   => 'finishedViewings',
											'header' => "Viewings",
											'value'  => function ($data) {

												echo CHtml::link($data['finishedViewings'], ['instruction/summary/id/' . $data['dea_id'] . '##viewing'], ['class' => 'table-link']);
											}
										),
										array(
											'name'   => 'futureViewings',
											'header' => "Future Viewings",
											'value'  => function ($data) {

												echo CHtml::link($data['futureViewings'], ['instruction/summary/id/' . $data['dea_id'] . '##viewing'], ['class' => 'table-link']);
											}
										),
										array(
											'name'   => 'timeOnMarket',
											'header' => "Time on market",
											'value'  => function ($data) {

												$totalDays = $data['timeOnMarket'];
												$weeks     = 0;
												if ($totalDays > 7) {
													$weeks = floor($totalDays / 7);
												}
												$days = $totalDays - ($weeks * 7);
												return $weeks . " weeks, " . $days . " days";
											}
										),

									),
									'selectableRows'  => 0
							   ));
?>

<script type="text/javascript">
	$('.deal_branch').on('change', function () {
		$('#vendor-care-filter-form').submit();
	});
	$('#branch-trigger').on('change', function () {
		$('.branch-checkbox').attr('checked', $(this).is(':checked'));
	});

	var onSelectDate = function () {
		var thisId = $(this).attr('id');
		if ($(this).datepicker("getDate") > new Date()) {
			alert("Invalid Date !!");
		} else {
			var contactDate = $(this).val();
			var clientIds = $(this).data('id');
			var thisAr = thisId.split("Vendor_lastContacted_");
			var ele = $('label#' + thisAr[1] + '.saveLastContacted');
			$.get('/admin4/client/saveLastContacted', {'contactDate': contactDate, 'clientIds': clientIds }, function (result) {
				if (!result) {
					$(this).val('');
				} else {
					ele.html(result);
					alert('Saved Successfully..');
				}
				ele.show();
			});
		}
	};

	var pickDate = function () {
		$(".datepicker").datepicker({
			showOn: "focus",
			onSelect: onSelectDate
		}).on('blur',function () {
					$(this).hide();
					$(this).next('.saveLastContacted').show();
				}).hide();
	};

	pickDate();
	var reinstallDatePicker = function () {
		pickDate();
	};

	var saveContactedDt = function (thisId) {
		var dtElement = $('#Vendor_lastContacted_' + thisId);
		dtElement.focus();
		$(this).hide();
	};
</script>