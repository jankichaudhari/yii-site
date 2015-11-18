<style type="text/css">
    .imageContainer{
        width: 200px;
        margin: 16px;;
        border: 1px solid #555555;
    }
    .imageContainer .title{
        font-weight: bold;
        width: 180px;
        float: left;
    }
    .imageContainer .cross{
        width: 20px;
        float: right;
    }
    .imageContainer .cross:after{
        clear: both;
        display: table;
        content: '';
    }
</style>
<?php
/**
 * @var           $this          PropertyCategoryController
 * @var           $model          PropertyCategory
 * @var AdminForm $form
 */
/** @var $cs CClientScript */
$cs = Yii::app()->getClientScript();
?>

<?php $form = $this->beginWidget('AdminForm', array(
    'id' => 'category-photos-form',
    'htmlOptions' => [
        'enctype' => 'multipart/form-data',
    ]
)); ?>


    <div class="control-group">
        <label class="bold">Photo(s)</label>
        <div class="controls">
            <?php $this->widget('CMultiFileUpload', array(
                'name'        => 'category-photo',
                'accept'      => 'jpeg|jpg|gif|png',
                'max'         => 1,
                'remove'      => 'Remove selection of photo(s)',
                'duplicate'   => 'Duplicate Image',
                'denied'      => 'Invalid Image',
                'htmlOptions' => array('size'     => 25,
                    'onChange' => '',
                ),
            ));
            ?>
        </div>
        <div class="control-group">
            <?= CHtml::dropDownList('category-photo-type','',$model->getPhotoTypes(),['empty' => '', 'id'=>'category-photo-type']) ?>
        </div>
        <div class="control-group">
            <div class="controls" style="margin: 5px;">
            <?= CHtml::submitButton('Upload & Save',['name'=>'upload-photo','class'=>'btn','id'=>'upload-photo']) ?>
        </div>
    </div>

    <div class="controls sortable">
        <?php
        foreach($model->getPhotoTypes() as $key => $value){
            $photos = File::model()->findAllByAttributes(['recordId'=>$model->id,'recordType'=>PropertyCategory::CATEGORY_PHOTO_PREFIX . $key]);
            if($photos){
                foreach($photos as $photo){
                    echo '<div class="control-group imageContainer" id="imageContainer-'.$photo->id.'">';
                    echo '<div class="controls">';

                    echo '<div class="title">' . $value . '</div>';
                    echo '<div class="cross">' . CHtml::image("/images/sys/admin/icons/cross-icon.png", "", array('onclick' => 'deleteImage(' . $photo->id . ',false)')) . '</div>';
                    echo CHtml::image(
                        $model->getImageFolderPath() . "/" . $photo->name,
                        '',
                        ['width' => '200']
                    );

                    echo '</div>';
                    echo '</div>';
                }
            }
        }
        ?>

        </div>
    </div>


<?php $this->endWidget() ?>

<script type="text/javascript">
    $('#category-photos-form').on('submit', function (){
        var photoType = $('#category-photo-type').val();
        if(!photoType || photoType.length==0){
            alert("Please select photo type");
            return false;
        }
    });

    var deleteImage = function(id,multiple) {
        var multipleImage = 'true';
        if(!multiple){
            if(!confirm("Are you sure you want to delete this image?")) {
                return false;
            }
            multipleImage = 'false';
        }
        $.get('<?php echo $this->createUrl('File/Delete') ?>/id/' + id + '/fileModel/Image/recordModel/PropertyCategory/multipleImages/'+multipleImage, function(data) {
            if(data.result == true) {
                $("#imageContainer-" + id).hide();
            }
        },"json");
    };

</script>