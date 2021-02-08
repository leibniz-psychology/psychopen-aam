{include file="frontend/components/header.tpl" pageTitleTranslated=$issueIdentification}

<div class="page page_aam">
	{include file="frontend/components/breadcrumbs.tpl" currentTitleKey="plugins.generic.aam.breadcrumb"}
	<h1>{translate key="plugins.generic.aam.headline"}</h1>
	{if empty($aamItems)}
		{include file="frontend/components/notification.tpl" type="warning" messageKey="plugins.generic.aam.help.no.content"}
	{else}
		<div class="cmp_notification info">
			{translate key="plugins.generic.aam.help.content" journalName=$displayPageHeaderTitle|escape}
		</div>
		<ul class="cmp_article_list articles obj_article_summary">
			{foreach from=$aamItems item=aamItem}
				<li>
					<div class="obj_article_summary">
						<div class="title">
							{$aamItem['title']|escape}
						</div>
						<div class="meta">
							<div class="authors">
								{$aamItem['authors']|escape}
							</div>
						</div>
					</div>
					{if isset($aamItem['preliminaryDOI']) && !empty($aamItem['preliminaryDOI'])}
					<div class="galleys_links pa-link-query" data-url="{url page="aam" op="getPsychArchivesLink"}"
					     data-doi="{$aamItem['preliminaryDOI']}">
						<div class="pa-link-query-result" style="display: none">
							<a class="obj_galley_link" href="" target="_blank" rel="noreferrer">
								{translate key="plugins.generic.aam.link.pa"}
							</a>
							<div/>
							<div class="pa-link-query-no-result" style="display: none">

							</div>
						</div>
						{/if}
					</div>
				</li>
			{/foreach}
		</ul>
	{/if}
	{*{url page="aam"}*}
</div>
{include file="frontend/components/footer.tpl"}
