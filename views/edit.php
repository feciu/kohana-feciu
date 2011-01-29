<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<h2><?php echo (isset($edit) && $edit === true ? 'Edit' : 'Add new').' '.$model_friendly_name; ?></h2>
<?php echo isset($form) ? $form : 'No content'; ?>

<?php if(! isset ($add_new)):?>
    or <a href="<?php echo URL::base().Kohana::config('feciu_conf.url_base').strtolower($model_friendly_name)?>/listing">cancel</a>
<?php else: ?>
    or <a href="<?php echo URL::base().Kohana::config('feciu_conf.url_base')?>/panel">cancel</a>
<?php endif;?>