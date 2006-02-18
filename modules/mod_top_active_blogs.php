<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_top_active_blogs.php,v 1.6 2006/02/18 20:37:26 spiderr Exp $
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

global $gBlog, $gQueryUserId, $gBitThemes;

$params = $gBitThemes->getModuleParameters('bitpackage:blogs/mod_top_active_blogs.tpl', $gQueryUserId);

$ranking = $gBlog->list_blogs(0, $params['module_rows'], 'activity_desc', '', $gQueryUserId, 'b.`activity` IS NOT NULL');
$gBitSmarty->assign('modTopActiveBlogs', $ranking["data"]);
?>
