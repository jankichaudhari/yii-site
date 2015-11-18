<?php
/**
 * @var $this  SuperAdminController
 * @var $model CActiveRecord
 */
?>
<style type="text/css">
	.expander {
		color         : #006dff;
		border-bottom : 1px dashed #006dff;
		cursor        : pointer;
	}

	.expandable {
		display : none;
	}
</style>
<div class="row-fluid">
	<div class="span6">
		<fieldset>
			<div class="block-header">Attributes</div>
			<div class="content">
				<div class="control-group">
					<label class="control-label">Model</label>

					<div class="controls">
						<?php echo get_class($model) ?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"></label>

					<div class="controls">
						<span data-expand="#print_r" class="expander">Show print_r output</span>

						<div class="expandable" id="print_r">
							<pre style="color: #555"><?php echo print_r($model->attributes, true) ?></pre>
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"></label>

					<div class="controls">
						<span data-expand="#var_dump" class="expander">Show var_dump</span>

						<div class="expandable" id="var_dump">
							<pre style="color: #555"><?php echo var_dump($model->attributes) ?></pre>
						</div>
					</div>
				</div>
				<?php foreach ($model->attributes as $key => $value): ?>
					<div class="control-group">
						<label class="control-label"><?php echo $key ?></label>

						<div class="controls">
							<?php echo substr($value, 0, 400); ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</fieldset>
	</div>
	<div class="span6">
		<fieldset>
			<div class="block-header">Relations</div>
			<div class="content">
				<?php foreach ($model->relations() as $key => $value): ?>
					<div class="control-group">
						<label class="control-label"><?php echo $key ?></label>

						<div class="controls">
							<?php echo CHtml::link('Load', [
														   'superadmin/loadRelatedIds', 'model' => get_class($model), 'pk' => $model->getPrimaryKey(), 'related' => $key
														   ], ['class' => 'get-related', 'data-model' => $value[1]]) ?>
						</div>
					</div>

				<?php endforeach; ?>
			</div>
		</fieldset>
	</div>
</div>
<script type="text/javascript">
	(function ()
	{
		$('.expander').on('click', function ()
		{
			$($(this).data('expand')).toggle();
		});

		$('.get-related').on('click', function (event)
		{
			var $this = $(this);
			var modelClass = $this.data('model');
			$.get($this.attr('href'), function (data)
			{
				var html = [];
				for (var i = 0; i < data.length; i++) {
					html.push('<a href=/admin4/superAdmin/viewRecord/model/' + modelClass + '/pk/' + data[i] + '>' + data[i] + '</a>');
				}
				if (html.length > 0) {

					$this.parent().html(html.join(', '));
				} else {
					$this.parent().html('no related records');
				}
			});
			event.preventDefault();
		})
	})();
</script>