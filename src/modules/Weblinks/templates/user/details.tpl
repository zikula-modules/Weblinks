{include file="user/header.tpl"}
{pagesetvar name=title value=$weblinks.title}
<div class="wl-borderbox">
    <div class="wl-linkbox">
        {include file="user/linkbox.tpl"}
        <p>&nbsp;</p>
        {* modcallhooks hookobject=item hookaction=display hookid=$weblinks.lid module=Weblinks implode=false *}
        {* $hooks.EZComments *}
    </div>
</div>
{include file="user/footer.tpl"}