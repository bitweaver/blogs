<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_blog_posts.php,v 1.11 2007/04/04 12:43:30 squareing Exp $
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_PATH.'BitBlogPost.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

// moduleParams contains lots of goodies: extract for easier handling
extract( $moduleParams );

$listHash = array(
	'sort_mode'   => 'created_desc',
	'max_records' => $module_rows,
	'parse_data'  => TRUE,
	'user'        => ( !empty( $module_params['user'] ) ? $module_params['user'] : NULL ),
	'blog_id'     => ( @BitBase::verifyId( $module_params['blog_id'] ) ? $module_params['blog_id'] : NULL ),
);

if(( empty( $module_params['include'] ) || $module_params['include'] != 'all' ) && !empty( $gQueryUserId )) {
	$listHash['user_id'] = $gQueryUserId;
}

$blogPost = new BitBlogPost();
$ranking = $blogPost->getList( $listHash );

$maxPreviewLength = ( !empty( $module_params['max_preview_length'] ) ? $module_params['max_preview_length'] : 500 );
$gBitSmarty->assign( 'maxPreviewLength', $maxPreviewLength );
$gBitSmarty->assign( 'modLastBlogPosts', $ranking["data"] );
// not sure what this is, but using title doesn't work cos that will rename the moduleTitle
//$gBitSmarty->assign( 'modLastBlogPostsTitle', ( isset( $module_params["title"] ) ? $module_params["title"]:"" ));
?>
