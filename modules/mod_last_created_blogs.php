<?php
/**
 * @version $Header$
 * @package blogs
 * @subpackage modules
 */

/**
 * required setup
 */
include_once( BLOGS_PKG_CLASS_PATH.'BitBlog.php' );

global $gQueryUserId, $module_rows, $module_params;

$listHash['max_records'] = $module_rows;
$listHash['sort_mode'] = 'created_desc';
BitUser::userCollection( $module_params, $listHash );

$blog = new BitBlog();
$_template->tpl_vars['modLastCreatedBlogs'] = new Smarty_variable( $blog->getList( $listHash ) );
?>
