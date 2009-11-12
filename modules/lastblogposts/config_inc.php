<?php
global $gBitThemes;

$gBitThemes->registerModule( array( 	
	'title' => tra( 'Recent Blog Posts' ),
	'description' => tra( 'Displays a list of recent blog posts. The list can be customized by various parameters.' ),
	'params' => array(
			'user' 			=> array( 'help' => 'Set a user name to limit the list of recent posts to just posts by that user.', ),
			'blog_id'		=> array( 'help' => 'Set a blog_id to limit the list of recent posts to just posts to that blog', ),
			'groupd_id' 	=> array( 'help' => 'Set a groupd_id to limit the list of recent posts to just posts by users in that group.', ),
			'max_records' 	=> array(),
			'offset'		=> array( 'help' => 'Set offset to offest the start of the list' ),
			'status'		=> array( 'help' => 'Set status to "draft" to display a list of draft posts by the logged in user.', 
									  'select' => array( 'public'	=> 'Public', 
														 'draft' 	=> 'Draft',
													  ),
									),
		),
	'package' => BLOGS_PKG_NAME,
	'directory' => 'lastblogposts',
	'handler' => 'mod_last_blog_posts.php',
	'template' => 'mod_last_blog_posts.tpl',
	'legacy_dir' => 'modules', // force module to show up in correct array during upgrade process
	'legacy_prefix' => 'mod_', // force module to show up in correct array during upgrade process
));

