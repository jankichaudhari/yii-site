<?php
/**
 *
 * @package application.LocalEvent.views
 *
 * @var LocalEvent    $model
 * @var CActiveForm   $form
 * @var               $this LocalEventController
 * @var Location       $address
 * @var Image[]       $images
 */
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/ckeditor/ckeditor.js');
?>
<div class="form wide">
	<?php $form = $this->beginWidget('CActiveForm', array(
														 'id'                  => 'local-event-form',
														 'enableAjaxValidation'=> false,
														 'htmlOptions'         => array('enctype' => 'multipart/form-data'),
													)); ?>
	<fieldset>
		<div class="block-header"><?php echo $model->isNewRecord ? "Create new Local Event" : "Update Local Event" ?></div>
		<?php echo $form->errorSummary(array($model, $address)); ?>
		<p class="note">Fields with <span class="required">*</span> are required.</p>
		<table style="width:100%">
			<tr>
				<td style="width:50%; vertical-align: top;">
					<h3>Events summary</h3>

					<div class="row">
						<?php echo $form->labelEx($model, 'heading'); ?>
						<?php echo $form->textField($model, 'heading', array('size'     => 80,
																			 'maxlength'=> 255)); ?>
					</div>
					<div class="row">
						<?php echo $form->labelEx($model, 'url'); ?>
						<?php echo $form->textField($model, 'url', array('size'     => 80,
																		 'maxlength'=> 255)); ?>
					</div>

					<div class="row">
						<?php echo $form->labelEx($model, 'strapline'); ?>
						<?php echo $form->textArea($model, 'strapline', array('rows'     => 3,
																			  'cols'     => 60)); ?>
					</div>


					<div class="row">
						<?php echo $form->labelEx($model, 'description'); ?>

						<div style="float:left"><?php echo $form->textArea($model, 'description', array('rows'=> 35,
																										'cols'=> 60)); ?></div>
					</div>

					<div class="row">
						<?php echo $form->labelEx($model, 'dateFrom'); ?>
						<?php echo $form->textField($model, 'dateFrom', array('placeholder' => 'dd/mm/yyyy',
																			  'value'       => $model->dateFrom ? date("d/m/Y", strtotime($model->dateFrom)) : '')); ?>
						<?php echo $form->textField($model, 'timeFrom', array('placeholder' => 'hh:mm')); ?>
					</div>

					<div class="row">
						<?php echo $form->labelEx($model, 'dateTo'); ?>
						<?php echo $form->textField($model, 'dateTo', array('placeholder' => 'dd/mm/yyyy',
																			'value'       => $model->dateTo ? date("d/m/Y", strtotime($model->dateTo)) : "")); ?>
						<?php echo $form->textField($model, 'timeTo', array('placeholder' => 'hh:mm')); ?>
					</div>
					<div class="row">
						<?php echo $form->labelEx($model, 'status'); ?>
						<?php echo $form->dropDownList($model, 'status', Lists::model()->getList("LocalEventStatus")); ?>
					</div>


					<h3>Events Address</h3>
					<?php $this->renderPartial("application.modules.admin4.views.location._location_form", array('model'       => $address,
																				'form'        => $form,
																				'parentModel' => $model,
																				'parentField' => 'addressID')) ?>
					<div class="row buttons">
						<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
					</div>
				</td>
				<td style="width:50%; vertical-align: top;">
					<h3>Main Image</h3>
                    <div class="row" style="clear:both;">
                        <span style="color:#999; font-size: 10px;">Please upload images 600x600(or aspect ratio = 1) as resize tool works bad at the moment</span>
						<?php $this->widget('CMultiFileUpload', array(
																	 'name'        => 'mainImage',
																	 'accept'      => 'jpg|png:gif',
																	 'max'         => 10,
																	 'remove'      => Yii::t('ui', 'Remove'),
																	 'htmlOptions' => array('size'     => 25,
																							'onChange' => ''),
																));
						?></div>
					<div class="row">
						<?php if(($model->mainImageID) && ($model->mainImage)){ ?>
                        <div id="imageContainer-<?php echo $model->mainImage->id ?>" style="float:left; border:1px solid #dedede; margin: 3px 3px; ">
							<?php echo CHtml::image(Yii::app()->params['imgUrl'] . "/LocalEvent/" . $model->id . "/" . $model->mainImage->smallName); ?>
                            <br>


                            <span style="float: right">
							<?php echo CHtml::image("/images/sys/admin/icons/cross-icon.png", "", array('onclick' => 'dropImage(' . $model->mainImage->id . ')')); ?>
							</span>
                        </div>
						<?php } ?>
					</div>
					<h3>Images</h3>
					<div class="row" style="clear:both;">
						<?php $this->widget('CMultiFileUpload', array(
																	 'name'        => 'files',
																	 'accept'      => 'jpeg|jpg|gif|png',
																	 'max'         => 20,
																	 'remove'      => 'Remove images(s) selection',
																	 'htmlOptions' => array('size'     => 25,
																							'onChange' => '',
																							'multiple'=>'multiple'),
																));
						?></div>

                    <div class="row">
						<?php
						if (isset($images) && $images) {
							if(count($images)==1 && ($model->mainImageID==$images[0]->id)){ } else {
							?>
                            <div>
                                <div style="float: left;">
									<?php echo CHtml::checkBox('LocalEvent[selectAll]', false, array('value'  => 0,
																									 'id'     => 'selectAll',
																									 'onClick'=> 'toggleChecked(this.checked)')) ?>
                                    <label for="selectAll" style="float: none;">Select/Deselect All</</label>
                                </div>
                                <div style="float: left;margin-left:100px;">
									<?php echo CHtml::link('Delete Selected', 'javascript:void(0)', array('onClick'=> 'deleteSelected()')) ?>
                                </div>
                                <br style="clear:both;">
                            </div>
								<?php }
							foreach ($images as $image) {
								if(($model->mainImageID) && ($model->mainImageID!=$image->id)){
								?>
                                <div id="imageContainer-<?php echo $image->id ?>" style="float:left; border:1px solid #dedede; margin: 3px 3px; ">
									<?php echo CHtml::image(Yii::app()->params['imgUrl'] . "/LocalEvent/" . $model->id . "/" . $image->smallName); ?>
                                    <br>
									<span>
									<?= CHtml::checkBox('LocalEvent[selectImage]',false,array('value'        => $image->id,
																							  'id'           => 'selectImageId_' . $image->id,
																							  'class'        => 'checkImage')) ?>
									</span>
									<span style="float: right">
									<?php echo CHtml::image("/images/sys/admin/icons/cross-icon.png", "", array('onclick' => 'dropImage(' . $image->id . ')')); ?>
									</span>
                                </div>
								<?php
								}
							}
						}
						?>
                    </div>
				</td>
			</tr>
		</table>

		<?php echo $form->hiddenField($model, "addressID") ?>
	</fieldset>
	<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
	$("#LocalEvent_dateFrom").datepicker();
	$("#LocalEvent_dateTo").datepicker();
	CKEDITOR.replace('LocalEvent_description', {
		width   : $('#LocalEvent_description').width(),
		height   : $('#LocalEvent_description').height()
	});

	var submitImage = function ()
	{
		$('#local-event-form').submit();
		return;
	}

	var dropImage = function(id,multiple) {
		var multipleImage = 'true';
		if(!multiple){
			if(!confirm("Are you sure you want to delete this image?")) {
				return false;
			}
            multipleImage = 'false';
		}
		$.get('<?php echo $this->createUrl('File/Delete') ?>/id/' + id + '/fileModel/Image/recordModel/LocalEvent/multipleImages/'+multipleImage, function(data) {
			if(data.result == true) {
				$("#imageContainer-" + id).hide();
			}
		},"json");
	}

    var deleteSelected = function()
    {
        var countChecked = $('input.checkImage:checked').length;
        if(countChecked!=0)
        {
            if(!confirm("Are you sure you want to delete selected image(s) ?")) {
                return false;
            }
            $('input.checkImage:checked').each( function(i) {
                var thisId = $(this).val();
                var multiple = true;
                if(i==(countChecked-1)){
                    dropImage(thisId,multiple);
                } else {
                    dropImage(thisId,multiple);
                }
            })
        } else {
            alert("Please select image(s)");
            return false;
        }
    }

    var toggleChecked = function (status) {
        $(".checkImage").each( function() {
            $(this).attr("checked",status);
        })
    }
</script>
