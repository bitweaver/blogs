<?php
/**
 * @version $Header$
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_CLASS_PATH.'BitBlogPost.php' );

// moduleParams contains lots of goodies: extract for easier handling
extract( $moduleParams );

$date_start = NULL;
if( !empty($module_params['date_start_offset']) ){
	//offset is passed as number of hours
	$date_start = time() - ( $module_params['date_start_offset'] * 3600 );
}

$defaultsHash = array(
	'sort_mode'   => ( !empty( $module_params['sort_mode'] ) ? $module_params['sort_mode'] : 'publish_date_desc' ),
	'max_records' => $module_rows,
	'parse_data'  => TRUE,
	'user'        => ( !empty( $module_params['user'] ) ? $module_params['user'] : NULL ),
	'blog_id'     => ( @BitBase::verifyId( $module_params['blog_id'] ) ? $module_params['blog_id'] : NULL ),
	'role_id'     => ( @BitBase::verifyId( $module_params['role_id'] ) ? $module_params['role_id'] : NULL ),
	'group_id'    => ( @BitBase::verifyId( $module_params['group_id'] ) ? $module_params['group_id'] : NULL ),
	'date_start'  =>  $date_start,
	'offset'	  => ( !empty( $module_params['offset'] ) ? $module_params['offset'] : 0 ),
);

$listHash = array_merge($module_params, $defaultsHash);

if(( empty( $module_params['include'] ) || $module_params['include'] != 'all' ) && !empty( $gQueryUserId )) {
	$listHash['user_id'] = $gQueryUserId;
}

if( !$gBitUser->hasPermission( 'p_blogs_admin' )) {
	$listHash['content_perm_name'] = 'p_blogs_view';
}

// we dont want admin drafts included in regular lists if we are enforcing content status
$listHash['enforce_status'] = TRUE;

if ( !empty( $module_params['status'] ) && $module_params['status'] = "draft" && isset( $gBitUser->mUserId ) ){
	// if we are getting drafts then get future posts too
    $listHash['show_future'] = TRUE;
	$listHash['min_status_id'] = -6;
	$listHash['max_status_id'] = -4;
	$listHash['min_owner_status_id'] = -6;
	// limit by user
	$listHash['user_id'] = $gBitUser->mUserId;
}else{
	$listHash['min_owner_status_id'] = 0;
}


$blogPost = new BitBlogPost();
$blogPosts = $blogPost->getList( $listHash );
$descriptionLength = ( !empty( $module_params['max_preview_length'] ) ? $module_params['max_preview_length'] : 500 );

$_template->tpl_vars['blogPostsFormat'] = new Smarty_variable( (empty($module_params['format']) ) );
$_template->tpl_vars['descriptionLength'] = new Smarty_variable( $descriptionLength );
$_template->tpl_vars['modLastBlogPosts'] = new Smarty_variable( $blogPosts );
