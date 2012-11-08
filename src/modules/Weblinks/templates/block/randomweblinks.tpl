{if $links}
<ul class="randomweblinks">
    {foreach from=$links item=links name=loop}
    {* $smarty.foreach.loop.iteration *}
    <li>
        <a href="{modurl modname='Weblinks' type='user' func='visit' lid=$links.lid}"{if $tb eq 1} target="_blank"{/if}>{$links.title|safetext}</a>
    </li>
    {/foreach}
</ul>
{/if}