<?php
/**
 * @var           $this          LocalEventController
 * @var           $model          LocalEvent
 * @var AdminForm $form
 */
/** @var $cs CClientScript */
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile('/js/jquery.Jcrop.min.js');
$cs->registerCssFile('/css/jquery.Jcrop.min.css');
?>
<style type="text/css">
    .image-box {
        float    : left;
        border   : 1px solid #666;
        margin   : 5px;
        position : relative;
    }

    .delete-file {
        position : absolute;
        top      : 2px;
        right    : 2px;
        cursor   : pointer;
    }

    .media-files {
        /*background : #fff5b5;*/
        overflow   : auto;
    }

    .previewMedia {
        border     : 3px inset #f90;
        background : white;
        position   : relative;
        float      : left;
        /*display: none;*/
        height     : 605px;
        width      : 605px;
        text-align : center;
        overflow   : auto;
    }

    .photo-container{
        width: 605px;
        /*height: 252px;*/
        padding: 10px 0px;
        margin: 10px 0px;
        text-align: left;
        /*border: 1px dotted #999;*/
    }
    .photo-upload-container{
        float: left;
        margin-left: 50px;
    }

    .thumbnail-container {
        float: left;
        width        : 246px;
        height       : 246px;
        overflow     : hidden;
        border       : 3px inset #f90;
        background   : white;
        margin-left: 10px;;
        text-align   : center;
    }
	.mainPhoto{
        float: left;
		border: 1px solid #999;
		padding: 5px;
		width: 44%;
		min-width: 612px;
	}
	.photos{
        float:right;
		margin-left:1%;
		border: 1px solid #999;
		padding: 5px;
		width:52%;
		min-width: 706px;
		min-height: 918px;
	}
	.imageContainer{
        float:left;
		border:1px solid #dedede;
		margin: 12px;
		cursor: move;
		min-height: 180px;
	}

</style>


	<?php $form = $this->beginWidget('AdminForm', array(
													   'htmlOptions' => ['enctype' => 'multipart/form-data',]
												  )); ?>

    <div class="mainPhoto">
        <input type="hidden" id="cropX" name="cropX">
        <input type="hidden" id="cropY" name="cropY">
        <input type="hidden" id="cropWidth" name="cropWidth">
        <input type="hidden" id="imageWidth" name="imageWidth">
        <input type="hidden" id="imageHeight" name="imageHeight">

        <div class="photo-container">
            <div class="thumbnail-container">
                <h4 id="thumbnail-text">THUMBNAIL PREVIEW</h4>
                <img src="" id="thumbnail" alt="">
            </div>

            <div class="photo-upload-container">
                <div class="control-group">
                    <label class="bold">Main  Photo</label>
                    <div class="controls">
						<?php $this->widget('CMultiFileUpload', array(
																	 'name'        => 'mainImage',
																	 'accept'      => 'jpg|png:gif',
																	 'max'         => 1,
																	 'remove'      => '',
																	 'denied'      => 'Invalid Image',
																	 'htmlOptions' => array('size'     => 25,
																							'onChange' => ''),
																));
						?>
                    </div>
                    <div class="controls">
						<?php if(($model->mainImageID) && ($model->mainImage)){ ?>
                        <div id="imageContainer-<?php echo $model->mainImage->id ?>" style="float:left; border:1px solid #dedede; margin: 3px 3px; ">
							<?php echo CHtml::image(Yii::app()->params['imgUrl'] . "/LocalEvent/" . $model->id . "/" . $model->mainImage->smallName); ?>
                            <br>


                            <span style="float: right">
							<?php echo CHtml::image("/images/sys/admin/icons/cross-icon.png", "", array('onclick' => 'deleteImage(' . $model->mainImage->id . ',false)')); ?>
							</span>
                        </div>
						<?php } ?>
                    </div>
                </div>
                <div class="control-group form-buttons">
                    <div class="controls" style="text-align: center;margin-top: 15px;">
                        <input type="submit" value="Upload" name="mainImage" class="btn" id="uploadMainImage">
                        <input type="button" value="Cancel Upload" class="btn" id="cancelUpload">
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

        <div id="previewMedia" class="previewMedia">
            <h1 id="preview-text">FILE PREVIEW</h1>
            <img src="" id="preview-image">
        </div>
    </div>

    <div class="photos">
        <div class="control-group">
            <label class="bold">Other Photo(s)</label>
            <div class="controls">
				<?php $this->widget('CMultiFileUpload', array(
															 'name'        => 'images',
															 'accept'      => 'jpeg|jpg|gif|png',
															 'max'         => 20,
															 'remove'      => 'Remove selection of photo(s)',
															 'duplicate'   => 'Duplicate Image',
															 'denied'      => 'Invalid Image',
															 'htmlOptions' => array('size'     => 25,
																					'onChange' => '',
																					'multiple'=>'multiple'),
														));
				?>
            </div>
            <div class="control-group form-buttons" >
                <div class="controls" style="margin: 5px;">
					<?= CHtml::submitButton('Upload',['name'=>'images','class'=>'btn','id'=>'uploadImages']) ?>
                </div>
            </div>
            <div class="controls sortable">
				<?php
				if (isset($model->images) && $model->images) {
					if(count($model->images)==1 && ($model->mainImageID==$model->images[0]->id)){ } else {
						?>
                        <div>
                            <div style="float: left;">
								<?php echo CHtml::checkBox('LocalEvent[selectAll]', false, array('value'  => 0,
																								 'id'     => 'selectAll',
																								 'onClick'=> 'toggleChecked(this.checked)')) ?>
                                <label for="selectAll" style="float: none;">Select/Deselect All</label>
                            </div>
                            <div style="float: left;margin-left:100px;">
								<?php echo CHtml::link('Delete Selected', 'javascript:void(0)', array('onClick'=> 'deleteSelected()')) ?>
                            </div>
                            <br style="clear:both;">
                        </div>
						<?php }
					foreach ($model->images as $image) {
						if(($model->mainImageID) && ($model->mainImageID!=$image->id)){
							?>
                            <div id="imageContainer-<?php echo $image->id ?>" class="imageContainer">
								<?php echo CHtml::image(Yii::app()->params['imgUrl'] . "/LocalEvent/" . $model->id . "/" . $image->smallName); ?>
                                <br>
									<span>
									<?= CHtml::checkBox('LocalEvent[selectImage]',false,array('value'        => $image->id,
																							  'id'           => 'selectImageId_' . $image->id,
																							  'class'        => 'checkImage')) ?>
									</span>
									<span style="float: right">
									<?php echo CHtml::image("/images/sys/admin/icons/cross-icon.png", "", array('onclick' => 'deleteImage(' . $image->id . ',false)')); ?>
									</span>
                            </div>
							<?php
						}
					}
				}
				?>
            </div>
        </div>

	<br class="clear"/>

	<?php $this->endWidget() ?>

