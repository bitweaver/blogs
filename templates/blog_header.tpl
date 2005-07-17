<span class="blogtitle">{tr}Blog{/tr}: {$title}</span>
<div class="bloginfo">
{tr}Created by{/tr} {displayname hash=$creator}{tr} on {/tr}{$created|bit_short_datetime}<br />
{tr}Last modified{/tr} {$last_modified|bit_short_datetime}<br /><br />
<table>
<tr>
	<td> ({$posts} {tr}posts{/tr} | {$hits} {tr}visits{/tr} | {tr}Activity{/tr} {$activity|string_format:"%.2f"})</td>
    <td>
{if $gBitUser->hasPermission( 'bit_p_blog_post' )}
    {if ($gBitUser->mUserId and $creator eq $gBitUser->mUserId) or $gBitUser->hasPermission( 'bit_p_blog_admin' ) or $public eq 'y'}
	    <a title="{tr}post{/tr}" href="{$gBitLoc.BLOGS_PKG_URL}post.php?blog_id={$blog_id}">{biticon ipackage=liberty iname="post" iexplain="post"}</a>
	{/if}
{/if}
{if $gBitSystem->isPackageActive( 'rss' ) && $rss_blog eq 'y'}
    <a title="{tr}RSS feed{/tr}" href="{$gBitLoc.BLOGS_PKG_URL}rss.php?blog_id={$blog_id}">{biticon ipackage="rss" iname="rss" iexplain="RSS feed"}</a>
{/if}
{if ($gBitUser->isRegistered() and $creator eq $gBitUser->mUserId) or $gBitUser->hasPermission( 'bit_p_blog_admin' )}
    <a title="{tr}Edit blog{/tr}" href="{$gBitLoc.BLOGS_PKG_URL}edit.php?blog_id={$blog_id}">{biticon ipackage=liberty iname="edit" iexplain="edit"}</a>
{/if}
{if $gBitUser->isRegistered() and $gBitSystem->isFeatureActive( 'feature_user_watches' )}
    {if $user_watching_blog eq "n"}
	    <a title="{tr}monitor this blog{/tr}" href="{$gBitLoc.BLOGS_PKG_URL}view.php?blog_id={$blog_id}&amp;watch_event=blog_post&amp;watch_object={$blog_id}&amp;watch_action=add">{biticon ipackage="users" iname="watch" iexplain="monitor this blog"}</a>
    {else}<a title="{tr}stop monitoring this blog{/tr}" href="{$gBitLoc.BLOGS_PKG_URL}view.php?blog_id={$blog_id}&amp;watch_event=blog_post&amp;watch_object={$blog_id}&amp;watch_action=remove">{biticon ipackage="users" iname="unwatch" iexplain="stop monitoring this blog"}</a>
    {/if}
{/if}
	</td>
</tr>
</table>
</div>

<div class="blogdesc">{tr}Description:{/tr} {$description}</div>
