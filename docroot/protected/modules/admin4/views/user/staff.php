<?php
/**
 * @var $this  UserController
 * @var $model User
 */
$this->widget('AdminGridView', array(
									'title'        => 'Staff members',
									'actions'      => array('export'),
									'dataProvider' => $model->search(),
									'columns'      => array(
										'fullname::Fullname',
										'branch.bra_title::Branch',
										'use_mobile::Mobile',
										'use_ext::Ext',
									)
							   ));