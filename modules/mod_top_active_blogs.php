<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_top_active_blogs.php,v 1.5 2006/02/03 17:23:54 squareing Exp $
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

$ranking = $gBlog->list_blogs(0, $params['rows'], 'activity_desc', '', $gQueryUserId, 'b.`activity` IS NOT NULL');
$gBitSmarty->assign('modTopActiveBlogs', $ranking["data"]);
?>
