<?php
/**
 * @var                  $error
 * @var Array            $items
 *
 */
$result = ['errorCode' => 0, 'errors' => [], 'items' => []];
if(!$error) {
	foreach ($items as $item) {
		$result['items'][] = ['id' => $item['id'], 'value'=> $item['description']];
	}
} else {
	$result['errorCode'] = 1;
	foreach ($error as $key => $value) {
		$result['errors'][] = (string)$value['message'];
	}
}
echo json_encode($result);
return;
