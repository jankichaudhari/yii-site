<?php

class CareerController extends PublicController
{
	public $layout = "/layouts/default";

	public function actionIndex()
	{
		$careers = Career::model()->onlyActive()->findAll();
        $model = new PublicCareerForm();

        $applyFormMessages = array();
        if (isset($_POST['PublicCareerForm']) && $_POST['PublicCareerForm']) {
            $applyFormMessages = $this->applyForm($model);
        }

		$this->render('index', array(
            'careers' => $careers,
            'model' => $model,
            'applyFormMessages' => $applyFormMessages
        ));
	}

    /**
     * @param $model PublicCareerForm
     */
    public function applyForm($model){
        $result = array();
        if (isset($_POST['PublicCareerForm']['career']) && $_POST['PublicCareerForm']['career']) {
            $career = $_POST['PublicCareerForm']['career'];
            $model->attributes = $_POST['PublicCareerForm'];
            $model->cv         = CUploadedFile::getInstance($model, "cv");
            if ($model->validate()) {
                $sent = $this->sendMessage($model, Career::model()->onlyActive()->findByPk($career));
                if($sent){
                    $result['type'] = 'success';
                    $result['value'] = 'Thank you for your application...';
                } else {
                    $result['type'] = 'error';
                    $result['value'] = 'Error!! Please try again...';
                }
            } else if($model->errors){
                $result['type'] = 'error';
                $data = '<ul>';
                foreach ($model->errors as $key => $value):
                    $data = $data . '<li>' .  $model->getAttributeLabel($key) . ' : ' . $model->getError($key) . '</li>';
                endforeach;
                $data = $data . '</ul>';
                $result['value'] = $data;
            } else {
                $result['type'] = 'error';
                $result['value'] = 'Error!! Please try again...';
            }
        } else {
            $result['type'] = 'error';
            $result['value'] = 'Please choose apply for';
        }
        return $result;
    }

//	public function actionApply($id)
//	{
//		$this->layout = "/layouts/popup-iframe";
//
//		$model = $id ? Career::model()->onlyActive()->findByPk($id) : new Career();
//        if (!$model) {
//            echo "career not found";
//        }
//		$formModel = new PublicCareerForm();
//
//		$sent      = false;
//		if (isset($_POST['PublicCareerForm']) && $_POST['PublicCareerForm']) {
//
//			$formModel->attributes = $_POST['PublicCareerForm'];
//			$formModel->cv         = CUploadedFile::getInstance($formModel, "cv");
//
//			if ($formModel->validate()) {
//				$sent = $this->sendMessage($formModel, $model);
//			}
//		}
//
//		$this->render("apply", array(
//                                'model'     => $model,
//                                'formModel' => $formModel,
//                                'sent'      => $sent
//            )
//        );
//
//	}

	private function sendMessage(PublicCareerForm $userData, Career $career)
	{
		try {
			include_once("Zend/Mail.php");

			$contactName = $userData->name;

			$staffMessage = "Name: " . $contactName . "\n"
					. "Tel: " . $userData->telephone . "\n"
					. "Email: " . $userData->email . "\n"
					. "Sent: " . date("d/m/Y H:i") . "\n\n"
					. $userData->message;

			$mailToStaff = new Zend_Mail("UTF-8");
			$mailToStaff->addTo($career->email);
//			$mailToStaff->setFrom("careers@woosterstock.co.uk");
//			$mailToStaff->setFrom("zoe.matheson@woosterstock.co.uk");        //zoe email address
			$mailToStaff->setFrom("janki.chaudhari@woosterstock.co.uk");        //zoe email address
			$mailToStaff->setSubject("Your application for '" . $career->name . "'");
			$mailToStaff->setBodyText($staffMessage);
			$mailToStaff->setReplyTo($userData->email);


			$at           = $mailToStaff->createAttachment(file_get_contents($userData->cv->getTempName()));
			$at->filename = $userData->cv->getName();
			$at->type     = $userData->cv->getType();

			$mailToStaff->send();

			$mailToClient = new Zend_Mail('UTF-8');
			$mailToClient->addTo($userData->email);
			$mailToClient->setFrom($career->email);
			$mailToClient->setSubject("Your application for '" . $career->name . "'");
			$mailToClient->setBodyText("Thank you for your application.\n\nIt has been sent to Wooster & Stock");
			$mailToClient->send();

		} catch (Exception $e) {
			return false;
		}
		return true;
	}
}
