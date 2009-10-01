<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/send_post.php,v 1.26 2009/10/01 14:16:58 wjames5 Exp $

 * @package blogs
 * @subpackage functions
 */

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

// Make sure all defines get created as fully qualified URI's
$_REQUEST['uri_mode'] = TRUE;

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

include_once( BLOGS_PKG_PATH.'BitBlogPost.php' );

$gBitSystem->verifyPermission( 'p_blogs_send_post' );

if (!isset($_REQUEST["post_id"])) {
	$gBitSystem->fatalError( tra( 'No post indicated' ));
}

include_once( BLOGS_PKG_PATH.'lookup_post_inc.php' );
// make sure this user can see the post to avoid emailing post to self to circumvent the perm
$gContent->verifyViewPermission();

$gBitSmarty->assign('post_info', $gContent->mInfo );

//Build absolute URI for this
$parts = parse_url($_SERVER['REQUEST_URI']);
/*OLD with blog_id - might later want to reincorporate blog_id but will have to start in the view_blog_post.tpl -wjames5
$uri = httpPrefix(). $parts['path'] . '?blog_id=' . $gContent->mInfo['blog_id'] . '&post_id=' . $gContent->mInfo['post_id'];
$uri2 = httpPrefix(). $parts['path'] . '/' . $gContent->mInfo['blog_id'] . '/' . $gContent->mInfo['post_id'];
*/
$uri = httpPrefix(). $parts['path'] . '?post_id=' . $gContent->mInfo['post_id'];
$uri2 = httpPrefix(). $parts['path'] . '/' . $gContent->mInfo['post_id'];
$gBitSmarty->assign('uri', $uri);
$gBitSmarty->assign('uri2', $uri2);

$gBitSmarty->assign( 'parsed_data', $gContent->parseData() );

if ($gBitSystem->isFeatureActive( 'blog_posts_comments' )) {
	$comments_vars = array(
		'post_id',
		'offset',
		'find',
		'sort_mode'
	);

	$comments_return_url = $_SERVER['PHP_SELF']."?post_id=".$gContent->mPostId;
	$commentsParentId = $gContent->mContentId;
	$comments_prefix_var = 'post:';
	$comments_object_var = 'post_id';
	include_once ( LIBERTY_PKG_PATH.'comments_inc.php' );
}

if (!isset($_REQUEST['addresses'])) {
	$_REQUEST['addresses'] = '';
}

$gBitSmarty->assign('addresses', $_REQUEST['addresses']);
$gBitSmarty->assign('sent', 'n');

if (isset($_REQUEST['send'])) {
	$emails = explode(',', $_REQUEST['addresses']);

	$foo = parse_url($_SERVER["REQUEST_URI"]);
	$machine = $gContent->getDisplayUrl();

	foreach ($emails as $email) {
		$gBitSmarty->assign('mail_site', $_SERVER["SERVER_NAME"]);

		$gBitSmarty->assign('mail_user', $gBitUser->getDisplayName() );
		$gBitSmarty->assign('mail_title', $gContent->mInfo['title'] ? $gContent->mInfo['title'] : date("d/m/Y [h:i]", $gContent->mInfo['created']));
		$gBitSmarty->assign('mail_machine', $machine);
		$mail_data = $gBitSmarty->fetch('bitpackage:blogs/blogs_send_link.tpl');
		@mail($email, tra('Post recommendation at'). ' ' . $_SERVER["SERVER_NAME"], $mail_data,
			"From: ".$gBitSystem->getConfig( 'site_sender_email' )."\r\nContent-type: text/plain;charset=utf-8\r\n");
	}

	$gBitSmarty->assign('sent', 'y');
}
$gBitSystem->setBrowserTitle("Send Blog Post: ".$gContent->mInfo['title']);

// Display the template
$gBitSystem->display( 'bitpackage:blogs/send_blog_post.tpl', NULL, array( 'display_mode' => 'display' ));

?>
