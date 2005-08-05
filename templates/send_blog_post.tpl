<!--
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns:dc="http://purl.org/dc/elements/1.1/"
         xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
<rdf:Description
    rdf:about="{$uri}"
    dc:identifer="{$uri}"
    dc:title="{if $blog_data.use_title eq 'y'}{$post_info.title} {tr}posted by{/tr} {$post_info.user} on {$post_info.created|bit_short_datetime}{else}{$post_info.created|bit_short_datetime}{tr}posted by{/tr} {$post_info.user}{/if}"
    trackback:ping="{$uri2}" />
</rdf:RDF>
-->
<h2>{tr}Send blog post{/tr}</h2>
{if $sent eq 'y'}
<h3>{tr}A link to this post was sent to the following addresses:{/tr}</h3>
<div class="wikibody">
{$addresses}
</div>
{else}
	<h3>{tr}Send post to these addresses{/tr}</h3>
	<form method="post" action="{$smarty.const.BLOGS_PKG_URL}send_post.php">
	<input type="hidden" name="post_id" value="{$post_info.post_id}" />
	<table class="panel">
	<tr>
		<td>{tr}List of email addresses separated by commas{/tr}</td>
		<td><textarea cols="60" rows="5" name="addresses">{$addresses|escape}</textarea></td>
	</tr>
	<tr class="panelsubmitrow">
		<td colspan="2"><input type="submit" name="send" value="{tr}send{/tr}" /></td>
	</tr>
	</table>
	</form>
{/if}	

{include file="bitpackage:blogs/view_blog_post.tpl"}
