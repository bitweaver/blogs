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

global $gQueryUserId, $moduleParams;
//$params = $moduleParams['module_params'];

$listHash['max_records'] = $moduleParams['module_rows'];
$listHash['sort_mode'] = 'last_modified_desc';
BitUser::userCollection( $moduleParams, $listHash );

$blog = new BitBlog();
$ranking = $blog->getList( $listHash );

$_template->tpl_vars['modLastModifiedBlogs'] = new Smarty_variable( $ranking);
?>
