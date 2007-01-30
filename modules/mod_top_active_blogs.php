<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_top_active_blogs.php,v 1.8 2007/01/30 23:09:27 spiderr Exp $
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
$ranking = $gBlog->getList( $listHash );
if( !empty( $ranking['data'] ) ) {
	$gBitSmarty->assign('modTopActiveBlogs', $ranking["data"]);
}
?>
