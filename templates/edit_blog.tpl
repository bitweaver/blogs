{* $Header: /cvsroot/bitweaver/_bit_blogs/templates/edit_blog.tpl,v 1.12 2006/11/18 15:41:22 spiderr Exp $ *}
{strip}

<div class="floaticon">{bithelp}</div>

<div class="edit blogs">
	<div class="header">
		<h1>{if $gBlog->isValid()}{tr}Edit Blog{/tr}{else}{tr}Create Blog{/tr}{/if}</h1>
	</div>

	<div class="body">
		{if $smarty.request.preview}
			<div class="preview">
				<div class="header">
					<h1>{$gBlog->getTitle()}</h1>
					{if $description}<h2>{$gBlog->getField('description')}</h2>{/if}
					<div class="date">
						{tr}Created by{/tr}: {displayname hash=$blog_data}, {$gBlog->getField('created')|bit_short_datetime}<br />
						{tr}Last modified{/tr}: {$gBlog->getField('last_modified')|bit_short_datetime}
					</div>
				</div>
			</div>
		{/if}

		{if $individual eq 'y'}
			{formfeedback warning="<a href='`$smarty.const.KERNEL_PKG_URL`object_permissions.php?objectName=blog%20`$title`&amp;object_type=blog&amp;permType=blogs&amp;object_id=`$blog_id`'>There are individual permissions set for this blog</a>" position="top"}
		{/if}

		{form ipackage="blogs" ifile="edit.php"}
			{legend legend="Blog Settings"}
				<input type="hidden" name="blog_id" value="{$gBlog->getField('blog_id')}" />
				<div class="row">
					{formfeedback warning=$warning}
					{formlabel label="Title" for="title"}
					{forminput}
						<input type="text" name="title" id="title" value="{if $gBlog->getTitle()}{$gBlog->getTitle()|escape}{else}{displayname hash=$gBitUser->mInfo nolink=FALSE}'s Blog{/if}" />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Description" for="description"}
					{forminput}
						<textarea name="description" id="description" rows="2" cols="50">{$gBlog->getField('description')|escape}</textarea>
						{formhelp note=''}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Number of posts to show" for="max_posts"}
					{forminput}
						<input type="text" name="max_posts" id="max_posts" value="{$gBlog->getField('max_posts')|escape|default:10}" />
						{formhelp note='Enter the number of blog posts you wish to display when viewing this blog.'}
					{/forminput}
				</div>

				{if $gBitUser->hasPermission('p_blogs_create_is_public')}
				<div class="row">
					{formlabel label="Public" for="is_public"}
					{forminput}
						<input type="checkbox" name="is_public" id="is_public" {if $is_public eq 'y'}checked="checked"{/if} />
						{formhelp note='Allow other user to post in this blog'}
					{/forminput}
				</div>
				{/if}

				<div class="row">
					{formlabel label="Use titles in blog posts" for="use_title"}
					{forminput}
						<input type="checkbox" name="use_title" id="use_title" {if !$use_title || $use_title eq 'y'}checked="checked"{/if} />
						{formhelp note='If this is not seelcted, the time and date of when the post was created will be displayed instead of the post title.'}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Allow search" for="use_find"}
					{forminput}
						<input type="checkbox" name="use_find" id="use_find" {if !$use_find || $use_find eq 'y'}checked="checked"{/if} />
						{formhelp note='Allow userers to search this blog for occurances of words.'}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Allow comments" for="allow_comments"}
					{forminput}
						<input type="checkbox" name="allow_comments" id="allow_comments" {if !$allow_comments || $allow_comments eq 'y'}checked="checked"{/if} />
						{formhelp note='Are other users allowed to add comments to posts made in this blog?'}
					{/forminput}
				</div>

				<div class="row submit">
					<input type="submit" name="preview" value="{tr}preview{/tr}" />&nbsp;
					<input type="submit" name="save_blog" value="{tr}save{/tr}" />
				</div>
			{/legend}
		{/form}

	</div><!-- end .body -->
</div><!-- end .blog -->

{/strip}
