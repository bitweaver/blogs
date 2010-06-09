{* $Header$ *}
{strip}
<div class="edit blogs">
	<div class="header">
		{ if !$post_info.content_id }
			<h1>{tr}Create Post{/tr}</h1>
		{else}
			<h1>{tr}Edit Post{/tr}</h1>
		{/if}
	</div>

	<div class="body">
		{if $preview eq 'y'}
			<h2>Preview {$title}</h2>
			<div class="preview">
				{include file="bitpackage:blogs/view_blog_post.tpl"}
			</div>
		{/if}

		{form enctype="multipart/form-data" id="editpageform"}
			<input type="hidden" name="content_id" value="{$gContent->getField('content_id')}" />
			<input type="hidden" name="rows" value="{$rows}"/>
			<input type="hidden" name="cols" value="{$cols}"/>

			{jstabs}
				{jstab title="Blog Post"}
					{legend legend="Post"}
						{if !$blog_data.use_title OR $blog_data.use_title eq 'y'}
							<div class="row">
								{formlabel label="Title" for="title"}
								{forminput}
									<input type="text" size="50" name="title" id="title" value="{$post_info.title|escape}" />
									{formhelp note="When you leave the title blank, the current date will be substituted automagically."}
								{/forminput}
							</div>
						{/if}

							<div class="row">
								{formlabel label="Summary" for="summary"}
								{forminput}
									<input type="text" size="50" name="summary" id="summary" value="{$post_info.summary|escape}" />
									{formhelp note="Description used in listings and search results. If left empty, the first few sentences of the body text will be used."}
								{/forminput}
							</div>

						{if $gBitSystem->isFeatureActive( 'blog_posts_autosplit' )}
							{include file="bitpackage:liberty/edit_format.tpl"}
							{formlabel label="Intro" for="edit"}
							{formhelp note="Text entered here is the top half of your post."}
							{textarea noformat="y"}{$post_info.raw}{/textarea}

							{formlabel label="Body" for="edit_body"}
							{formhelp note="Text entered here will be displayed in the full blog post, commonly known as the Read More section."}
							{textarea id="edit_body" name="edit_body" noformat="y"}{$post_info.raw_more}{/textarea}
						{else}
							{textarea}{$post_info.raw}{/textarea}
						{/if}

						{if $availableBlogs}
							<div class="row">
								{formlabel label="Include in Blogs" for=""}
								{forminput}
									{if count($availableBlogs) > 10}
										<select name="blog_content_id[]" size="6" id="blog_id" multiple="multiple">
											{foreach from=$availableBlogs key=blogContentId item=availBlogTitle}
												<option value="{$blogContentId}" {if $gContent->mInfo.blogs.$blogContentId || $blogContentId == $smarty.request.blog_id}selected="selected"{/if}>{$availBlogTitle|escape}</option>
											{/foreach}
										</select>
									{else}
										{foreach from=$availableBlogs key=blogContentId item=availBlogTitle}
											<input name="blog_content_id[]" type="checkbox" value="{$blogContentId}" {if $gContent->mInfo.blogs.$blogContentId || $blogContentId == $smarty.request.blog_id || $blogContentId == $default_target_blog_content_id}checked="checked"{/if} /> {$availBlogTitle|escape}<br/>
										{/foreach}
									{/if}
									{formhelp note="You can cross post to any and all of the blogs listed above.<br />Just check off the blogs you wish this post to also show up in."}
								{/forminput}
							</div>
						{/if}

						{* here we assign edit_content_status_tpl to customize the status input presentation. this gets passed along to liberty::edit_service_mini_inc.tpl *}
						{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_mini_tpl" edit_content_status_tpl="bitpackage:blogs/edit_blogpost_status_inc.tpl"}

						{include file="bitpackage:liberty/edit_storage_list.tpl"}

						<div class="row submit">
							<input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;
							<input type="submit" name="save_post_exit" value="{tr}Save{/tr}" />
						</div>
					{/legend}
				{/jstab}

				{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_tab_tpl"}

				{if $gBitUser->hasPermission('p_liberty_attach_attachments') }
				{jstab title="Attachments"}
					{legend legend="Attachments"}
						{include file="bitpackage:liberty/edit_storage.tpl"}
					{/legend}
				{/jstab}
				{/if}

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
	</div><!-- end .body -->
</div><!-- end .blogs -->
{/strip}
