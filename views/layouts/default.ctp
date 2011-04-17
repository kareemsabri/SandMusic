<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="google-site-verification" content="aoSwKwdpHt4VaCRD4uYkPntEj27meObWEDYwxpGe3tM" />
		<meta name="google-site-verification" content="k4H2TNIwmP9cS7Nc3IFA4kiwWXKV-hhTicruu17zLO8" />
		<meta name="keywords" content="music, indie bands, mp3s, music player, social music" />
		<meta name="description" content="Online music sharing for the indie-obsessed" />
		<meta name="Programming/XHTML/CSS" content="Kareem Sabri" />
		<meta name="Web Design" content="Jamie Umpherson" />
		<meta property="og:title" content="<?php echo $title_for_layout; ?>" />
		<meta property="og:type" content="music" />
		<meta property="og:image" content="http://sandmusic.ca/img/sandmusic_logo_square.gif" />
		<meta property="og:site_name" content="SandMusic" />
		<meta property="fb:admins" content="72604167" />
		<meta property="og:description" content="Online music sharing for the indie-obsessed." />
		<title><?php echo $title_for_layout; ?></title>
		<?php echo $this->Html->css('reset'); ?>
		<?php echo $this->Html->css('960_24_col'); ?>
		<?php echo $this->Html->css('text'); ?>
		<?php echo $this->Html->css('screen'); ?>
		<?php echo $this->Html->script('prototype'); ?>
		<?php echo $this->Html->script('scriptaculous'); ?>
		<?php echo $this->Html->script('sm_facebook'); ?>
		<?php echo $this->Html->script('ui/ui'); ?>
		<script src="http://connect.facebook.net/en_US/all.js"></script>
	</head>
	<body <?php if ($overlay) { echo 'onload="setTimeout(\'showOverlay(\\\''.$message.'\\\')\',3000);"'; } ?>>
		<div class="wrapper">
		<div id="top_notification" style="display:none;"><div class="top_notification" align="center"><span id="top_notification_message" class="top_notification_message"></span><div id="notification_dismiss" class="right"><a href="#" onclick="document.getElementById('top_notification').setAttribute('style','display:none');showing=false;return false;">X</a></div></div></div>
		<div class="container_24">
			<div id="header">
				<div class="grid_4" id="logo">
					<a href="/"><?php echo $this->Html->image('sandmusic_logo.png', array('alt' => 'SandMusic'))?></a>
				</div>
				<?php if (!$pages) { ?>
				<div class="grid_6 push_14" id="account_info">
					<?php if ($info['signed_in']) { ?>
					<div id="name" class="text_right">
						Welcome <a href="/settings"><?php echo $name; ?></a><br/>
						<a href="/settings" class="small">Account Settings</a>
					</div>
					<?php } else { ?>
					<div class="text_right">
						<fb:login-button perms="email,user_likes"></fb:login-button><br/><a href="/pages/facebook" class="tiny" target="_blank">What's this?</a>
					</div>
					<?php } ?>
					<?php if (isset($search))  { ?>
					<div id="search" class="text_right">
						<?php echo $this->Form->create('Song', array('default' => false, 'action' => 'index', 'type' => 'get')); ?>
						<?php echo $this->Form->text('q', array('size' => 10, 'value' => isset($q) ? $q : 'Search', 'onfocus' => 'clearForm(this);', 'autocomplete' => 'off')); ?>&nbsp;
						<?php echo $this->Js->submit('Go', array('update' => '#content', 'div' => false, 'method' => 'get', 'evalScripts' => true, 'before' => 'new Effect.Opacity(\'content\', { from: 1, to: 0.3, duration: 0.5});', 'complete' => 'setTimeout("new Effect.Opacity(\'content\', {from: 0.3, to: 1, duration: 0.5})", 1000);YAHOO.MediaPlayer.addTracks(document.getElementById("songs_table"),null,true);initialize();')); ?>
						<?php echo $this->Form->end(); ?>
					</div>
					<?php } ?>
				</div>				
				<?php } ?>			
			</div>		
			<?php echo $content_for_layout; ?>
		<div class="clear"></div>
		</div>
		<div id="push"></div>
		</div>
		<div id="footer">
			<div class="container_24">
				<ul id="footer_links" class="left">
					<li>
						<a href="/pages/about" class="plain" target="_blank">
							ABOUT
						</a>
					</li>
					<li>
						<a class="plain" href="/pages/privacy" target="_blank">
							PRIVACY
						</a>
					</li>
					<li>
						<a href="/pages/copyrights" class="plain" target="_blank">
							COPYRIGHTS
						</a>
					</li>
					<li>
						<a href="mailto:support@sandmusic.ca" class="plain" target="_blank">
							HELP
						</a>
					</li>
					<li>
						<a href="/pages/whats_new" class="plain" target="_blank">
							WHAT'S NEW
						</a>
					</li>					
					<li>
						<a href="http://www.facebook.com/pages/SandMusic/166924103349516" class="plain" target="_blank">
							FACEBOOK
						</a>
					</li>
					<li>
						<a href="http://twitter.com/SandMusicCA" class="plain" target="_blank">
							TWITTER
						</a>
					</li>										
				</ul>
			</div>
		</div>
		<?php echo $this->element('sql_dump'); ?>
		<div id="fb-root"></div>
		<script src="http://connect.facebook.net/en_US/all.js"></script>
		<script>
		FB.init({appId: '184491284895556', status: true, cookie: true, xfbml: true});
		FB.XFBML.parse();
		FB.Event.subscribe('auth.sessionChange', function(response) {
		if (response.session) {
			facebookLogin();
		}
		});
		</script>
		<script type="text/javascript">
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-9540760-3']);
		  _gaq.push(['_trackPageview']);
		
		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		</script>
		<?php echo $this->Js->writeBuffer(); ?> 		
	</body>
</html>