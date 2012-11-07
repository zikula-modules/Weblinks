{*  $Id: weblinks_block_lastweblinks.html 166 2010-10-19 14:39:19Z herr.vorragend $  *}
{if $weblinks}
<ol class="lastweblinks">
    {foreach from=$weblinks item=weblinks name=loop}
    {* $smarty.foreach.loop.iteration *}
    <li>
        <a href="{modurl modname=Weblinks type=user func=visit lid=$weblinks.lid}"{if $tb eq 1} target="_blank"{/if}>{$weblinks.title|safetext}</a>
    </li>
    {/foreach}
</ol>
{/if}