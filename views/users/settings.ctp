<div class="grid_5">
	<h2>Account Settings</h2>
</div>

<div class="clear"></div>

<div class="grid_14">
<?php echo $this->Form->create('User', array('url' => '/settings')); ?>
<h4>Notifications</h4>
<?php echo $this->Form->input('notifications', array('label' => '&nbsp;Yes, send me email notifications.', 'after' => '<br/><br/>')); ?>
<?php echo $this->Form->input('facebook_share', array('label' => '&nbsp;Yes, share songs I like on facebook.', 'after' => '<br/><br/>', 'onclick' => 'facebookPermsCheck();', 'id' => 'fb_share')); ?>
<?php echo $this->Form->end('Save Settings'); ?>
</div>