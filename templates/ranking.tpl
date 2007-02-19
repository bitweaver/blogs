{* THIS FILE CAN BE  BE REMOVED AS SOON AS BLOGS ARE PART OF RANKINGS *}
{strip}
<div class="ranking">
	<div class="header">
		<h1>{tr}Rankings{/tr}</h1>
	</div>

	<div class="body">
		{form legend="Ranking Settings"}
			<div class="row">
				{formlabel label="Select Attribute" for="which"}
				{forminput}
					<select name="which" id="which">
						{section name=ix loop=$allrankings}
							<option value="{$allrankings[ix].value|escape}" {if $which eq $allrankings[ix].value}selected="selected"{/if}>{$allrankings[ix].name}</option>
						{/section}
					</select>
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Number of items" for="limit"}
				{forminput}
					<select name="limit" id="limit">
						<option value="10" {if $smarty.request.limit eq 10}selected="selected"{/if}>{tr}Top 10{/tr}</option>
						<option value="20" {if $smarty.request.limit eq 20}selected="selected"{/if}>{tr}Top 20{/tr}</option>
						<option value="50" {if $smarty.request.limit eq 50}selected="selected"{/if}>{tr}Top 50{/tr}</option>
						<option value="100" {if $smarty.request.limit eq 100}selected="selected"{/if}>{tr}Top 100{/tr}</option>
					</select>
				{/forminput}
			</div>

			<div class="row submit">
				<input type="submit" name="selrank" value="{tr}Apply settings{/tr}" />
			</div>
		{/form}
		
		<h1>{$rankings.title}</h2>
		
		{foreach from=$rankings.data item=blog}
			<ul>
				<li class="{cycle value="even,odd"}"><h2><a title="{$blog.title}" href="{$blog.blog_url}">{$blog.title}</a> (Hits: {$blog.hits|default:0}) (Posts: {$blog.posts|default:0})</h2>
				{if $blog.post_array}
					<ul>
						{foreach from=$blog.post_array item=post}
							<li class="{cycle value="even,odd"}"><a title="{$post.title}" href="{$post.display_url}">{$post.title}</a> [{$post.created|bit_short_date}]</li>
						{/foreach}
					</ul>
				{/if
				</li>}
			</ul>
		{foreachelse}
			{tr}No Blogs Found.{/tr}
		{/foreach}
	</div><!-- end .body -->
</div><!-- end .ranking -->
{/strip}
