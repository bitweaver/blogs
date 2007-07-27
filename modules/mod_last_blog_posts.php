<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_blog_posts.php,v 1.16 2007/07/27 08:11:10 wjames5 Exp $
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

$date_start = NULL;
if( !empty($module_params['date_start_offset']) ){ 
	//offset is passed as number of hours
	$date_start = time() - ( $module_params['date_start_offset'] * 3600 );
}

$listHash = array(
	'sort_mode'   => ( !empty( $module_params['sort_mode'] ) ? $module_params['sort_mode'] : 'publish_date_desc' ),
	'max_records' => $module_rows,
	'parse_data'  => TRUE,
	'user'        => ( !empty( $module_params['user'] ) ? $module_params['user'] : NULL ),
	'blog_id'     => ( @BitBase::verifyId( $module_params['blog_id'] ) ? $module_params['blog_id'] : NULL ),
	'group_id'     => ( @BitBase::verifyId( $module_params['group_id'] ) ? $module_params['group_id'] : NULL ),
	'date_start'  =>  $date_start,
);

if(( empty( $module_params['include'] ) || $module_params['include'] != 'all' ) && !empty( $gQueryUserId )) {
	$listHash['user_id'] = $gQueryUserId;
}

$blogPost = new BitBlogPost();
$ranking = $blogPost->getList( $listHash );

$maxPreviewLength = ( !empty( $module_params['max_preview_length'] ) ? $module_params['max_preview_length'] : 500 );

$gBitSmarty->assign( 'modLastBlogPostsFormat', (empty($module_params['format']) ? 'list' : $module_params['format']) );
$gBitSmarty->assign( 'maxPreviewLength', $maxPreviewLength );
$gBitSmarty->assign( 'modLastBlogPosts', $ranking["data"] );
// not sure what this is, but using title doesn't work cos that will rename the moduleTitle
//$gBitSmarty->assign( 'modLastBlogPostsTitle', ( isset( $module_params["title"] ) ? $module_params["title"]:"" ));
?>
