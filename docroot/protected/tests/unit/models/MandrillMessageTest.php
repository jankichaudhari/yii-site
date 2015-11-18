<?php
require_once dirname(__FILE__) . '/bootstrap.php';
class MandrillMessageTest extends ActiveRecordTest
{
	/**
	 *
	 */
	public function testSetOptionThrowsExceptionOnWrongOptionValue()
	{
		$this->setExpectedException('InvalidArgumentException');
		$model = $this->getModel();
		$model->setOption('Fernando Magelan', 'The Sailor');
	}

	public function testSetOptionMergeVarsSetsUpSeparateArray()
	{
		$model = $this->getModel();
		$model->setOption(MandrillMessage::KEY_MERGE_VARS, array(
																array(
																	'rcpt' => 'recipient.email@example.com',
																	'vars' => array(
																		array(
																			'name'    => 'merge2',
																			'content' => 'merge2 content'
																		)
																	)
																)
														   ));
		$this->assertEquals(array(
								 'recipient.email@example.com' => array(
									 array(
										 'name'    => 'merge2',
										 'content' => 'merge2 content'
									 )
								 )
							), $model->getMergeVars());

		$model = $this->getModel();
		$model->setOption(MandrillMessage::KEY_MERGE_VARS, array(
																array(
																	'rcpt' => 'recipient.email@example.com',
																	'vars' => array(
																		array(
																			'name'    => 'merge2',
																			'content' => 'merge2 content'
																		), array(
																			'name'    => 'merge3',
																			'content' => 'merge3 content'
																		)
																	)
																)

														   ));
		$this->assertEquals(array(
								 'recipient.email@example.com' => array(
									 array(
										 'name'    => 'merge2',
										 'content' => 'merge2 content'
									 ),
									 array(
										 'name'    => 'merge3',
										 'content' => 'merge3 content'
									 )
								 )
							), $model->getMergeVars());
	}

	public function testSetMergeVars()
	{
		$model = $this->getModel();
		$model->setRecepientMergeVar('recipient.email@example.com', 'merge2', 'merge2 content');
		$this->assertEquals(array(
								 'recipient.email@example.com' => array(
									 array(
										 'name'    => 'merge2',
										 'content' => 'merge2 content'
									 )
								 )
							), $model->getMergeVars());

		$model->setRecepientMergeVar('recipient.email@example.com', 'merge3', 'merge3 content');
		$this->assertEquals(array(
								 'recipient.email@example.com' => array(
									 array(
										 'name'    => 'merge2',
										 'content' => 'merge2 content'
									 ),
									 array(
										 'name'    => 'merge3',
										 'content' => 'merge3 content'
									 )
								 )
							), $model->getMergeVars());
	}

	public function testGetMessage()
	{
		$model = $this->getModel();
		$model->setRecepientMergeVar('recipient.email@example.com', 'merge2', 'merge2 content');

		$this->assertEquals(array(
								 'html'                      => '',
								 'text'                      => '',
								 'subject'                   => '',
								 'from_email'                => null,
								 'from_name'                 => '',
								 'to'                        => [],
								 'headers'                   => [],
								 'important'                 => false,
								 'track_opens'               => null,
								 'track_clicks'              => null,
								 'auto_text'                 => null,
								 'auto_html'                 => null,
								 'inline_css'                => null,
								 'url_strip_qs'              => null,
								 'preserve_recipients'       => null,
								 'view_content_link'         => null,
								 'bcc_address'               => null,
								 'tracking_domain'           => null,
								 'signing_domain'            => null,
								 'return_path_domain'        => null,
								 'merge'                     => null,
								 'global_merge_vars'         => [],
								 'merge_vars'                => array(
									 array(
										 'rcpt' => 'recipient.email@example.com',
										 'vars' => array(
											 array(
												 'name'    => 'merge2',
												 'content' => 'merge2 content'
											 )
										 )
									 )
								 ),
								 'tags'                      => [],
								 'subaccount'                => null,
								 'google_analytics_domains'  => [],
								 'google_analytics_campaign' => null,
								 'metadata'                  => [],
								 'recipient_metadata'        => [],
								 'attachments'               => [],
								 'images'                    => []
							), $model->getMessage());
	}

	/**
	 * @param string $scenario
	 * @return MandrillMessage
	 */
	protected function getModel($scenario = 'insert')
	{
		return new MandrillMessage($scenario);
	}

