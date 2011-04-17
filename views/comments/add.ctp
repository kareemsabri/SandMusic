<?php if ($submit) {
	echo $response;
} else { ?>
<div id="comment_form">
	<div id="comment_questions">
		<a href="mailto:support@sandmusic.ca"><?php echo $this->Html->image('buttons/questions.png', array('alt' => 'Questions')); ?></a>
	</div>
	
	<div id="comment_form_inputs">
		<?php echo $this->Form->create('Comment', array('default' => false, 'action' => 'add')); ?>
		<?php echo $this->Form->hidden('song_id', array('value' => $song_id));?>
		<?php echo $this->Form->textarea('comment', array('class' => 'comment_area', 'rows' => 8, 'cols' => 40));?>
		<div class="submit">
		<?php echo $this->Js->submit('Submit', array('url' => '/comments/add/'.$song_id, 'before' => 'document.getElementById(\'CommentComment\').setAttribute(\'disabled\', \'disabled\');', 'complete' => 'document.getElementById(\'CommentAddForm\').reset();document.getElementById(\'comment_form_inputs\').innerHTML = \'<p>Thank you. Your comment has been submitted.</p>\';document.getElementById(\'comment_count_'.$song_id.'\').innerHTML=transport.responseText;', 'class' => 'right')); ?>
		</div>
		<?php echo $this->Form->end(); ?>
	</div>
	<a href="#" class="lbAction left" rel="deactivate"><?php echo $this->Html->image('buttons/cancel.png', array('alt' => 'Close')); ?></a>
</div>

<?php echo $this->Js->writeBuffer(); ?>

<?php } ?>