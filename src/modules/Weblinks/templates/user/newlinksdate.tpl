{*  $Id: weblinks_user_newlinksdate.html 166 2010-10-19 14:39:19Z herr.vorragend $  *}
{include file="weblinks_user_header.html"}
<div class="wl-borderbox">

    <h3>{$dateview|safetext} - {$totallinks|safetext} {gt text="new link" plural="new links" count=$totallinks}</h3>

    {foreach from=$weblinks item=weblinks}
    <div class="wl-linkbox">
        {include file="weblinks_user_linkbox.html"}
    </div>
    {/foreach}

</div>
{include file="weblinks_user_footer.html"}