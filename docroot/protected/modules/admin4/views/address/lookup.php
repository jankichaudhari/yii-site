<?php
/**
 * @var                  $error
 * @var Array            $items
 *
 */
$result = ['errorCode' => 0, 'errors' => [], 'items' => []];
//if(!$error) {
//	foreach ($items as $item) {
//		$result['items'][] = ['id' => $item['id'], 'value'=> $item['description']];
//	}
//} else {
//	$result['errorCode'] = 1;
//	foreach ($error as $key => $value) {
//		$result['errors'][] = (string)$value['message'];
//	}
//}
//echo json_encode($result);
//return;

ob_start();
$errorList = '';
if (!$error) {
	$data = '<div class="row"><label>Select Address</label><select id="addressLookupSelector" size="10" style="width:400px;">';
	foreach ($items as $item) {
		$data .= '<option value="' . $item['id'] . '">' . $item['description'] . '</option>';
	}
	$data .= '</select></div>';
	$data .= '<div class="row"><label>&nbsp;</label><input type="button" id="selectAddresFromLookupBtn" value="use selected address" style="margin-top:20px;"><img src="/images/loading.gif" id="useSelAddLoading" style="margin: 0px 0px 0px 5px;display: none;" alt="Loading"></div>';
	echo $data;
} else {
	$errorList = '<ul>';
	foreach ($error as $key => $value) {
		$errorList .= '<li>' . $value['message'] . '</li>';
	}
	$errorList .= '</ul>';
}
?>

<?php $data = ob_get_clean() ?>
<?php //echo $data; exit;?>
<?php echo json_encode(array('html'  => $data,
							 'error' => $errorList)) ?>