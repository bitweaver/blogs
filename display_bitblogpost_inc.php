<?php
/**
 * @package blogs
 * @subpackage functions
 */

/**
 * required setup
 */
if (defined("CATEGORIES_PKG_PATH")) {
  include_once( CATEGORIES_PKG_PATH.'categ_lib.php');
}

include_once( BLOGS_PKG_PATH.'BitBlog.php' );

global $PHP_SELF;

if (!isset($gContent->mPostId) && !isset($gContent->mPostId)) {
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

if (!isset($gContent->mPostId) && $post_id) {
	$gContent->mPostId = $gContent->mInfo['blog_id'];
}
$smarty->assign('post_info', $gContent->mInfo );
$smarty->assign('post_id', $gContent->mPostId);
$_REQUEST["blog_id"] = $gContent->mInfo["blog_id"];

$smarty->assign('blog_id', $_REQUEST["blog_id"]);

if( !empty( $gContent->mInfo['blog_style'] ) && $gBitSystem->getPreference('feature_user_theme') == 'h' ) {
	$gBitSystem->setStyle( $gContent->mInfo['blog_style'] );
	$gBitLoc['styleSheet'] = $gBitSystem->getStyleCss( $gContent->mInfo['blog_style'], $gContent->mInfo['user_id'] );
	$smarty->assign( 'userStyle', $gContent->mInfo['blog_style'] );
}

//Build absolute URI for this
$parts = parse_url($_SERVER['REQUEST_URI']);
$uri = httpPrefix(). $parts['path'] . '?blog_id=' . $gContent->mInfo['blog_id'] . '&post_id=' . $gContent->mPostId;
$uri2 = httpPrefix(). $parts['path'] . '/' . $gContent->mInfo['blog_id'] . '/' . $gContent->mPostId;
$smarty->assign('uri', $uri);
$smarty->assign('uri2', $uri2);

if (!isset($_REQUEST['offset']))
	$_REQUEST['offset'] = 0;

if (!isset($_REQUEST['sort_mode']))
	$_REQUEST['sort_mode'] = 'created_desc';

if (!isset($_REQUEST['find']))
	$_REQUEST['find'] = '';

$smarty->assign('offset', $_REQUEST["offset"]);
$smarty->assign('sort_mode', $_REQUEST["sort_mode"]);
$smarty->assign('find', $_REQUEST["find"]);
$offset = $_REQUEST["offset"];
$sort_mode = $_REQUEST["sort_mode"];
$find = $_REQUEST["find"];

$parsed_data = $gContent->parseData();

if (!isset($_REQUEST['page']))
	$_REQUEST['page'] = 1;

$pages = $gContent->getNumberOfPages($parsed_data);
$parsed_data = $gContent->getPage($parsed_data, $_REQUEST['page']);
$smarty->assign('pages', $pages);

if ($pages > $_REQUEST['page']) {
	$smarty->assign('next_page', $_REQUEST['page'] + 1);
} else {
	$smarty->assign('next_page', $_REQUEST['page']);
}

if ($_REQUEST['page'] > 1) {
	$smarty->assign('prev_page', $_REQUEST['page'] - 1);
} else {
	$smarty->assign('prev_page', 1);
}

$smarty->assign('first_page', 1);
$smarty->assign('last_page', $pages);
$smarty->assign('page', $_REQUEST['page']);

$smarty->assign('parsed_data', $parsed_data);

$smarty->assign('individual', 'n');

if ($gBitUser->object_has_one_permission($_REQUEST["blog_id"], 'blog')) {
	$smarty->assign('individual', 'y');

	if (!$gBitUser->isAdmin()) {
		// Now get all the permissions that are set for this type of permissions 'image gallery'
		$perms = $gBitUser->getPermissions('', 'blogs');

		foreach ($perms["data"] as $perm) {
			$perm_name = $perm["perm_name"];

			if ($gBitUser->object_has_permission($user, $_REQUEST["blog_id"], 'blog', $perm_name)) {
				$$perm_name = 'y';

				$smarty->assign("$perm_name", 'y');
			} else {
				$$perm_name = 'n';

				$smarty->assign("$perm_name", 'n');
			}
		}
	}
}

if ($gBitUser->hasPermission( 'bit_p_blog_admin' )) {
	$bit_p_create_blogs = 'y';

	$smarty->assign('bit_p_create_blogs', 'y');
	$bit_p_blog_post = 'y';
	$smarty->assign('bit_p_blog_post', 'y');
	$bit_p_read_blog = 'y';
	$smarty->assign('bit_p_read_blog', 'y');
}

$gBitSystem->verifyPermission( 'bit_p_read_blog' );
/*if (!$gBitUser->hasPermission( 'bit_p_read_blog' )) {
	$smarty->assign('msg', tra("Permission denied you can not view this section"));

	$gBitSystem->display( 'error.tpl' );
	die;
}*/

$smarty->assign('ownsblog', ( $gBitUser->isValid() && $gBitUser->mUserId == $gContent->mInfo["user_id"] ) ? 'y' : 'n' );

if ($gBitSystem->isFeatureActive( 'feature_blogposts_comments' ) ) {
	$comments_at_top_of_page = 'n';
	  
	$maxComments = $gBitSystem->getPreference( 'blog_comments_per_page' );
	if (!empty($_REQUEST["comments_maxComments"])) {
		$maxComments = $_REQUEST["comments_maxComments"];
		$comments_at_top_of_page = 'y';
	}
	$comments_sort_mode = $gBitSystem->getPreference( 'wiki_comments_default_ordering' );
	if (!empty($_REQUEST["comments_sort_mode"])) {
		$comments_sort_mode = $_REQUEST["comments_sort_mode"];
		$comments_at_top_of_page = 'y';
	}

	$comments_display_style = 'flat';
	if (!empty($_REQUEST["comments_style"])) {
		$comments_display_style = $_REQUEST["comments_style"];
		$comments_at_top_of_page = 'y';
	}

	$comments_return_url = $PHP_SELF."?post_id=".$gContent->mPostId;
	$commentsParentId = $gContent->mInfo['content_id'];
	include_once ( LIBERTY_PKG_PATH.'comments_inc.php' );
}

$section = 'blogs';

if( $gBitSystem->isFeatureActive( 'feature_theme_control' ) ) {
	$cat_type = 'blog';

	$cat_objid = $gContent->mContentId;
	include( THEMES_PKG_PATH.'tc_inc.php' );
}

if( $gBitSystem->isPackageActive( 'categories' ) ) {
	$cat_obj_type = BITBLOGPOST_CONTENT_TYPE_GUID;
	$cat_objid = $gContent->mContentId;
	include_once( CATEGORIES_PKG_PATH.'categories_display_inc.php' );
}

if ( $gBitSystem->isPackageActive( 'notepad' ) && $gBitUser->isValid() && isset($_REQUEST['savenotepad'])) {
	
	$gBitSystem->replace_note($user, 0, $gContent->mInfo['title'] ? $gContent->mInfo['title'] : date("d/m/Y [h:i]", $gContent->mInfo['created']), $gContent->mInfo['data']);
}


$gBitSystem->setBrowserTitle($gContent->mInfo['title'].' - '.$gContent->mInfo['blogtitle']);
// Display the template
$gBitSystem->display( 'bitpackage:blogs/view_blog_post.tpl');

?>
