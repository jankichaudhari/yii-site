<?php
/**
 * @var $offerClientList Deal Owners OR deal tenants
 * @ var $clientStatusType
 * @ var $instructionType
 */

ob_start();
$data = '';
if ($offerClientList) {
	$data = $data . '<table cellpadding="2 5 2 5">';
	foreach ($offerClientList as $offerClient) {
		$data = $data . '<tr><td>';
		if (!empty($offerClient->c2o_cli)) {
			$client = Client::model()->findByPk($offerClient->c2o_cli);
			$data   = $data . CHtml::link($client->fullName, Yii::app()->createUrl('admin4/client/update', ['id' => $offerClient->c2o_cli]));
		}
		$data = $data . '</td><td>';
		$data = $data . CHtml::dropDownList('Offer[clientStatus][' . $client->cli_id . ']', $client->$clientStatusType, [0 => ''] + CHtml::listData(ClientStatus::model()->findAll(array('scopes' => array($instructionType))), 'cst_id', 'cst_title'), ['class' => 'input-xsmall']);
		$data = $data . '</td><td>';
		$data = $data . CHtml::link(
					CHtml::image(Yii::app()->params["imgUrl"] . "/sys/admin/icons/cross-icon.png", "Delete"),
					"#",
					["onClick" => "deleteClient(" . $offerClient->c2o_id . ")"]
				);
		$data = $data . '</td></tr>';
	}
	$data = $data . '</table>';
}
echo json_encode(array('html' => $data));
?>