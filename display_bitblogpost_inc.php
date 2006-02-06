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

$displayHash = array( 'perm_name' => 'bit_p_view' );
$gContent->invokeServices( 'content_display_function', $displayHash );

if (!isset($gContent->mPostId) && $post_id) {
	$gContent->mPostId = $gContent->mInfo['blog_id'];
}
$gBitSmarty->assign('post_info', $gContent->mInfo );
$gBitSmarty->assign('post_id', $gContent->mPostId);
$_REQUEST["blog_id"] = $gContent->mInfo["blog_id"];

$gBitSmarty->assign('blog_id', $_REQUEST["blog_id"]);

if( !empty( $gContent->mInfo['blog_style'] ) && $gBitSystem->getPreference('feature_user_theme') == 'h' ) {
	$gBitSystem->setStyle( $gContent->mInfo['blog_style'] );
	$gBitSystem->mStyles['styleSheet'] = $gBitSystem->getStyleCss( $gContent->mInfo['blog_style'], $gContent->mInfo['user_id'] );
	$gBitSmarty->assign( 'userStyle', $gContent->mInfo['blog_style'] );
}

//Build absolute URI for this
$parts = parse_url($_SERVER['REQUEST_URI']);
$uri = httpPrefix(). $parts['path'] . '?blog_id=' . $gContent->mInfo['blog_id'] . '&post_id=' . $gContent->mPostId;
$uri2 = httpPrefix(). $parts['path'] . '/' . $gContent->mInfo['blog_id'] . '/' . $gContent->mPostId;
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

$parsed_data = $gContent->parseData();

if (!isset($_REQUEST['page']))
	$_REQUEST['page'] = 1;

$pages = $gContent->getNumberOfPages($parsed_data);
$parsed_data = $gContent->getPage($parsed_data, $_REQUEST['page']);
$gBitSmarty->assign('pages', $pages);

if ($pages > $_REQUEST['page']) {
	$gBitSmarty->assign('next_page', $_REQUEST['page'] + 1);
} else {
	$gBitSmarty->assign('next_page', $_REQUEST['page']);
}

if ($_REQUEST['page'] > 1) {
	$gBitSmarty->assign('prev_page', $_REQUEST['page'] - 1);
} else {
	$gBitSmarty->assign('prev_page', 1);
}

$gBitSmarty->assign('first_page', 1);
$gBitSmarty->assign('last_page', $pages);
$gBitSmarty->assign('page', $_REQUEST['page']);

$gBitSmarty->assign('parsed_data', $parsed_data);

$gBitSmarty->assign('individual', 'n');

if ($gBitUser->object_has_one_permission($_REQUEST["blog_id"], 'blog')) {
	$gBitSmarty->assign('individual', 'y');

	if (!$gBitUser->isAdmin()) {
		// Now get all the permissions that are set for this type of permissions 'image gallery'
		$perms = $gBitUser->getPermissions('', 'blogs');

		foreach ($perms["data"] as $perm) {
			$perm_name = $perm["perm_name"];

			if ($gBitUser->object_has_permission($user, $_REQUEST["blog_id"], 'blog', $perm_name)) {
				$$perm_name = 'y';

				$gBitSmarty->assign("$perm_name", 'y');
			} else {
				$$perm_name = 'n';

				$gBitSmarty->assign("$perm_name", 'n');
			}
		}
	}
}

$gBitSystem->verifyPermission( 'bit_p_read_blog' );
/*if (!$gBitUser->hasPermission( 'bit_p_read_blog' )) {
	$gBitSmarty->assign('msg', tra("Permission denied you can not view this section"));

	$gBitSystem->display( 'error.tpl' );
	die;
}*/

$gBitSmarty->assign('ownsblog', ( $gBitUser->isValid() && $gBitUser->mUserId == $gContent->mInfo["user_id"] ) ? 'y' : 'n' );

if ($gBitSystem->isFeatureActive( 'blogposts_comments' ) ) {
	$comments_return_url = $_SERVER['PHP_SELF']."?post_id=".$gContent->mPostId;
	$commentsParentId = $gContent->mInfo['content_id'];
	include_once ( LIBERTY_PKG_PATH.'comments_inc.php' );
}

$gBitSystem->setBrowserTitle($gContent->mInfo['title'].' - '.$gContent->mInfo['blogtitle']);
// Display the template
$gBitSystem->display( 'bitpackage:blogs/view_blog_post.tpl');

?>
