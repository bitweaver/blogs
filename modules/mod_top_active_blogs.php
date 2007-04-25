<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_top_active_blogs.php,v 1.9 2007/04/25 16:17:57 wjames5 Exp $
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

global $gBlog, $gQueryUserId, $gBitThemes, $module_rows;

$params = $gBitThemes->getModuleParameters('bitpackage:blogs/mod_top_active_blogs.tpl', $gQueryUserId);

$listHash['max_records'] = $params['module_rows'];
$listHash['sort_mode'] = 'activity_desc';
$listHash['user_id'] = $gQueryUserId;
$listHash['is_active'] = TRUE;
if( @BitBase::verifyId( $module_params['group_id'] ) ) {
	$listHash['group_id'] = $module_params['group_id'];
}
$ranking = $gBlog->getList( $listHash );
if( !empty( $ranking['data'] ) ) {
	$gBitSmarty->assign('modTopActiveBlogs', $ranking["data"]);
}
?>
