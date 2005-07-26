<?php
/**
 * Params: 
 * - title : if is "title", show the title of the post, else show the date of creation
 *
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_blog_posts.php,v 1.1.1.1.2.2 2005/07/26 15:50:02 drewslater Exp $
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
if( !defined( 'MAX_BLOG_PREVIEW_LENGTH' ) ) {
	define ('MAX_BLOG_PREVIEW_LENGTH', 100);
}

include_once( BLOGS_PKG_PATH.'BitBlog.php' );
include_once( BLOGS_PKG_PATH.'BitBlogPost.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );
require_once( KERNEL_PKG_PATH.'mod_lib.php' );

global $gBitSmarty, $gBlog, $modlib, $gQueryUserId, $module_rows, $module_params, $gBitSystem;

$listHash = array( 'user_id' => $gQueryUserId, 'sort_mode' => 'created_desc', 'max_records' => $module_rows, 'parse_data' => TRUE );
$blogPost = new BitBlogPost();
$ranking = $blogPost->getList( $listHash );

$modParams = $modlib->get_module_params('bitpackage:blogs/mod_last_blog_posts.tpl', $gQueryUserId);

$maxPreviewLength = (!empty($modParams['max_preview_length']) ? $modParams['max_preview_length'] : MAX_BLOG_PREVIEW_LENGTH);
$user_blog_id = NULL;
if( count( $ranking['data'] ) ) {
	$user_blog_id = $ranking['data'][0]['blog_id'];
}
$gBitSmarty->assign('user_blog_id', $user_blog_id);

$gBitSmarty->assign('maxPreviewLength', $maxPreviewLength);
$gBitSmarty->assign('modLastBlogPosts', $ranking["data"]);
$gBitSmarty->assign('modLastBlogPostsTitle',(isset($module_params["title"])?$module_params["title"]:""));
$gBitSmarty->assign('blogsPackageActive', $gBitSystem->isPackageActive('blogs'));
?>
