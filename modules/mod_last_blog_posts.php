<?php
/**
 * Params: 
 * - title : if is "title", show the title of the post, else show the date of creation
 *
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_blog_posts.php,v 1.10 2007/04/03 14:45:56 squareing Exp $
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
if( !defined( 'MAX_BLOG_PREVIEW_LENGTH' ) ) {
	define ('MAX_BLOG_PREVIEW_LENGTH', 100);
}

include_once( BLOGS_PKG_PATH.'BitBlogPost.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

// moduleParams contains lots of goodies: extract for easier handling
extract( $moduleParams );

$listHash = array( 'user_id' => $gQueryUserId, 'sort_mode' => 'created_desc', 'max_records' => $module_rows, 'parse_data' => TRUE );

$listHash['user'] = isset($module_params['user']) ? $module_params['user']:NULL;
if ( isset($module_params['id']) ){ $listHash['blog_id'] = $module_params['id'];}

$blogPost = new BitBlogPost();
$ranking = $blogPost->getList( $listHash );

$maxPreviewLength = (!empty($module_params['max_preview_length']) ? $module_params['max_preview_length'] : MAX_BLOG_PREVIEW_LENGTH);
//DEPRECATED Slated for removal -wjames5
/*
$user_blog_id = NULL;
if( count( $ranking['data'] ) ) {
	$user_blog_id = $ranking['data'][0]['blog_id'];
}
$gBitSmarty->assign('user_blog_id', $user_blog_id);
*/
$gBitSmarty->assign('maxPreviewLength', $maxPreviewLength);
$gBitSmarty->assign('modLastBlogPosts', $ranking["data"]);
$gBitSmarty->assign('modLastBlogPostsTitle',(isset($module_params["title"])?$module_params["title"]:""));
$gBitSmarty->assign('blogsPackageActive', $gBitSystem->isPackageActive('blogs'));
?>
