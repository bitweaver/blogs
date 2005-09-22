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
<div class="display blogs">
	<div class="header">
		<h1>{tr}Send blog post{/tr}</h1>
	</div>

	<div class="body">
		{if $sent eq 'y'}
			{formfeedback success="{tr}A link to this post was sent to the following addresses{/tr}: $addresses"}</h3>
		{else}
			{form legend="Send post to these addresses"}
				<input type="hidden" name="post_id" value="{$post_info.post_id}" />
				<div class="row">
					{formlabel label="Email Addresses" for="addresses"}
					{forminput}
						<textarea cols="60" rows="2" name="addresses" id="addresses">{$addresses|escape}</textarea>
						{formhelp note="List of email addresses separated by commas."}
					{/forminput}
				</div>

				<div class="row submit">
					<input type="submit" name="send" value="{tr}Send{/tr}" />
				</div>
			{/form}
		{/if}
	</div><!-- end .body -->
</div><!-- end .blogs -->

{include file="bitpackage:blogs/view_blog_post.tpl"}
