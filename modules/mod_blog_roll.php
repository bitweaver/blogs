<?php
/**
 * @version $Header$
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_PATH.'BitBlog.php' );
//require_once( USERS_PKG_PATH.'BitUser.php' );

global $gQueryUserId, $module_rows, $moduleParams;

$listHash['max_records'] = $module_rows;
$listHash['sort_mode'] = ( !empty( $moduleParams['module_params']['sort_mode'] ) ) ? $moduleParams['module_params']['sort_mode'] : 'created_desc';
BitUser::userCollection( $moduleParams['module_params'], $listHash );

$blog = new BitBlog();
if( $modBlogs = $blog->getList( $listHash ) ) {
	foreach( array_keys( $modBlogs ) as $b ) {
		$modBlogs[$b]['post'] = $blog->getPost( array( 'blog_id' => $modBlogs[$b]['blog_id'] ) );
	}
	$gBitSmarty->assign( 'modBlogs', $modBlogs );
}

$moduleTitle = (!empty( $moduleParams['title'] ) ? $moduleParams['title'] : 'Blog Roll');
$gBitSmarty->assign( 'moduleTitle', $moduleTitle );
?>
