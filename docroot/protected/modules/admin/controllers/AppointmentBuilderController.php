<?php
class AppointmentBuilderController extends AdminController
{
	public $layout = "//layouts/adminDefault";

	public function actionSelectClient()
	{

		AppointmentBuilder::start();
		AppointmentBuilder::getCurrent()->setUser(Yii::app()->user->id);

		$availableTypes  = ['viewing', 'valuation'];
		$appointmentType = isset($_GET['for']) && in_array($_GET['for'], $availableTypes) ? $_GET['for'] : reset($availableTypes);
		$appointmentDate = isset($_GET['date']) && $_GET['date'] ? Date::formatDate("Y/m/d", $_GET['date']) : date("Y/m/d");

		$model                = new Client('search');
		$model->cli_created   = '';
		$model->cli_saleemail = '';
		$model->telephones    = [new Telephone('search')];

		AppointmentBuilder::getCurrent()->setDate($appointmentDate);
		AppointmentBuilder::getCurrent()->setType($appointmentType);
		AppointmentBuilder::getCurrent()->setInstructionId(isset($_GET['instructionId']) ? $_GET['instructionId'] : null);

		$this->render('selectClient', compact('model', 'appointmentDate', 'appointmentType'));
	}

	/**
	 * after client is selected set it to builder and decide what to do next.
	 *
	 * @param $clientId
	 */
	public function actionClientSelected($clientId)
	{

		$builder = AppointmentBuilder::getCurrent();
		$builder->setClientId($clientId);
		if ($builder->getType() == AppointmentBuilder::TYPE_VALUATION) {
			$this->redirect(['AppointmentBuilder/SelectProperty']);
		} else {
			$this->redirect('/v3.0/live/admin/viewing_add.php?' . http_build_query(array(
																						'date'   => $builder->getDate(),
																						'dest'   => $builder->getType(),
																						'cli_id' => $clientId,
																						'dea_id' => $builder->getInstructionId(),
																				   )));
		}
	}

	/**
	 * Step 2
	 *
	 * @throws CHttpException
	 * @return void
	 * @internal param $clientId
	 */
	public function actionSelectProperty()
	{

		$clientId = AppointmentBuilder::getCurrent()->getClientId();
		/**
		 * @var $client Client
		 */
		$client = Client::model()->findByPk($clientId);

		if (!$client) {
			throw new CHttpException('404', 'Client [id: ' . $clientId . '] is not found');
		}
		$this->render('selectProperty', ['model' => $client]);

	}

	public function actionSearchProperty()
	{

		$owner = AppointmentBuilder::getCurrent()->getClientId();

		$model = new Property('search');
		$this->render('searchProperty', ['model' => $model]);
	}

	/**
	 * After property is selected
	 */
	public function actionPropertySelected($propertyId)
	{

		$builder = AppointmentBuilder::getCurrent();
		$builder->setPropertyId($propertyId);
		$this->redirect(['AppointmentBuilder/SelectInstruction']);
	}

	public function actionSelectInstruction()
	{

		$builder = AppointmentBuilder::getCurrent();

		if (isset($_POST['Deal']) && $_POST['Deal']) { // need to finish instruction info;

			$instruction = Deal::model()->findByPk($builder->getInstructionId());

			$instruction->attributes = $_POST['Deal'];

			if ($instruction->save()) {
				$this->redirect(['InstructionSelected', 'instructionId' => $instruction->dea_id]);
			} else {
				echo "<pre style='color:blue' title='" . __FILE__ . "'>" . basename(__FILE__) . ":" . __LINE__ . "<br>";
				print_r($instruction->getErrors());
				echo "</pre>";

			}

			$this->render('createInstruction', ['model' => $instruction]);
			return;
		}

		/** @var $property Property */
		$property = Property::model()->findByPk($builder->getPropertyId());

		if (!$property->instructions || isset($_GET['new'])) { // need to create one.
			$instruction             = new Deal();
			$instruction->dea_prop   = $property->pro_id;
			$instruction->dea_status = Deal::STATUS_VALUATION;
			$instruction->importFromProperty($property);
			$instruction->save(false);
			$builder->setInstructionId($instruction->dea_id);

			$this->render('createInstruction', ['model' => $instruction]);
			return;
		}

		$this->render('selectInstruction', ['model' => $property]);
	}

	public function actionInstructionSelected($instructionId)
	{

		$builder = AppointmentBuilder::getCurrent();
		$builder->setInstructionId($instructionId);

		if ($builder->getType() == AppointmentBuilder::TYPE_VALUATION) {
			$query = http_build_query([
									  'stage'      => 'appointment',
									  'pro_id'     => $builder->getPropertyId(),
									  'cli_id'     => $builder->getClientId(),
									  'dea_id'     => $builder->getInstructionId(),
									  'dea_status' => 'Valuation',
									  'dea_type'   => '',
									  'date'       => $builder->getDate(),
									  ]);
			$this->redirect('/v3.0/live/admin/valuation_add.php?' . $query);
		} else {
			$query = http_build_query([
									  'stage'  => 'appointment',
									  'pro_id' => $builder->getPropertyId(),
									  'cli_id' => $builder->getClientId(),
									  'dea_id' => $builder->getInstructionId(),
									  'date'   => $builder->getDate(),
									  ]);
			$this->redirect('/v3.0/live/admin/viewing_add.php?' . $query);
		}

	}

	public function filters()
	{

		return CMap::mergeArray(parent::filters(), [['application.components.AppointmentBuilder']]);
	}

}