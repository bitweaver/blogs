<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_top_active_blogs.php,v 1.1.1.1.2.2 2005/07/26 15:50:02 drewslater Exp $
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

global $gBlog, $gQueryUserId, $modlib;

$params = $modlib->get_module_params('bitpackage:blogs/mod_top_active_blogs.tpl', $gQueryUserId);

$ranking = $gBlog->list_blogs(0, $params['rows'], 'activity_desc', '', $gQueryUserId, 'tb.`activity` IS NOT NULL');
$gBitSmarty->assign('modTopActiveBlogs', $ranking["data"]);
?>
