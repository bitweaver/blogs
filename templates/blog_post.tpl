{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/blog_post.tpl,v 1.22 2007/03/22 19:59:05 wjames5 Exp $ *}
{strip}
<div class="edit blogs">
	<div class="header">
		<h1>{tr}Edit Post{/tr}</h1>
	</div>

	<div class="body">
		{if $preview eq 'y'}
			<h2>Preview {$title}</h2>
			<div class="preview">
				{include file="bitpackage:blogs/view_blog_post.tpl"}
			</div>
		{/if}

		{form enctype="multipart/form-data" name="blogpost" id="editpageform"}
			<input type="hidden" name="post_id" value="{$post_id|escape}" />
			<input type="hidden" name="blog_id" value="{$blog_id|escape}" />
			<input type="hidden" name="rows" value="{$rows}"/>
			<input type="hidden" name="cols" value="{$cols}"/>

			{jstabs}
				{jstab title="Create Blog Post"}
					{legend legend="Post"}
						{if !$blog_data.use_title OR $blog_data.use_title eq 'y'}
							<div class="row">
								{formlabel label="Title" for="title"}
								{forminput}
									<input type="text" size="50" name="title" id="title" value="{$gContent->getTitle()|escape}" />
									{formhelp note="When you leave the title blank, the current date will be substituted automagically."}
								{/forminput}
							</div>
						{/if}

						{include file="bitpackage:liberty/edit_format.tpl"}

						{if $gBitSystem->isPackageActive( 'smileys' )}
							{include file="bitpackage:smileys/smileys_full.tpl"}
						{/if}

						{if $gBitSystem->isPackageActive( 'quicktags' )}
							{include file="bitpackage:quicktags/quicktags_full.tpl"}
						{/if}

						<div class="row">
							{forminput}
								<textarea {spellchecker} id="{$textarea_id}" name="edit" rows="{$smarty.cookies.rows|default:20}" cols="50">{$gContent->getField('data')|escape:html}</textarea>
							{/forminput}
						</div>

						{if $availableBlogs}
							<div class="row">
								{formlabel label="Include in Blogs" for="blog_id"}
								{forminput}
								{*
									{if count($availableBlogs)==1}
										{foreach from=$availableBlogs key=blogContentId item=availBlogTitle}
											<input type="hidden" name="blog_content_id[]" value="{$blogContentId}">{$availBlogTitle}
										{/foreach}
									{else}
									*}
										{foreach from=$availableBlogs key=blogContentId item=availBlogTitle}
											<input name="blog_content_id[]" type="checkbox" option value="{$blogContentId}" {if $gContent->mInfo.blogs.$blogContentId}checked="checked"{/if}>{$availBlogTitle|escape}</option><br/>
										{/foreach}
										{formhelp note="You can cross post to any and all of the blogs listed above.<br />Just check off the blogs you wish this post to also show up in."}
								{*	{/if} *}
								{/forminput}
							</div>
						{/if}
						
						{include file="bitpackage:liberty/edit_services_inc.tpl serviceFile=content_edit_mini_tpl}

						{include file="bitpackage:liberty/edit_storage_list.tpl"}

						<div class="row submit">
							<input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;
							<input type="submit" name="save_post_exit" value="{tr}Save{/tr}" />
						</div>
					{/legend}
				{/jstab}

				{include file="bitpackage:liberty/edit_services_inc.tpl serviceFile=content_edit_tab_tpl}

				{jstab title="Attachments"}
					{legend legend="Attachments"}
						{include file="bitpackage:liberty/edit_storage.tpl"}
					{/legend}
				{/jstab}

				{jstab title="Advanced Options"}
					{legend legend="Publication and Expiration Dates"}
						<div class="row">
							<input type="hidden" name="publishDateInput" value="1" />
							{formlabel label="Publish Date" for=""}
							{forminput}
								{html_select_date prefix="publish_" time=$post_info.publish_date start_year="-5" end_year="+10"} {tr}at{/tr}&nbsp;
								<span dir="ltr">{html_select_time prefix="publish_" time=$post_info.publish_date display_seconds=false}&nbsp;{$siteTimeZone}</span>
								{formhelp note="This post will not be displayed <strong>before</strong> this date."}
							{/forminput}
						</div>

						<div class="row">
							<input type="hidden" name="expireDateInput" value="1" />
							{formlabel label="Expiration Date" for=""}
							{forminput}
								{html_select_date prefix="expire_" time=$post_info.expire_date start_year="-5" end_year="+10"} {tr}at{/tr}&nbsp;
								<span dir="ltr">{html_select_time prefix="expire_" time=$post_info.expire_date display_seconds=false}&nbsp;{$siteTimeZone}</span>
								{formhelp note="If this date is set after the publish date, this post will not be displayed <strong>after</strong> the expiration date."}
							{/forminput}
						</div>
					{/legend}
					
					{legend legend="Trackbacks"}
						<div class="row">
							{formlabel label="Send trackback pings" for="trackback"}
							{forminput}
								<textarea name="trackback" id="trackback" rows="3" cols="50">{section name=ix loop=$trackbacks_to}{if not $smarty.section.ix.first},{/if}{$trackbacks_to[ix]}{/section}</textarea>
								{formhelp note="Insert a comma separated list of URIs to send blogs."}
							{/forminput}
						</div>
					{/legend}
				{/jstab}
			{/jstabs}
		{/form}

		<br /><br />
		{include file="bitpackage:liberty/edit_help_inc.tpl"}

	</div><!-- end .body -->
</div><!-- end .blogs -->
{/strip}
