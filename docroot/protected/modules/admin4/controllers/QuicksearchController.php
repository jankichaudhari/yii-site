<?php

class QuicksearchController extends AdminController
{

	public function actionIndex()
	{
		if (Yii::app()->request->isAjaxRequest) {
			header('Content-Type: text/json');
		}

		$data = [];
		if (isset($_GET['search']) && $_GET['search']) {
			if (is_numeric($_GET['search'])) {
				$data = $this->getDataById($_GET['search']);
			} else {
				$data = $this->getDataBySearch($_GET['search']);
			}

		}

		echo json_encode($data);

	}

	private function getDataById($id)
	{
		$data   = [];
		$client = Client::model()->findByPk($id);
		if ($client) {
			$data[] = ['label' => 'Client: ' . $client->getFullName(), 'value' => $client->getFullName(), 'url' => $this->createUrl('client/update', ['id' => $client->cli_id])];
		}

		$instruction = Deal::model()->findByPk($id);
		if ($instruction) {
			$data[] = [
				'label' => 'Instruction: ' . $instruction->title, 'value' => $instruction->title, 'url' => $this->createUrl('instruction/summary', ['id' => $instruction->dea_id])
			];
		}

		/** @var Appointment $appointment */
		$appointment = Appointment::model()->with('user')->findByPk($id);
		if ($appointment) {
			$data[] = [
				'label' => $appointment->app_type . ': ' . Date::formatDate('d/m H:i', $appointment->app_start) . ' ' . $appointment->user->getFullName(),
				'value' => $instruction->title,
				'url'   => $this->createUrl('instruction/update', ['id' => $instruction->dea_id])
			];
		}
		return $data;
	}

	private function getDataBySearch($search)
	{
		$data = [];

		$clients = Client::model()->quickSearch($search);
		foreach ($clients as $key => $client) {
			$data[] = ['label' => $client->getFullName(), 'value' => $client->getFullName(), 'url' => $this->createUrl('client/update', ['id' => $client->cli_id])];
		}

		$instructions = Deal::model()->quickSearch($search);

		foreach ($instructions as $key => $instr) {
			$title  = $instr->address->line1 . ' ' . $instr->title;
			$data[] = [
				'label' => $title, 'value' => $title, 'url' => $this->createUrl('instruction/summary', ['id' => $instr->dea_id])
			];
		}
		
		return $data;

	}

}
