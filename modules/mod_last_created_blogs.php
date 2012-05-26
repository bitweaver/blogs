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

global $gQueryUserId, $module_rows, $module_params;

$listHash['max_records'] = $module_rows;
$listHash['sort_mode'] = 'created_desc';
BitUser::userCollection( $module_params, $listHash );

$blog = new BitBlog();
$ranking = $blog->getList( $listHash );
if( !empty( $ranking['data'] ) ) {
	$gBitSmarty->assign('modLastCreatedBlogs', $ranking["data"]);
}
?>
