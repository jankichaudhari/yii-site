 <style type="text/css">
	.imgPreview{
        float: left;
        display: inline;
        width: 146px;
		height: 200px;
        margin: 10px;
        vertical-align: top;
		border: 1px solid #888888;
		cursor: move;
	}
</style>
<?php
/**
 * @var $galleryImages PageGalleryImage
 * @var $recordType
 * @var $recordId
 * @var $title
 * @var $instanceName
 * @var $this CController
 */

$modelName = 'PageGalleryImage';
?>

<fieldset>
<div class="block-header"><?= $title ?></div>
	<div class="content">
		<?php $form = $this->beginWidget('AdminForm', array(
													   'id'                  => 'gallery-image-form',
													   'enableAjaxValidation'=> false,
													   'htmlOptions'         => array('enctype' => 'multipart/form-data'),
												  ));
		$this->widget('CMultiFileUpload', array(
											   'name'        => $modelName,
											   'id'          => $modelName,
											   'accept'      => 'jpeg|jpg|gif|png',
											   'max'         => 20,
												'remove'      => '',
											   'duplicate'   => 'Duplicate Image',
											   'denied'      => 'Invalid Image',
											   'htmlOptions' => array('size'=> 25,
																	  'multiple'=>'multiple'),
										  ));

		echo CHtml::submitButton('Upload');

		$this->endWidget();
		?>
	</div>
	<?php if (isset($galleryImages) && count($galleryImages) > 0)
	{
	?>
		<div class="content">
			<div class="control-group">
					<input type="checkbox" name="selectAll" value="0" id="selectAll" onclick="toggleChecked(this.checked)">
					<label for="selectAll" style="float: none;">Select/Deselect All</label>
					<?php echo CHtml::link('Delete Selected', 'javascript:void(0)', array(
																						 'onClick'=> 'deleteSelected(' . $recordId . ',' . "'".$modelName."','".$recordType."')",
																						 'style' => 'margin-left:50%;'
																					)) ?>
			</div>

			<div class="control-group sortable">
				<?php
					$galleryImageCount = 0;
					foreach ($galleryImages as $galleryImage) {
						?>
						<div data-id="<?php echo $galleryImage->id ?>" class="imgPreview">
								<?php
								echo CHtml::checkBox('selectImage', false, array('value'        => $galleryImage->id,
																							  'id'           => 'selectImageId_' . $galleryImage->id,
																							  'class'        => 'checkImage',
																							  'style'        => 'float:left;'));

								echo CHtml::image("/images/sys/admin/icons/cross-icon.png",
									"",
									array('onclick' => 'deleteImage(' . $galleryImage->id . ',' . "'".$modelName."','".$recordType. "'" . ',false)',
										  'style'=> 'cursor: pointer;cursor: hand;float:right;')
								);

								echo CHtml::image(Yii::app()->params['imgUrl'] . "/".$recordType."/" . $recordId . "/" . $galleryImage->smallName,
													"",
													array('id'=> $galleryImage->fullPath . '/' . $galleryImage->name,
															'width'=>'146px;')
												);
								?>
							</div>
						<?php } ?>
					<br clear="all">
			</div>
		</div>
	<?php
	}
	?>
</fieldset>

<script type="text/javascript">
    function toggleChecked(status) {
        $(".checkImage").each( function() {
            $(this).attr("checked",status);
        })
    }

    $(".sortable").sortable({
        stop : function (event, ui)
        {
            var updateOrderIds = [];
            $('.imgPreview').each(function ()
            {
                updateOrderIds.push($(this).data('id'));
            });

            $.post('/admin4/file/rearrange', { 'updateOrderIds' : updateOrderIds, 'recordId' : '<?= $recordId ?>', 'recordType':'<?= $recordType ?>' }, function (data){
                console.log(data);
            });

        }
    });

    var deleteSelected = function(record,modelName,imageType)
    {
        var countChecked = $('input.checkImage:checked').length;
        if(countChecked!=0)
        {
            if(!confirm("Are you sure you want to delete selected image(s)?")) {
                return false;
            }
            $('input.checkImage:checked').each( function(i) {
                var thisId = $(this).val();
                var multiple = true;
                if(i==(countChecked-1)){
                    deleteImage(thisId,modelName,imageType,multiple,true);
                } else {
                    deleteImage(thisId,modelName,imageType,multiple,false);
                }
            })
        } else {
            alert("Please select image(s)");
            return false;
        }
    };

    var deleteImage = function(id,modelName,imageType,multiple,last) {
        var multipleImages = 'no';
        if(!multiple){
            if(!confirm("Are you sure you want to delete this image(s) ?")) { return false; }
        } else if(last==false){
            multipleImages = 'yes';
        }

        $.get('<?php echo $this->createUrl('File/delete') ?>/id/' + id + '/fileModel/'+modelName+'/recordModel/'+imageType+'/multipleImages/' + multipleImages , function(data) {
            if(data.result == true) {
                location.reload();
            }
        },"json");
    };
</script>
