{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/blog_post.tpl,v 1.15 2006/11/18 21:43:16 spiderr Exp $ *}
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
					{legend legend="Post to a Blog"}
						<div class="row">
							{formlabel label="Blog" for="blog_id"}
							{forminput}
								<select name="blog_id" id="blog_id">
									{section name=ix loop=$blogs}
										<option value="{$blogs[ix].blog_id|escape}" {if $blogs[ix].blog_id eq $selectedBlog}selected="selected"{/if}>{$blogs[ix].title|escape}</option>
									{/section}
								</select>
							{/forminput}
						</div>

						{if !$blog_data.use_title OR $blog_data.use_title eq 'y'}
							<div class="row">
								{formlabel label="Title" for="title"}
								{forminput}
									<input type="text" size="50" name="title" id="title" value="{$title|escape}" />
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
								<textarea {spellchecker} id="{$textarea_id}" name="edit" rows="{$smarty.cookies.rows|default:20}" cols="50">{$data|escape:html}</textarea>
							{/forminput}
						</div>

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
					{legend legend="Advanced Options"}
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
