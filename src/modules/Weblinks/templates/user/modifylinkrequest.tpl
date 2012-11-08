{include file="user/header.tpl"}
<div class="wl-borderbox">
    {if $blockunregmodify == 1 && $ratinguser == $anonymous}
    <div class="z-errormsg">{gt text="Sorry! Only registered users can suggest link modifications."}</div>
    {else}
    <form class="z-form" action="{modurl modname='Weblinks' type='user' func='modifylinkrequests'}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name="csrftoken"}" />
            <input type="hidden" name="modlink[lid]" value="{$link.lid|safetext}" />
            <input type="hidden" name="modlink[submitter]" value="{$submitter|safetext}" />

            <fieldset>
                <legend>{gt text="Request link modification"} - <em>{gt text="Link ID"}: {$link.lid|safetext}</em></legend>

                <div class="z-formrow">
                    <label for="linkrequest_title">{gt text="Link title"}:</label>
                    <input id="linkrequest_title" type="text" name="modlink[title]" value="{$link.title|safetext}" size="50" maxlength="100" />
                </div>
                <div class="z-formrow">
                    <label for="linkrequest_url">{gt text="URL"}:</label>
                    <input id="linkrequest_url" type="text" name="modlink[url]" value="{$link.url|safetext}" size="75" maxlength="254" />
                </div>
                <div class="z-formrow">
                    <label for="linkrequest_cat">{gt text="Category"}:</label>
                    <select id="linkrequest_cat" name="modlink[cid]">{catlist scat=0 sel=$link.cat_id}</select>
                </div>
                <div class="z-formrow">
                    <label for="linkrequest_description">{gt text="Description (255 characters max)"}:</label>
                    <textarea id="linkrequest_description" name="modlink[description]" cols="65" rows="10">{$link.description|safehtml}</textarea>
                </div>

            </fieldset>
            <div class="z-formbuttons">
                {button src=button_ok.gif set=icons/small __alt="Send request" __title="Send request"}
                <a href="{modurl modname='Weblinks' type='user' func='view'}">{img modname=core src=button_cancel.gif set=icons/small __alt="Back" __title="Back"}</a>
            </div>

        </div>
    </form>
    {/if}
</div>
{include file="user/footer.tpl"}