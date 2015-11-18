<?php
Yii::import("zii.widgets.grid.CGridView");
/**
 *
 */
class AdminGridView extends CGridView
{
	/**
	 * @var string
	 */
	public $title = '';
	/**
	 * @var array
	 */
	public $actions = array();
	/**
	 * if true means we are exporting data to some other format, at the moment csv is only implemented format
	 * @var bool
	 */
	protected $export = false;
	/**
	 * @var string
	 */
	public $template = '{summary} {pager} {items} {summary} {pager}';

	public $newLayout = false;

	public $summaryText = '{start}-{end} of {count}';

	/**
	 * @param null $owner
	 */
	public function __construct($owner = null)
	{
		$this->pager['header'] = '';
		parent::__construct($owner);
	}

	/**
	 * renders all actions such as export.
	 *
	 * if method render$actionName$Action exists calls it to render action
	 * otherwise outputs contents of action.
	 *
	 * whole output is wrapped in <div class="block-buttons"> tag
	 */
	public function renderActions()
	{

		if ($this->actions) {
			ob_start();
			foreach ($this->actions as $key => $action) {
				$params = array();
				if (is_array($action)) {
					$params = $action;
					$action = $key;
				}
				$methodName = 'render' . $action . 'Action';
				if (method_exists($this, $methodName)) {
					call_user_func_array(array($this, $methodName), $params);
				} else {
					echo $action;
				}
			}
			$actions = ob_get_clean();
			echo '<div class="block-buttons">' . $actions . '</div>';
		}
	}

	/**
	 * renders export button
	 */
	public function renderExportAction()
	{

		echo CHtml::link('Export to Excel', array(
												 "",
												 $this->getId() => array('export' => 'csv')
											), array('class' => 'btn'));
	}

	public function renderAddAction($link, $title = 'Add new item', $attributes = array())
	{

		$attributes['class'] = 'btn btn-green';
		echo $this->displayLink($link, $title, $attributes);

	}

	/**
	 *
	 */
	public function renderTitle()
	{

		if ($this->title) {
			echo '<div class="block-header grey">' . $this->title . '</div>';
		}
	}

	/**
	 * @param        $link
	 * @param string $title
	 * @param array  $attributes
	 */
	public function renderButtonAction($link, $title = 'button', $attributes = array())
	{

		$attributes['class'] = 'btn';
		echo $this->displayLink($link, $title, $attributes);

	}

	private function displayLink($link, $title = '', $attributes = array())
	{

		$htmlOptions          = array();
		$htmlOptions['title'] = $title;
		foreach ($attributes as $key => $val) {
			$htmlOptions[$key] = $val;
		}

		return CHtml::link($title, $link, $htmlOptions);
	}

	/**
	 *
	 */
	public function init()
	{

		if ($this->dataProvider->getPagination() instanceof CPagination) {
			$oldPagination                                            = $this->dataProvider->getPagination();
			$this->dataProvider->pagination                           = new AdminPagination($this->dataProvider->getPagination()->getItemCount());
			$this->dataProvider->getPagination()->pageSize            = $oldPagination->pageSize;
			$this->dataProvider->getPagination()->pageVar             = $oldPagination->pageVar;
			$this->dataProvider->getPagination()->params              = $oldPagination->params;
			$this->dataProvider->getPagination()->validateCurrentPage = $oldPagination->validateCurrentPage;
		}

		if (isset($_GET[$this->getId()]['export'])) {
			$this->export                   = true;
			$this->template                 = '{items}';
			$this->dataProvider->pagination = false;
		}

		if (!$this->cssFile) {
			$this->cssFile = Yii::app()->getBaseUrl(true) . "/css/grid-view/style.css";
		}
		if (!isset($this->pager['cssFile']) || !$this->pager['cssFile']) {
			$this->pager['cssFile'] = Yii::app()->getBaseUrl(true) . "/css/pager.css";
		}

		parent::init();
	}

