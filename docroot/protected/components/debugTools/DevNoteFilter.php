<?php
class DevNoteFilter extends CFilter
{
	public function preFilter($filterChain)
	{

		echo '
				<div style="
				position:fixed;
				bottom:0;
				width:150px;
				height:30px;
				background: #fcffc4;
				border-top: 1px solid #666;
				border-right: 1px solid #666;
				">
				<img src="/images/sys/add_icon.png" alt="" onclick="devnote.new()">
				<a href="#" onclick="devnote.toggleNotes()">toggle notes</a>
				</div>
				';

		/** @var $cs CClientScript */
		$cs = Yii::app()->getClientScript();
		$cs->registerScriptFile('/js/devnote.js', CClientScript::POS_HEAD);

		return true;
	}

	public function postFilter($filterChain)
	{

		echo '<script>
		devnote.pageId = "' . Devnote::getPageId() . '";
		</script>';
		/** @var $devnotes Devnote[] */
		$devnotes = Devnote::model()->findNotesForCurrentAction();
		$js = '';
		foreach ($devnotes as $key => $value) {
			$js .= 'devnote("' . $value->id . '").init({
				width : "' . $value->width . '",
				height : "' . $value->height . '",
				posX : "' . $value->posX . '",
				posY : "' . $value->posY . '",
				pageId : "' . $value->pageId . '",
				text: "' . $value->text . '"
			});';
		}
		echo '<script>' . $js . '</script>';

	}
}