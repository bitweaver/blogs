<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_modified_blogs.php,v 1.7 2008/08/01 02:22:41 laetzer Exp $
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

global $gQueryUserId, $moduleParams;
//$params = $moduleParams['module_params'];

$listHash['max_records'] = $moduleParams['module_rows'];
$listHash['sort_mode'] = 'last_modified_desc';
$listHash['user_id'] = $gQueryUserId;
if( @BitBase::verifyId( $module_params['group_id'] ) ) {
	$listHash['group_id'] = $module_params['group_id'];
}
$blog = new BitBlog();
$ranking = $blog->getList( $listHash );

$gBitSmarty->assign('modLastModifiedBlogs', $ranking);
?>
