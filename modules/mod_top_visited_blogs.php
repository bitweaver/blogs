<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_top_visited_blogs.php,v 1.6 2006/10/11 06:05:13 spiderr Exp $
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

global $gBlog, $gQueryUserId, $gBitThemes;

$params = $gBitThemes->getModuleParameters('bitpackage:blogs/mod_top_visited_blogs.tpl', $gQueryUserId);
$listHash['max_records'] = $params['module_rows'];
$listHash['sort_mode'] = 'hits_desc';
$listHash['user_id'] = $gQueryUserId;
$listHash['is_hit'] = TRUE;
$ranking = $gBlog->getList( $listHash );

$gBitSmarty->assign('modTopVisitedBlogs', $ranking["data"]);
$gBitSmarty->assign('bulletSrc', isset($params["bullet"]) ? $params['bullet'] : NULL);
?>
