<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/templates/center_list_blog_posts.php,v 1.24 2007/11/13 04:29:20 wjames5 Exp $
 * @package bitweaver
 */
global $gBitSmarty, $gBlog, $gBitSystem, $gQueryUserId, $moduleParams;
if( !empty( $moduleParams ) ) {
	extract( $moduleParams );
}

include_once( BLOGS_PKG_PATH.'BitBlog.php' );
include_once( BLOGS_PKG_PATH.'lookup_post_inc.php' );

$blogPost = new BitBlogPost();
if( empty( $gContent )) {
	$gBitSmarty->assign_by_ref( 'gContent', $blogPost );
}

if( $gBitUser->hasPermission( 'p_blog_posts_read_future' ) || $gBitUser->isAdmin() ) {
	$futuresHash = array();
    $futuresHash['max_records'] = !empty( $_REQUEST['max_records'] ) ? $_REQUEST['max_records'] : $gBitSystem->getConfig( 'blog_posts_max_list' );
    $futuresHash['get_future'] = TRUE;
	if( empty( $futuresHash['user_id'] )) {
		if( !empty( $gQueryUserId )) {
			$futuresHash['user_id'] = $gQueryUserId;
		} elseif( $_REQUEST['user_id'] ) {
			$futuresHash['user_id'] = $_REQUEST['user_id'];
		}
	}
    $futures = $blogPost->getFutureList( $futuresHash );
    $gBitSmarty->assign( 'futures', $futures['data']);
} else {
    $_REQUEST['max_records'] = $gBitSystem->getConfig( 'blog_posts_max_list' );
}

// various options might be set in module_params
/*
$listHash = $module_params;
$listHash = array(
	'max_records'   => $module_rows,
	'parse_data'    => TRUE,
	'load_comments' => TRUE,
);
*/

$listHash = array();
if( !empty( $moduleParams )) {
    $listHash = array_merge( $_REQUEST, $moduleParams['module_params'] );
	$listHash['max_records'] = $module_rows;
	//$listHash['parse_data'] = TRUE;
	//$listHash['load_comments'] = TRUE;
} else {
    $listHash = $_REQUEST;
}

if( empty( $listHash['user_id'] )) {
	if( !empty( $gQueryUserId )) {
		$listHash['user_id'] = $gQueryUserId;
	} elseif( $_REQUEST['user_id'] ) {
		$listHash['user_id'] = $_REQUEST['user_id'];
	}
}
if ( empty( $listHash['sort_mode'] ) ){
	$listHash['sort_mode'] = 'sort_date_desc';
}
if( @BitBase::verifyId( $_REQUEST['group_id'] ) ) {
	$listHash['group_id'] = $_REQUEST['group_id'];
}

if( !$gBitUser->hasPermission( 'p_blogs_admin' )) {
//	$listHash['user_id'] = $gBitUser->mUserId;
	$listHash['content_perm_name'] = 'p_blogs_view';
}

/* I think this is right - usually we pass in $_REQUEST
 * but in this case I pass in the listHash because 
 * this is in a module - change it if its a mistake wjames5
 */
$blogPost->invokeServices( 'content_list_function', $listHash );
$blogPosts = $blogPost->getList( $listHash );

$gBitSmarty->assign( 'showEmpty', TRUE );
$gBitSmarty->assign_by_ref( 'gQueryUserId', $listHash['user_id'] );
$gBitSmarty->assign_by_ref( 'blogPosts', $blogPosts["data"] );
$gBitSmarty->assign( 'listInfo', $blogPosts['listInfo'] );
$gBitSmarty->assign( 'descriptionLength', $gBitSystem->getConfig( 'blog_posts_description_length', 500 ));
$gBitSmarty->assign( 'showDescriptionsOnly', TRUE );
$gBitSmarty->assign( 'showBlogTitle', 'y' );
$gBitSmarty->assign( 'blogPostsFormat', (empty($module_params['format']) ? 'full' : $module_params['format']) );
?>
