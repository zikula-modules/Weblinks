{include file="admin/header.tpl"}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname=core src=info.gif set=icons/large __alt="Overview" __title="Overview"}</div>
    <h2>{gt text="Overview"}</h2>

    <h3>{gt text="Status"}</h3>
    <p>{gt text="There are"} <strong>{$numrows}</strong> {gt text="link" plural="links" count=$numrows} {gt text="and"} <strong>{$catnum}</strong> {gt text="category" plural="categories" count=$catnum} {gt text="in the database"}</p>

    {if $totalbrokenlinks gt 0 || $totalmodrequests gt 0}
    <div class="z-informationmsg">
        <a href="{modurl modname=Weblinks type=admin func=listbrokenlinks}">{gt text="Broken link reports"} ({$totalbrokenlinks|safetext})</a><br />
        <a href="{modurl modname=Weblinks type=admin func=listmodrequests}">{gt text="Link modification requests"} ({$totalmodrequests|safetext})</a>
    </div>
    {/if}

    <form class="z-form" action="{modurl modname=Weblinks type=admin func=validate}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name="csrftoken"}" />
            <fieldset>
                <legend>{gt text="Link validation"}</legend>
                <div class="z-formrow">
                    <label for="vat_cid">{gt text="Check category"}</label>
                    <select id="vat_cid" name="cid"><option value="0">{gt text="Check ALL categories"}</option>{catlist scat=0 sel=0}</select>
                </div>
                <div class="z-formbuttons">
                    {button src=button_ok.gif set=icons/small __alt="Check category" __title="Check category"}
                </div>
            </fieldset>
        </div>
    </form>

    {if $newlinks}

    <h3>{gt text="Links awaiting validation"}</h3>

    {foreach from=$newlinks item=newlinks}
    <form class="z-form" action="{modurl modname=Weblinks type=admin func=addlink}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <fieldset>
                <input type="hidden" name="csrftoken" value="{insert name="csrftoken"}" />
                <input type="hidden" name="link[new]" value="1" />
                <input type="hidden" name="link[lid]" value="{$newlinks.lid|safetext}" />
                <input type="hidden" name="link[submitter]" value="{$newlinks.submitter|safetext}" />
                <legend>{gt text="Link ID"}: <strong>{$newlinks.lid}</strong></legend>
                <div class="z-formrow">
                    <label>{gt text="Submitter"}</label>
                    <span>{$newlinks.submitter|safetext}</span>
                </div>
                <div class="z-formrow">
                    <label for="addlink_title">{gt text="Page title"}</label>
                    <input id="addlink_title" type="text" name="link[title]" value="{$newlinks.title|safetext}" size="50" maxlength="100" />
                </div>
                <div class="z-formrow">
                    <label for="addlink_url">{gt text="URL"} [ <a target="_blank" href="{$newlinks.url|safetext}">{gt text="Visit"}</a> ]</label>
                    <input id="addlink_url" type="text" name="link[url]" value="{$newlinks.url|safetext}" size="65" maxlength="254" />
                </div>
                <div class="z-formrow">
                    <label for="addlink_cat">{gt text="Category"}</label>
                    <select id="addlink_cat" name="link[cat]">{catlist scat=0 sel=$newlinks.cat_id}</select>
                </div>
                <div class="z-formrow">
                    <label for="addlink_description">{gt text="Description"}</label>
                    <textarea id="addlink_description" name="link[description]" cols="65" rows="10">{$newlinks.description|safehtml}</textarea>
                </div>
                <div class="z-formrow">
                    <label for="addlink_name">{gt text="Name"}</label>
                    <input id="addlink_name" type="text" name="link[name]" size="20" maxlength="100" value="{$newlinks.name|safetext}" />
                </div>
                <div class="z-formrow">
                    <label for="addlink_email">{gt text="E-mail address"}</label>
                    <input id="addlink_email" type="text" name="link[email]" size="20" maxlength="100" value="{$newlinks.email|safetext}" />
                </div>
                <div class="z-formbuttons">
                    {button src=button_ok.gif set=icons/small __alt="Add link" __title="Add link"}
                    <a href="{modurl modname=Weblinks type=admin func=delnewlink lid=$newlinks.lid authid=$authid}">{img modname=core src=editdelete.gif set=icons/small __alt="Delete link" __title="Delete link"}</a>
                </div>
            </fieldset>
        </div>
    </form>
    {/foreach}

    {/if}
</div>
{include file="admin/footer.tpl"}