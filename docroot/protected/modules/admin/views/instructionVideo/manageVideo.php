<style type="text/css">
    .video-box {
        float    : left;
        border   : 1px solid #555555;
        margin-bottom: 7px;
        margin-right: 7px;
        position : relative;
		background-position: center center;
		height: 200px;
		width: 200px;
		text-align: center;
    }
    .video-box .top-part {
        height      : 150px;
        line-height : 150px;
        text-align  : center;
    }

    .video-box .bottom-part {
        height     : 40px;
		padding: 5px 0;
        background : rgba(255, 255, 255, 0.8);
    }
    .video-box a{
		text-decoration: none;
		font-weight: bold;
		color: #555555;
	}
</style>
<?php
/**
 * :var $this InstructionVideoController
 * @var $instructionVideos InstructionVideo
 * @var $activeInstructionIds Deal[]
 */

$criteria = new CDbCriteria();
$criteria->scopes = ['notUnderTheRadar', 'publicAvailable'];
?>

<div class="row-fluid">
    <div class="span12">
        <fieldset>
            <div class="block-header">
                Manage Videos Sequence
            </div>

            <div class="content sortable">
				<?php
				foreach($instructionVideos as $instructionVideo):
                    /** @var $instruction Deal[ ] */
                $instruction = Deal::model()->findByPk($instructionVideo->instructionId,$criteria);
					if($instruction):
						$photo = $instruction->getMainImage() ? $instruction->getMainImage()->getMediaImageURIPath('_small') : '';
						$info = $instruction->property->getLine(3) . ', ' . $instruction->property->getFirstPostcodePart() . ' - ' . Locale::formatPrice($instruction->dea_marketprice, $instruction->dea_type == 'Sales' ? false : true);
						echo '<div data-id="'.$instructionVideo->id.'" class="video-box" style="background-image: url('.$photo.')">';
							echo '<div class="top-part">';
							echo '<img src="' .Icon::PUBLIC_VIDEO_PLAY_ICON .'" style="vertical-align:middle;">';
							echo '</div>';
							echo '<div class="bottom-part">';
							echo '<a href="'.$this->createUrl('/admin4/instruction/production/',['id'=>$instruction->dea_id]).'##manageVideo">'. $info .'</a>';
							echo '</div>';
						echo '</div>';
					endif;
				endforeach;
				?>
            </div>
		</fieldset>
	</div>
</div>


<script type="text/javascript">
    $(".sortable").sortable({
        stop : function (event, ui)
        {
            var newOrder = [];
            $('.sortable .video-box').each(function ()
            {
                newOrder.push($(this).data('id'));
            });

			$.post('/admin4/instructionVideo/rearrange', {'newOrder' : newOrder }, function (data)
				{
					console.log(data);
				}
            );
        }
    });
</script>