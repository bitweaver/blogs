{strip}
{form}
	{jstabs}
		{jstab title="Home Blog"}
			{legend legend="Home Blog"}
				<input type="hidden" name="page" value="{$page}" />
				<div class="row">
					{formlabel label="Home Blog (main blog)" for="blog_home"}
					{forminput}
						<select name="blog_home" id="blog_home">
							{if $blogList}
								<option>{tr}Use default recent blogpost page{/tr}</option>
							{/if}
							{foreach from=$blogList item=blog}
								<option value="{$blog.blog_id}" {if $blog.blog_id == $gBitSystem->getConfig('blog_home')}selected="selected"{/if}>{$blog.title|escape html|truncate:30:"...":true}</option>
							{foreachelse}
								<option>{tr}No records found{/tr}</option>
							{/foreach}
						</select>
					{/forminput}
				</div>

				<div class="row submit">
					<input type="submit" name="set_blog_home" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="Blog Features"}
			{legend legend="Blog Features"}
				<input type="hidden" name="page" value="{$page}" />

				{foreach from=$formBlogFeatures key=item item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$item}
						{forminput}
							{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}

				{foreach from=$formBlogInputs key=item item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$item}
						{forminput}
							<input type="text" name="$item" value="{$gBitSystem->getConfig($item, 3)}" id={$item} />
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}

				<div class="row">
					{formlabel label="Blog Posts Description Length" for="blog-posts-descrlength"}
					{forminput}
						<input size="5" type="text" name="blog_posts_description_length" id="blog-posts-descrlength" value="{$gBitSystem->getConfig('blog_posts_description_length')|escape}" /> {tr}characters{/tr}
						{formhelp note="Number of characters displayed on the blog posts main page before splitting the blog post into a heading and body.<br />Changing this value might influence existing blog posts."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Default ordering for blog listing" for="blog_list_order"}
					{forminput}
						<select name="blog_list_order" id="blog_list_order">
							<option value="created_desc"       {if $gBitSystem->getConfig('blog_list_order') eq 'created_desc'}selected="selected"{/if}>{tr}Creation date{/tr} ({tr}desc{/tr})</option>
							<option value="last_modified_desc" {if $gBitSystem->getConfig('blog_list_order') eq 'last_modified_desc'}selected="selected"{/if}>{tr}Last modification date{/tr} ({tr}desc{/tr})</option>
							<option value="title_asc"          {if $gBitSystem->getConfig('blog_list_order') eq 'title_asc'}selected="selected"{/if}>{tr}Blog title{/tr} ({tr}asc{/tr})</option>
							<option value="posts_desc"         {if $gBitSystem->getConfig('blog_list_order') eq 'posts_desc'}selected="selected"{/if}>{tr}Number of posts{/tr} ({tr}desc{/tr})</option>
							<option value="hits_desc"          {if $gBitSystem->getConfig('blog_list_order') eq 'hits_desc'}selected="selected"{/if}>{tr}Visits{/tr} ({tr}desc{/tr})</option>
							<option value="activity_desc"      {if $gBitSystem->getConfig('blog_list_order') eq 'activity_desc'}selected="selected"{/if}>{tr}Activity{/tr} ({tr}desc{/tr})</option>
						</select>
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Display user as" for="blog_list_user_as"}
					{forminput}
						<select name="blog_list_user_as" id="blog_list_user_as">
							<option value="text"   {if $gBitSystem->getConfig('blog_list_user_as') eq 'text'}selected="selected"{/if}>{tr}Plain text{/tr}</option>
							<option value="link"   {if $gBitSystem->getConfig('blog_list_user_as') eq 'link'}selected="selected"{/if}>{tr}Link to user information{/tr}</option>
							<option value="avatar" {if $gBitSystem->getConfig('blog_list_user_as') eq 'avatar'}selected="selected"{/if}>{tr}User avatar{/tr}</option>
						</select>
						{formhelp note="Decide how blog post author information is displayed."}
					{/forminput}
				</div>

				<div class="row submit">
					<input type="submit" name="featuresTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="List Settings"}
			{legend legend="List Settings"}
				<input type="hidden" name="page" value="{$page}" />

				{foreach from=$formBlogLists key=item item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$item}
						{forminput}
							{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}

				<div class="row submit">
					<input type="submit" name="listTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}
	{/jstabs}
{/form}

{/strip}
