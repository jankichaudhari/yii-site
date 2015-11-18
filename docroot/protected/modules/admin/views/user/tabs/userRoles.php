<?php
/**
 * @var $model User[]
 * @var $roles UserRole[]
 */
?>

<?php
$roles = UserRole::model()->findAll();
foreach ($roles as $role) {
	?>
	<div class="control-group">
		<label class="control-label"
			   for="<?php echo 'User_role_' . $role->rol_id ?>"><?php echo $role->rol_title ?></label>

		<div class="controls">
			<?php echo CHtml::checkBox('User[role][' . $role->rol_id . ']', $model->userBelongsToRole($role->rol_id)); ?>
		</div>

	</div>
<?php
}
?>