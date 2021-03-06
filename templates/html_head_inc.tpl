{* $Header$ *}
{strip}
{if $gBitSystem->isPackageActive( 'rss' ) and $gBitSystem->isFeatureActive( 'blogs_rss' ) and $gBitSystem->getActivePackage() eq 'blogs' and $gBitUser->hasPermission( 'p_blogs_view' )}
	{if isset($gContent->mBlogId)}
		<link rel="alternate" type="application/rss+xml" title="{$gContent->getTitle()}" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?blog_id={$gContent->blog_content_id}&amp;version={$gBitSystem->getConfig('rssfeed_default_version',0)}" />
	{elseif isset($post_info.blogs)}
		{foreach from=$post_info.blogs item=memberBlog key=blogContentId name=memberBlogLoop}
			<link rel="alternate" type="application/rss+xml" title="{$memberBlog.title}" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?blog_id={$memberBlog.blog_id}&amp;version={$gBitSystem->getConfig('rssfeed_default_version',0)}" />
		{/foreach}
	{else}
		<link rel="alternate" type="application/rss+xml" title="{$gBitSystem->getConfig('blogs_rss_title',"{tr}Blogs{/tr} RSS")}" href="{$smarty.const.BLOGS_PKG_URL}blogs_rss.php?version={$gBitSystem->getConfig('rssfeed_default_version',0)}" />
	{/if}
{/if}
{* this is for ajaxing the readmore portion of blog posts. 
 * this is ugly, but because recent posts are handled by dynamic center outside of the blog package we have 
 * no way to assign the appropriate ajax library needs the propery way - so we force it here.
 *
 * this is configured for using center_list_blog_posts in the user pkg, as this is a common configuration,
 * but it can be used in other pkgs, like wiki for example. Expand this set of conditionals as
 * needed for your site configuation by creating a custom version of this tpl in your theme. Target the 
 * conditionals to the pkg you are including center_list_blog_posts in.
 *}
{if $ajax_more}
{* @TODO develop custom callback - for now override ajax callback for cool scroll effect *}
{literal}
<script type="text/javascript">/* <![CDATA[ */
	BitAjax.updaterCallback = function(target, rslt){
		BitBase.hideSpinner();
		var e = document.getElementById(target);
		if (e != null){
			e.style.display = 'none';
			e.innerHTML = rslt.responseText;
			MochiKit.Visual.blindDown( e, {duration:1} );
		}
	}
/* ]]> */</script>
{/literal}
{/if}
{/strip}
