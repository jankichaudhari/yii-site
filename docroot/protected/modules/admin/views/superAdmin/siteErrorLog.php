<?php
/**
 * @var $this         SuperAdminController
 * @var $dataProvider CArrayDataProvider
 * @var $files        String[]
 */
?>
<table>
	<?php foreach ($files as $key => $value): ?>
		<tr>
			<td><?php echo CHtml::link(basename($value), ['SuperAdmin/siteLogDetails', 'file' => basename($value)]) ?></td>
		</tr>
	<?php endforeach; ?>
</table>
