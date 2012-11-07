{include file="user/header.tpl"}
<div class="wl-borderbox">

    <form class="z-form wl-form" action="{modurl modname="Weblinks" type="user" func="brokenlinks"}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name="csrftoken"}" />
            <input type="hidden" name="lid" value="{$lid}" />
            <input type="hidden" name="submitter" value="{$submitter}" />

            <fieldset class="z-form-fieldset">
                <legend>{gt text="Report broken link"}</legend>
                <p>
                    {gt text="Thank you for helping maintain this directory's integrity."}<br />
                    {gt text="For security reasons, your user name and IP address will also be recorded temporarily."}
                </p>
            </fieldset>

            <div class="z-formrow z-formbuttons">
                {button src=button_ok.gif set=icons/small __alt="Report broken link" __title="Report broken link"}
                <a href="{modurl modname=Weblinks type=user func=view}">{img modname=core src=button_cancel.gif set=icons/small __alt="Back" __title="Back"}</a>
            </div>
        </div>
    </form>

</div>
{include file="user/footer.tpl"}