	/**
	 *
	 */
	public function run()
	{

		if ($this->export) {
			ob_end_clean(); // any output before should be ignored

			ob_start();
			$this->renderContent();
			$data = ob_get_contents();
			ob_end_clean();

			$filename = Yii::app()->params['tmpDirPath'] . "/export" . str_replace(" ", "_", $this->title) . "_" . date("dmY") . ".csv";
			/** @var $file CFile */
			$file = Yii::app()->file->set($filename);
			$file->create();
			$file->setMimeType("application/vnd.ms-excel");
			$file->setContents(print_r($data, true))->download();
			Yii::app()->end();
		} else {
			if ($this->newLayout) {

			} else {
			}

			echo '<fieldset class="white bordered">';
			$this->renderTitle();
			$this->renderActions();
			parent::run();
			echo '</fieldset>';

		}

		$session = Yii::app()->session;
		$sorter  = $this->dataProvider->getSort();
		$key     = $this->getId() . "_" . $sorter->sortVar;
		if (isset($_GET[$sorter->sortVar])) {
			$session->add($key, serialize($sorter));
		}

	}

	/**
	 *
	 */
	protected function initColumns()
	{

		if ($this->export) {
			if ($this->columns === array()) {
				if ($this->dataProvider instanceof CActiveDataProvider) {
					$this->columns = $this->dataProvider->model->attributeNames();
				} else {
					if ($this->dataProvider instanceof IDataProvider) {
						// use the keys of the first row of data as the default columns
						$data = $this->dataProvider->getData();
						if (isset($data[0]) && is_array($data[0])) {
							$this->columns = array_keys($data[0]);
						}
					}
				}
			}
			$id = $this->getId();
			foreach ($this->columns as $i => $column) {
				if (is_string($column)) {
					$column = $this->createDataColumn($column);
				} else {
					if (!isset($column['class'])) {
						$column['class'] = 'AdminExportColumn';
					} else {
						unset($this->columns[$i]);
						continue; // we cannot export any non data columns
					}
					$column = Yii::createComponent($column, $this);
				}
				if (!$column->visible) {
					unset($this->columns[$i]);
					continue;
				}
				if ($column->id === null) {
					$column->id = $id . '_c' . $i;
				}
				$this->columns[$i] = $column;
			}

			foreach ($this->columns as $column) {
				$column->init();
			}
		} else {
			if (Yii::app()->user->is('SuperAdmin')) {
				/** @var $cs CClientScript */
				$cs = Yii::app()->getClientScript();
				$cs->registerScriptFile('/js/AdminGridView.superadmin.js', CClientScript::POS_END);
				$this->columns[] = array(
					'type'  => 'raw',
					'value' => function (CActiveRecord $data) {
								return CHtml::link(CHtml::image(Icon::INFO_ICON, 'attributes'), array(
																									 'superAdmin/viewRecord',
																									 'model' => get_class($data),
																									 'pk'    => $data->getPrimaryKey(),
																								), array(
																										'class' => 'grid-superadmin-info',
																										'title' => print_r($data->getAttributes(), true)
																								   ));
								return;
							}
				);
			}
			parent::initColumns();
		}
	}

	/**
	 * @param string $text
	 * @return AdminExportColumn|CDataColumn
	 * @throws CException
	 */
	protected function createDataColumn($text)
	{

		if ($this->export) {
			if (!preg_match('/^([\w\.]+)(:(\w*))?(:(.*))?$/', $text, $matches)) {
				throw new CException(Yii::t('zii', 'The column must be specified in the format of "Name:Type:Label", where "Type" and "Label" are optional.'));
			}
			$column       = new AdminExportColumn($this);
			$column->name = $matches[1];
			if (isset($matches[3]) && $matches[3] !== '') {
				$column->type = $matches[3];
			}
			if (isset($matches[5])) {
				$column->header = $matches[5];
			}
			return $column;
		} else {
			return parent::createDataColumn($text);
		}

	}

	/**
	 *
	 */
	public function renderItems()
	{

		if ($this->export) {
			$export = array();
			foreach ($this->columns as $column) {
				/** @var $column CDataColumn */
				$export['headers'][] = $column->header ? $column->header : $column->name;
			}
			$data = $this->dataProvider->getData();
			foreach ($data as $row => $line) {
				foreach ($this->columns as $column) {
					$export[$row][] = '"' . str_replace('"', '""', $column->renderDataCell($row)) . '"';
				}
			}

			echo implode(",", $export['headers']) . "\n";
			unset($export['headers']);
			foreach ($export as $key => $value) {
				echo implode(",", $value) . "\n";
			}

		} else {
			parent::renderItems();
		}
	}

}