<script type="text/javascript">
    var deleteImage = function(id,multiple) {
        var multipleImage = 'true';
        if(!multiple){
            if(!confirm("Are you sure you want to delete this image?")) {
                return false;
            }
            multipleImage = 'false';
        }
        $.get('<?php echo $this->createUrl('File/Delete') ?>/id/' + id + '/fileModel/LocalEventImage/recordModel/LocalEvent/multipleImages/'+multipleImage, function(data) {
			console.log(data);
            if(data.result == true) {
                $("#imageContainer-" + id).hide();
            }
        },"json");
    };

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
                    deleteImage(thisId,multiple);
                } else {
                    deleteImage(thisId,multiple);
                }
            })
        } else {
            alert("Please select image(s)");
            return false;
        }
    };

    var toggleChecked = function (status) {
        $(".checkImage").each( function() {
            $(this).attr("checked",status);
        })
    };
    var jcrop_api;

    (function ()
    {
        $(".sortable").sortable({
            revert:true,
            cursor:"move",
            stop : function (event, ui)
            {
                var updateOrderIds = [];
                $('.imageContainer').each(function ()
                {
                    var thisId = $(this).attr("id").split("-");
                    updateOrderIds.push(thisId[1]);
                });

                $.post('/admin4/file/rearrange', { 'updateOrderIds' : updateOrderIds, 'recordId' : '<?= $model->id ?>', 'recordType':'LocalEvent' }, function (data){
                    if(data == 'error') {
                        alert("Error! Order not changed..");
                        return false;
                    }
                });
            }
        },"json");
    })();

    var preview = function ()
    {
        if (this.files && this.files[0]) {
            if(jcrop_api){
                jcrop_api.destroy();
            }

            destroySizeCss();

            var reader = new FileReader();

            reader.onload = function (e)
            {
                $('#preview-image').attr('src', e.target.result);
                $('#thumbnail').attr('src', e.target.result);
                $('#thumbnail-text').hide();
                $('#preview-text').hide();
            }
            reader.readAsDataURL(this.files[0]);
        }
    }

    $('#mainImage').on('change', preview);
//    $('#images').on('change', preview);

    function showThumbnail(coords)
    {
        document.getElementById("cropX").value = coords.x;
        document.getElementById("cropY").value = coords.y;
        document.getElementById("cropWidth").value = coords.w;

        var rx = 246 / coords.w;
        var ry = 246 / coords.h;
        var w = Math.round(rx * $('#preview-image').width()) + 'px';
        var h = Math.round(ry * $('#preview-image').height()) + 'px';

        $('#thumbnail').css({
            width      : w,
            height     : h,
            marginLeft : '-' + Math.round(rx * coords.x) + 'px',
            marginTop  : '-' + Math.round(ry * coords.y) + 'px'
        });
    }

    var destroySizeCss = function(){
        $('#preview-image').attr('src', '');
        $('#preview-image').attr('width', '');
        $('#preview-image').attr('height', '');
        $('#preview-image').css('width', '');
        $('#preview-image').css('height', '');
    }

    $('#preview-image').on('load', function ()
    {
        var x = this.width;
        var y = this.height;
        var w = 600;
        var h = 600;
        if(x < y){
            if(w > x) { w = x; }
            var setSel = [0, ((y-x)/2), x, x];
        } else {
            if(h > y) { h = y; }
            var setSel = [((x-y)/2), 0, y, y];
        }
        document.getElementById("imageWidth").value = $('#preview-image').width();
        document.getElementById("imageHeight").value = $('#preview-image').height();
        $('#preview-image').Jcrop({
            aspectRatio : 1,
            onChange    : showThumbnail,
            onSelect    : showThumbnail,
            setSelect:   setSel,
            boxWidth: w ,
            boxHeight: h

        },function(){
            jcrop_api = this;
        });
    });

    $('#cancelUpload').on('click',function(){
        if(jcrop_api){
            jcrop_api.destroy();
        }
        destroySizeCss();
        $("#media").val('');
        $('#thumbnail').attr('src', '');
        $("#cropX").val('');
        $("#cropY").val('');
        $("#cropWidth").val('');
        $('#thumbnail-text').show();
        $('#preview-text').show();
        return false;
    });

</script>