<?php
/**
 * @var $this CController
 */
?>
<style type="text/css">
	.column-example {
		background    : #919191;
		text-align    : center;
		border-radius : 5px;
		line-height   : 28px;
	}

	.row-example {
		margin-bottom : 7px;
	}
</style>
<div class="row-fluid">
<div class="span12">
<fieldset>
	<div class="block-header">
		Grid system
	</div>
	<div class="content">
		<div class="row-fluid row-example">
			<div class="span12">We are using a grid system taken from <a href="http://twitter.github.com/bootstrap/">Twitter Bootstrap</a>. So it is twelve column fluid
								grid.
			</div>
		</div>
		<div class="row-fluid row-example">
			<div class="span1 column-example">1</div>
			<div class="span1 column-example">1</div>
			<div class="span1 column-example">1</div>
			<div class="span1 column-example">1</div>
			<div class="span1 column-example">1</div>
			<div class="span1 column-example">1</div>
			<div class="span1 column-example">1</div>
			<div class="span1 column-example">1</div>
			<div class="span1 column-example">1</div>
			<div class="span1 column-example">1</div>
			<div class="span1 column-example">1</div>
			<div class="span1 column-example">1</div>
		</div>
		<div class="row-fluid row-example">
			<div class="span2 column-example">2</div>
			<div class="span2 column-example">2</div>
			<div class="span2 column-example">2</div>
			<div class="span2 column-example">2</div>
			<div class="span2 column-example">2</div>
			<div class="span2 column-example">2</div>
		</div>
		<div class="row-fluid row-example">
			<div class="span3 column-example">3</div>
			<div class="span3 column-example">3</div>
			<div class="span3 column-example">3</div>
			<div class="span3 column-example">3</div>
		</div>
		<div class="row-fluid row-example">
			<div class="span4 column-example">4</div>
			<div class="span4 column-example">4</div>
			<div class="span4 column-example">4</div>
		</div>
		<div class="row-fluid row-example">
			<div class="span6 column-example">6</div>
			<div class="span6 column-example">6</div>
		</div>
		<div class="row-fluid row-example">
			<div class="span12 column-example">12</div>
		</div>
		<div class="row-fluid row-example">
			<div class="span8 column-example">8</div>
			<div class="span4 column-example">4</div>
		</div>
	</div>
</fieldset>
<fieldset>
	<div class="block-header">
		General layout
	</div>
	<div class="content">
		<ul>
			<li>All margins and paddings must be a multiple of 7px</li>
			<li>All icon paths should be now defined as constants in Icon class. otherwise it is really annoying to remember where they are.</li>
			<li>default layout is something like
						<pre>
<?php ob_start() ?>
							<form action="">
								<fieldset>
									<div class="block-header">Header</div>
									<div class="block-buttons">
										here comes all buttons.
										<input type="submit" value="Save" class="btn">
									</div>
									<div class="content">
										.content has a 7 pixel padding on all sides.
										<div class="control-group">
											<label class="control-label" for="">Control label</label>
										</div>
										<div class="controls">
											<input type="text">
										</div>
									</div>
									<div class="block-buttons">
										here comes all buttons.
										<input type="submit" value="Save" class="btn">
									</div>
								</fieldset>
							</form>
							<?php echo htmlentities(ob_get_clean()); ?>
						</pre>
			</li>
			<li>Output of example above is show below:</li>
		</ul>
	</div>
</fieldset>
<form action="">
	<fieldset>
		<div class="block-header">Header</div>
		<div class="block-buttons">
			here comes all buttons.
			<input type="submit" value="Save" class="btn">
		</div>
		<div class="content">

			<div class="control-group">
				<label class="control-label" for="">Control label</label>

				<div class="controls">
					<input type="text">
				</div>
			</div>
		</div>
		<div class="block-buttons">
			here comes all buttons.
			<input type="submit" value="Save" class="btn">
		</div>
	</fieldset>
</form>
<form action="">
<fieldset>
<div class="block-header">
	form controls
