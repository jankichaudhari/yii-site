<?php
/**
 * @var $dealChangeLog Deal Notes
 * @var $noteType
 */
$changes = count($dealChangeLog);
?>
<fieldset>
	<div class="block-header">Change History</div>
	<div class="content">
		<div class="row-fluid">
			<div class="span6">
				<table class="small-table" style="width:100%;">
					<tr>
						<th>#</th>
						<th>Date</th>
						<th>User</th>
						<th>Old Value</th>
						<th>New Value</th>
						<th></th>
					</tr>
					<?php foreach ($dealChangeLog as $key => $history): ?>
						<tr>
							<td><?php echo $changes-- ?></td>
							<td><?php echo date('d/m/Y H:i', strtotime($history->cha_datetime)) ?></td>
							<td><?php echo $history->creator->fullName ?></td>
							<td><?php echo $history->cha_old ?></td>
							<td><?php echo $history->cha_new ?></td>
						</tr>
					<?php endforeach; ?>

				</table>
			</div>
		</div>
	</div>
</fieldset>