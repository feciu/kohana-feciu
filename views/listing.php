<h2><?php echo ucwords($model_friendly_name); ?> Management</h2>
<script type="text/javascript">
<!--
function confirm_delete(record) {
	return confirm("Are you sure you want to delete record '"+ record +"'? This change is permanent and cannot be undone.")
}
-->
</script>
<?php echo isset($search_form) ? $search_form : ''; ?>

<?php
if($total_results > 0) {
	echo $list;
	echo '<p>'.$pagination.'</p>';
} else {
?>
<p><strong>There are no <?php echo ucwords(inflector::plural($model_friendly_name)); ?> - create one by clicking below.</strong></p>
<?php
}
?>
<?php if(! isset($add_new)):?>
<p><?php echo html::anchor('admin/'.strtolower($model_friendly_name).'/add', 'Add new '.$model_friendly_name, array('class' => 'eauth_add button')); ?></p>
<?php else:?>
<p><?php echo html::anchor('admin/panel/add', 'Add new '.$model_friendly_name, array('class' => 'eauth_add button')); ?></p>
<?php endif;?>