</div>
<div class="content">
	<div class="control-group">
		<label class="control-label">Input[type=text]</label>

		<div class="controls"><input type="text"></div>
	</div>
	<div class="control-group">
		<label class="control-label">select</label>

		<div class="controls">
			<select name="" id="">
				<option value="">item 1</option>
				<option value="">item 2</option>
				<option value="">item 3</option>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Textarea</label>

		<div class="controls">
			<textarea></textarea>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">single checkbox</label>

		<div class="controls"><input type="checkbox"></div>
	</div>
	<div class="control-group">
		<label class="control-label">single radio(nonsense)</label>

		<div class="controls"><input type="radio"></div>
	</div>
	<div class="control-group">
		<label class="control-label">checbox horizontal list</label>

		<div class="controls">
			<label><input type="checkbox">checkbox 1</label>
			<label><input type="checkbox">checkbox 2</label>
			<label><input type="checkbox">checkbox 3</label>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Radio horizontal list</label>

		<div class="controls">
			<label><input type="radio">radio 1</label>
			<label><input type="radio">radio 2</label>
			<label><input type="radio">radio 3</label>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">checbox vertical list</label>

		<div class="controls">
			<label><input type="checkbox">checkbox 1</label><br>
			<label><input type="checkbox">checkbox 2</label><br>
			<label><input type="checkbox">checkbox 3</label>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Radio vertical list</label>

		<div class="controls">
			<label><input type="radio">radio 1</label><br>
			<label><input type="radio">radio 2</label><br>
			<label><input type="radio">radio 3</label>
		</div>
	</div>
	<div class="control-group">
		<div class="controls force-margin">
			<input type="text" placeholder=".input-xxlarge" class="input-xxlarge">
		</div>
	</div>
	<div class="control-group">
		<div class="controls force-margin">
			<input type="text" placeholder=".input-xlarge" class="input-xlarge">
		</div>
	</div>
	<div class="control-group">
		<div class="controls force-margin">
			<input type="text" placeholder=".input-large" class="input-large">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			Different sizes
		</label>

		<div class="controls">
			<input type="text" placeholder=".input-medium"><span class="hint">Default (and this  is span.hint)</span>
		</div>
	</div>
	<div class="control-group">
		<div class="controls force-margin">
			<input type="text" placeholder=".input-xsmall" class="input-xsmall">
		</div>
	</div>
	<div class="control-group">
		<div class="controls force-margin">
			<input type="text" placeholder=".input-xsmall" class="input-xsmall">
		</div>
	</div>
	<div class="control-group">
		<div class="controls force-margin">
			<input type="text" placeholder=".input-xxsmall" class="input-xxsmall">
		</div>
	</div>
	<div class="control-group">
		<div class="controls text force-margin">
			if yo uwant to put some text in .controls add .text class. if you don't have a label add .force-margin class to inline it with other elelemets.
		</div>
	</div>
</div>
<div class="block-buttons">
	<input type="submit" class="btn" value="input[type=submit].btn">
	<input type="button" class="btn" value="input[type=button].btn">
	<input type="reset" class="btn" value="input[type=reset].btn">
	<button class="btn">button.btn</button>
	<a href="#" class="btn">a.btn</a>
</div>
<div class="block-buttons">
	<input type="submit" class="btn btn-green" value="input[type=submit].btn.btn-green">
	<input type="button" class="btn btn-green" value="input[type=button].btn.btn-green">
	<input type="reset" class="btn btn-green" value="input[type=reset].btn.btn-green">
	<button class="btn btn-green">button.btn.btn-green</button>
	<a href="#" class="btn btn-green">a.btn.btn-green</a>
</div>
<div class="block-buttons">
	<input type="submit" class="btn btn-red" value="input[type=submit].btn.btn-red">
	<input type="button" class="btn btn-red" value="input[type=button].btn.btn-red">
	<input type="reset" class="btn btn-red" value="input[type=reset].btn.btn-red">
	<button class="btn btn-red">button.btn.btn-red</button>
	<a href="#" class="btn btn-red">a.btn.btn-red</a>
</div>
<div class="block-buttons">
	<input type="submit" class="btn btn-gray" value="input[type=submit].btn.btn-gray">
	<input type="button" class="btn btn-gray" value="input[type=button].btn.btn-gray">
	<input type="reset" class="btn btn-gray" value="input[type=reset].btn.btn-gray">
	<button class="btn btn-gray">button.btn.btn-gray</button>
	<a href="#" class="btn btn-gray">a.btn.btn-gray</a>
</div>
<div class="block-buttons">
	<input type="submit" class="btn btn-orange" value="input[type=submit].btn.btn-orange">
	<input type="button" class="btn btn-orange" value="input[type=button].btn.btn-orange">
	<input type="reset" class="btn btn-orange" value="input[type=reset].btn.btn-orange">
	<button class="btn btn-orange">button.btn.btn-orange</button>
	<a href="#" class="btn btn-orange">a.btn.btn-orange</a>
</div>

<div class="control-group">
	<div class="controls">
		<input type="text" class="input-xxsmall">

	</div>
</div>
<div class="control-group">
	<div class="controls">
		<input type="text" class="input-xsmall">

	</div>
</div>
<div class="control-group">
	<div class="controls">
		<input type="text" class="input-xsmall">

	</div>
</div>
<div class="control-group">
	<div class="controls">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
	</div>
</div>
<div class="control-group">
	<div class="controls">
		<input type="text" class="input-medium">
	</div>
</div>
<div class="control-group">
	<div class="controls">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
	</div>
</div>
<div class="control-group">
	<div class="controls">
		<input type="text" class="input-large">
	</div>
</div>
<div class="control-group">
	<div class="controls">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
	</div>
</div>
<div class="control-group">
	<div class="controls">
		<input type="text" class="input-xlarge">
	</div>
</div>
<div class="control-group">
	<div class="controls">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
	</div>
</div>
<div class="control-group">
	<div class="controls">
		<input type="text" class="input-xxlarge">
	</div>
</div>
<div class="control-group">
	<div class="controls">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
		<input type="text" class="input-xxsmall">
	</div>
</div>

</fieldset>
</form>
</div>
</div>