	public function testSend()
	{
		$model = $this->getModel();

		$model->addTo('luke@woosterstock.co.uk');
		$model->addTo('julia.russell@woosterstock.co.uk');
		$model->addTo('gemma@woosterstock.co.uk');
		$model->addTo('zoe.matheson@woosterstock.co.uk');
		$model->addTo('thanh@woosterstock.co.uk');
		$model->addTo('luke.bateman@woosterstock.co.uk');
		$model->addTo('luke.bishop@woosterstock.co.uk');
		$model->addTo('robert@woosterstock.co.uk');
		$model->addTo('stephen@woosterstock.co.uk');
		$model->addTo('paul.scannell@woosterstock.co.uk');
		$model->addTo('vitaly.suhanov@woosterstock.co.uk');
		$model->addTo('aimee.fancourt@woosterstock.co.uk');
		$model->addTo('rufus.eyrevarnier@woosterstock.co.uk');
		$model->addTo('zack.hill@woosterstock.co.uk');
		$model->addTo('janki.chaudhari@woosterstock.co.uk');
		$model->addTo('ali.devlin@woosterstock.co.uk');
		$model->addTo('patrick.brown@woosterstock.co.uk');
		$model->addTo('jade.woollacott@woosterstock.co.uk');
		$model->addTo('manuela.batas@woosterstock.co.uk');
		$model->addTo('fritz.clarke@woosterstock.co.uk');
		$model->addTo('dominique.spens@woosterstock.co.uk');
		$model->addTo('molly@woosterstock.co.uk');
		$model->addTo('charlie@woosterstock.co.uk');
		$model->addTo('nita@woosterstock.co.uk');
		$model->addTo('millie.dean@woosterstock.co.uk');
		$model->addTo('aggie.hedley@woosterstock.co.uk');

		$model->setRecepientMergeVar('vitaly.suhanov@woosterstock.co.uk', 'FNAME', 'Vitaly');
		$model->setRecepientMergeVar('luke@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('julia.russell@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('gemma@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('zoe.matheson@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('thanh@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('luke.bateman@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('luke.bishop@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('robert@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('stephen@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('paul.scannell@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('vitaly.suhanov@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('aimee.fancourt@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('rufus.eyrevarnier@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('zack.hill@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('janki.chaudhari@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('ali.devlin@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('patrick.brown@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('jade.woollacott@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('manuela.batas@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('fritz.clarke@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('dominique.spens@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('molly@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('charlie@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('nita@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('millie.dean@woosterstock.co.uk', 'FNAME', 'User 1');
		$model->setRecepientMergeVar('aggie.hedley@woosterstock.co.uk', 'FNAME', 'User 1');

		$model->setSubject('Please forward this message to vitaly@woosterstock.co.uk');
		$model->setHtmlBody('<!DOCTYPE html>
		<html>
		<head>
			<title>HTML5 page</title>
			<style type="text/css">
				.italic {
					font-style : italic;
					margin     : 30px;
				}
			</style>
		</head>
		<body>
		<h1>Hello *|FNAME|*</h1>

		<p>I am testing new mailshot system and need to check how messages are delivered to multiple adressses</p>

		<div style="color: #770500;">I am also trying to test how messages are formated</div>
		<span style="display: block">I would be verry happy if you could forward this message to me — <a href="mailto:vitaly@woosterstock.co.uk">Vitaly</a></span>

		<div>There is a little chance that you will receive this particular email multiple times; if you do, I am sorry that is not how it intended to work</div>
		<div class="italic">
			Sorry for any inconvenience <br />
			Regards, <br />
			Vitaly Suhanov
		</div>
		<img src="http://www.woosterstock.co.uk/images/sys/wooster-stock-logo.png" alt="Wooster and stock logo" />
		</body>
		</html>
		');
		print_r($model->send());

	}

	function testInsert2kRecords()
	{
		Yii::app()->db->createCommand('TRUNCATE TABLE wstest.mandrillEmail')->execute();

		$sql = [];
		for ($i = 0; $i < 2400; $i++) {
			$sql[] = '("exampleaaaaaaaaaa' . $i . '", 1, "queued", "' . date("Y-m-d H:i:s") . '", 0, 0, "' . date("Y-m-d H:i:s") . '")';
		}

		$command = "INSERT INTO wstest.mandrillEmail (id, messageId, status, sent, opened, clientId, created) VALUES " . implode(', ', $sql);
//		echo $command;
		Yii::app()->db->createCommand($command)->execute();
	}
}
