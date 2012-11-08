{include file="user/header.tpl"}
<div class="z-statusmsg">
    {if $submit eq 0}
    <strong>{gt text="$text"}</strong><br />[ <a href="{modurl modname='Weblinks' type='user' func='addlink'}">{gt text="Back"}</a> ]
    {else}
    <strong>{gt text="Thank you! Your link submission has been received."}</strong><br />{gt text="$text"}
    {/if}
</div>
{include file="user/footer.tpl"}