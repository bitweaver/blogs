<?php
/**
 * @version $Header$

 * @package blogs
 * @subpackage functions
 */
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );

include_once( BLOGS_PKG_PATH.'BitBlogPost.php' );

$gBitSystem->verifyPackage( 'blogs' );

if (!isset($_REQUEST["post_id"])) {
	$gBitSystem->fatalError( tra( 'No post indicated' ));
}

include_once( BLOGS_PKG_PATH.'lookup_post_inc.php' );

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

$gBitSmarty->assign( 'parsed_data', $gContent->getParsedData() );

$gBitSystem->verifyPermission( 'p_blogs_view' );

if ($gBitSystem->isFeatureActive( 'blog_posts_comments' )) {
	$comments_return_url = $_SERVER['SCRIPT_NAME']."?post_id=".$gContent->getField( 'post_id' );
	$commentsParentId = $gContent->mContentId;
	include_once ( LIBERTY_PKG_PATH.'comments_inc.php' );
}


$gBitSystem->setBrowserTitle( $gContent->mInfo['title'] );
// Display the template
$gBitSmarty->display("bitpackage:blogs/print_blog_post.tpl");

?>
