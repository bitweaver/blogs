<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_last_created_blogs.php,v 1.1.1.1.2.1 2005/06/27 10:08:44 lsces Exp $
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

global $gBlog, $gQueryUserId, $module_rows;
$ranking = $gBlog->list_blogs(0, $module_rows, 'created_desc', '', $gQueryUserId);

$smarty->assign('modLastCreatedBlogs', $ranking["data"]);
?>
