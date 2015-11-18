<?php
include_once __DIR__ . '/bootstrap.php';
/**
 * Class ActiveRecordTest
 * Abstract class to test active record models.
 * May incapsulate some logic that is very common for all models
 */
abstract class ActiveRecordTest extends CDbTestCase
{
	/**
	 * @param string $scenario
	 * @return CActiveRecord
	 */
	abstract protected function getModel($scenario = 'insert');

	/**
	 * this method is going to fail in case if you forget to add attribute label for some particular attribute.
	 * Usualy this happens when you add new field to database.
	 * in that case you will know that test fails and you need to reconsider your model to work with new field
	 * i.e. set up rules and define business logic.
	 */
	public function testAllAttributesHaveLabels()
	{

		$model  = $this->getModel();
		$labels = $model->attributeLabels();
		foreach ($model->attributes as $name => $value) {
			$this->assertArrayHasKey($name, $labels, "label for attribute " . $name . " is not set in " . get_class($model) . " Active Record Model");
		}

	}

	/**
	 * @return CActiveRecord
	 */
	public function testSearch()
	{

		$model        = $this->getModel('search');
		$dataProvider = $model->search();
		$this->assertInstanceOf('CActiveDataProvider', $dataProvider, "CActiveRecord::search method must return CActiveRecord");
		return $model;
	}
}