<div class="detail-box-listings career-details">
<?php
/**
 * @var          $this         CController
 * @var $careers  Career
 * @var $model PublicCareerForm
 * @var $applyFormMessages
 */
$this->pageTitle = 'Careers';
?>
<div class="page-top-block career">
    <div class="page-content">
		<div class="row">
			<div class="span8 cell-right apply-now-widget">
				<?php $this->renderPartial("apply-form-horizontal", ['model' => $model, 'messages' => $applyFormMessages]); ?>
			</div>
		</div>
    </div>
</div>

<div class="body">
    <div class="page-content careers">

        <div class="row margin-bottom"></div>
        <div class="row margin-bottom">
            <div class="span4 orange-big-heading">
                Come And Work With Us
            </div>
            <div class="span8 grey-heading-text">
                <p>Wooster & Stock is always interested in talking to talented and hardworking people who want to help give our clients a brilliant experience.</p>
                <p>We are a dynamic team of individuals from many different industry backgrounds, all committed to the Wooster & Stock culture of excellent service and teamwork.</p>
                <p>If you think you might like to join us please email a CV along with a covering letter to us to the email address on the right.</p>
            </div>
        </div>

        <div class="orange-big-separator"></div>

        <div class="row margin-bottom">

            <div class="span8 listings">
                <?php foreach ($careers as $career) : ?>
                <div class="full-details white-bg with-shadow">
                    <div class="wrap-description">
                    <div class="inner-description">
                        <div class="info-block description">
                            <div class="header"><?php echo $career->name ?></div>
                            <div class="details">
                                <?php echo $career->description ?>
                            </div>
                        </div>
                        <div class="info-block skills">
                            <div class="header">Skills</div>
                            <div class="details">
                                <?php echo $career->skillsRequired ?>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="span4 additional-widgets">
                <div class="info-box white-bg top-border-orange with-shadow">
                    <?php $this->widget("application.components.public.widgets.ContactUs.ContactUs") ?>
                </div>
            </div>
        </div>

    </div>
</div>