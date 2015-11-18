<?php

Yii::import('zii.widgets.grid.CDataColumn');
class AdminExportColumn extends CDataColumn {
	public function renderDataCell($row)
	{
		ob_start();
		$this->renderDataCellContent($row, $this->grid->dataProvider->data[$row]);
		return ob_get_clean();
	}

}