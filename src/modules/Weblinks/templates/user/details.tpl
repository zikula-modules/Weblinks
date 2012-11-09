{include file="user/header.tpl"}
{pagesetvar name='title' value=$weblinks.title}
<div class="wl-borderbox">
    <div class="wl-linkbox">
        {include file="user/linkbox.tpl"}
        <p>&nbsp;</p>
        {notifydisplayhooks eventname='weblinks.ui_hooks.link.ui_view' id=$weblinks.lid}
    </div>
</div>
{include file="user/footer.tpl"}