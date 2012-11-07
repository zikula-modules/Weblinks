{*  $Id: weblinks_admin_suredelcategory.html 165 2010-10-19 13:28:15Z herr.vorragend $  *}
{include file="weblinks_admin_header.html"}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname=core src=folder.gif set=icons/large __alt="Delete Category" __title="Delete Category"}</div>

    <form class="z-form" action="{modurl modname=Weblinks type=admin func=delcategory}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <fieldset>
                <legend>{gt text="Delete Category"}</legend>
                <input type="hidden" name="authid" value="{insert name="generateauthkey" module="Weblinks"}" />
                <input type="hidden" name="cid" value="{$cid}" />

                <div class="z-formrow">
                    <p style="font-weight:bold">{gt text="Confirmation prompt: are you sure you want to delete this category and ALL its links?"}</p>
                </div>
            </fieldset>

            <div class="z-formbuttons">
                {button src=button_ok.gif set=icons/small __alt="Delete Category" __title="Delete Category"}
                <a href="{modurl modname=Weblinks type=admin func=catview}">{img modname=core src=button_cancel.gif set=icons/small __alt="Back" __title="Back"}</a>
            </div>

        </div>
    </form>

</div>
{include file="weblinks_admin_footer.html"}