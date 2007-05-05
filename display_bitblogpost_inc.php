<?php
/**
 * @package blogs
 * @subpackage functions
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_PATH.'BitBlog.php' );

if (!isset($gContent->mPostId)) {
	$parts = parse_url($_SERVER['REQUEST_URI']);

	$paths = explode('/', $parts['path']);
	$blog_id = $paths[count($paths) - 2];
	$post_id = $paths[count($paths) - 1];
	// So this is to process a trackback ping
	if (isset($_REQUEST['__mode'])) {
		// Build RSS listing trackback_from
		$pings = $gContent->getTrackbacksFrom();
	}

	if (isset($_REQUEST['url'])) {
		// Add a trackback ping to the list of trackback_from
		$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';

		$excerpt = isset($_REQUEST['excerpt']) ? $_REQUEST['excerpt'] : '';
		$blog_name = isset($_REQUEST['blog_name']) ? $_REQUEST['blog_name'] : '';

		if ($gContent->addTrackbackFrom( $_REQUEST['url'], $title, $excerpt, $blog_name ) ) {
			print ('<?xml version="1.0" encoding="iso-8859-1"?>');

			print ('<response>');
			print ('<error>0</error>');
			print ('</response>');
		} else {
			print ('<?xml version="1.0" encoding="iso-8859-1"?>');

			print ('<response>');
			print ('<error>1</error>');
			print ('<message>Error trying to add ping for post</message>');
			print ('</response>');
		}

		die;
	}
}

$gBitSystem->verifyPackage( 'blogs' );

$gBitSystem->verifyPermission( 'p_blogs_view' );

// Check permissions to access this page
if( !$gContent->isValid() ) {
	$gBitSystem->fatalError( tra( 'Post cannot be found' ));
}

$displayHash = array( 'perm_name' => 'p_blogs_view' );
$gContent->invokeServices( 'content_display_function', $displayHash );
$gBitSmarty->assign('post_id', $gContent->mPostId);


//Build absolute URI for this
$parts = parse_url($_SERVER['REQUEST_URI']);
/* DEPRECATED - these prolly need to be a larger array of any blog_ids 
$uri = httpPrefix(). $parts['path'] . '?blog_id=' . $gContent->mInfo['blog_id'] . '&post_id=' . $gContent->mPostId;
$uri2 = httpPrefix(). $parts['path'] . '/' . $gContent->mInfo['blog_id'] . '/' . $gContent->mPostId;
$gBitSmarty->assign('uri', $uri);
$gBitSmarty->assign('uri2', $uri2);
*/

/* MOVE THIS - this looks like it should be part of browsing a blog not a post -wjames5
if (!isset($_REQUEST['offset']))
	$_REQUEST['offset'] = 0;

if (!isset($_REQUEST['sort_mode']))
	$_REQUEST['sort_mode'] = 'created_desc';

if (!isset($_REQUEST['find']))
	$_REQUEST['find'] = '';

$gBitSmarty->assign('offset', $_REQUEST["offset"]);
$gBitSmarty->assign('sort_mode', $_REQUEST["sort_mode"]);
$gBitSmarty->assign('find', $_REQUEST["find"]);
$offset = $_REQUEST["offset"];
$sort_mode = $_REQUEST["sort_mode"];
$find = $_REQUEST["find"];
*/

$parsed_data = $gContent->parseData();
$gBitSmarty->assign('parsed_data', $parsed_data);
$gBitSmarty->assign('post_info', $gContent->mInfo );

if ($gBitSystem->isFeatureActive( 'blog_posts_comments' ) ) {
	$comments_return_url = $_SERVER['PHP_SELF']."?post_id=".$gContent->mPostId;
	$commentsParentId = $gContent->mInfo['content_id'];
	include_once ( LIBERTY_PKG_PATH.'comments_inc.php' );
}

$extendedTitle = isset($gContent->mInfo['blogtitle']) ? ' - '.$gContent->mInfo['blogtitle'] : NULL;
$gBitSystem->setBrowserTitle($gContent->mInfo['title'].$extendedTitle);
// Display the template
$gBitSystem->display( 'bitpackage:blogs/view_blog_post.tpl');

?>
