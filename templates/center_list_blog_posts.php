<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/templates/center_list_blog_posts.php,v 1.34 2009/09/02 17:41:56 spiderr Exp $
 * @package bitweaver
 */
global $gBitSmarty, $gBlog, $gBitSystem, $gQueryUserId, $moduleParams, $gBitUser;
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
	// prevent anything lower than publicly visible be displayed in blog roll
	$futuresHash['enforce_status'] = TRUE;
	$futuresHash['min_owner_status_id'] = 0;
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
	$listHash['content_perm_name'] = 'p_blogs_view';
}


$paginationPath = BLOGS_PKG_URL.'index.php';

/* if a blog_id is passed from the modle settings then
 * we want to push to the view page if the user looks for older posts
 * i.e. this is for pagination
 */

if ( !empty( $module_params ) && !empty( $module_params['blog_id'] ) ){
	$gBitSmarty->assign( 'blogId', $module_params['blog_id']  );
	$paginationPath = BLOGS_PKG_URL.'view.php';
}

// prevent anything lower than publicly visible be displayed in blog roll
$listHash['enforce_status'] = TRUE;
$listHash['min_owner_status_id'] = 0;

/* I think this is right - usually we pass in $_REQUEST
 * but in this case I pass in the listHash because 
 * this is in a module - change it if its a mistake wjames5
 */
$blogPost->invokeServices( 'content_list_function', $listHash );
$blogPosts = $blogPost->getList( $listHash );
$gBitSmarty->assign( 'paginationPath', $paginationPath );
$gBitSmarty->assign( 'showEmpty', !empty( $moduleParams['module_params']['show_empty'] ) );
$gBitSmarty->assign_by_ref( 'gQueryUserId', $listHash['user_id'] );
$gBitSmarty->assign_by_ref( 'blogPosts', $blogPosts["data"] );
$gBitSmarty->assign( 'listInfo', $blogPosts['listInfo'] );
$gBitSmarty->assign( 'descriptionLength', $gBitSystem->getConfig( 'blog_posts_description_length', 500 ));
$gBitSmarty->assign( 'showDescriptionsOnly', TRUE );
$gBitSmarty->assign( 'showBlogTitle', 'y' );
$gBitSmarty->assign( 'blogPostsFormat', (empty($module_params['format']) ? 'full' : $module_params['format']) );
// unfortunately, the following feature pulls module parameters in from other modules
//$gBitSmarty->assign( 'centerTitle', $moduleParams['title'] );
?>
