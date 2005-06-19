<?php
// $Header: /cvsroot/bitweaver/_bit_blogs/modules/mod_top_visited_blogs.php,v 1.1 2005/06/19 03:57:42 bitweaver Exp $
include_once( BLOGS_PKG_PATH.'BitBlog.php' );
require_once( USERS_PKG_PATH.'BitUser.php' );

global $gBlog, $gQueryUserId, $modlib;

$params = $modlib->get_module_params('bitpackage:blogs/mod_top_visited_blogs.tpl', $gQueryUserId);
$ranking = $gBlog->list_blogs(0, $params['rows'], 'hits_desc', '',$gQueryUserId,' `hits` IS NOT NULL ');

$smarty->assign('modTopVisitedBlogs', $ranking["data"]);
$smarty->assign('bulletSrc', isset($params["bullet"]) ? $params['bullet'] : NULL);
?>
