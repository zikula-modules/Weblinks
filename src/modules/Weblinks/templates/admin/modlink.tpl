{include file="admin/header.tpl"}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname=core src=agt_internet.gif set=icons/large __alt="Links administer" __title="Links administer"}</div>
    <h2>{gt text="Links administer"}</h2>

    <form class="z-form" action="{modurl modname=Weblinks type=admin func=modlinks}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <fieldset>
                <legend>{gt text="Modify/Delete a link"} - {gt text="Link ID"}: <strong>{$link.lid|safetext}</strong></legend>
                <input type="hidden" name="authid" value="{insert name="generateauthkey" module="Weblinks"}" />
                <input type="hidden" name="link[lid]" value="{$link.lid|safetext}" />
                <div class="z-formrow">
                    <label for="modlinks_title">{gt text="Page title"}</label>
                    <input id="modlinks_title" type="text" name="link[title]" value="{$link.title|safetext}" size="50" maxlength="100" />
                </div>
                <div class="z-formrow">
                    <label for="modlinks_url">{gt text="URL"} [ <a href="{$link.url}">{gt text="Visit"}</a> ]</label>
                    <input id="modlinks_url" type="text" name="link[url]" value="{$link.url|safetext}" size="65" maxlength="254" />
                </div>
                <div class="z-formrow">
                    <label for="modlinks_description">{gt text="Description:"}</label>
                    <textarea id="modlinks_description" name="link[description]" cols="65" rows="10">{$link.description|safehtml}</textarea>
                </div>
                <div class="z-formrow">
                    <label for="modlinks_name">{gt text="Name"}</label>
                    <input id="modlinks_name" type="text" name="link[name]" size="50" maxlength="100" value="{$link.name|safetext}" />
                </div>
                <div class="z-formrow">
                    <label for="modlinks_email">{gt text="E-mail adress"}</label>
                    <input id="modlinks_email" type="text" name="link[email]" size="50" maxlength="100" value="{$link.email|safetext}" />
                </div>
                <div class="z-formrow">
                    <label for="modlinks_hits">{gt text="Hits"}</label>
                    <input id="modlinks_hits" type="text" name="link[hits]" value="{$link.hits|safetext}" size="12" maxlength="11" />
                </div>
                <div class="z-formrow">
                    <label for="modlinks_cat">{gt text="Category"}</label>
                    <select id="modlinks_cat" name="link[cat]">{catlist scat=0 sel=$link.cat_id}</select>
                </div>
            </fieldset>

            <div class="z-formbuttons">
                {button src=button_ok.gif set=icons/small __alt="Modify link" __title="Modify link"}
                <a href="{modurl modname=Weblinks type=admin func=dellink lid=$link.lid authid=$authid}">{img modname=core src=editdelete.gif set=icons/small __alt="Delete link" __title="Delete link"}</a>
            </div>
        </div>
    </form>

</div>
{include file="admin/footer.tpl"}