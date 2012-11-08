{include file="admin/header.tpl"}
{modgetinfo modname=Weblinks info=all assign=info}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname=core src=configure.gif set=icons/large __alt="Import" __title="Import"}</div>
    <h2>{gt text="Import"}</h2>
    <!--
        <form class="z-form" action="{modurl modname='Weblinks' type='admin' func='importratings'}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <fieldset>
                <legend>{gt text="Ratings"}</legend>
                {if $ratings eq 1}
                <div class="z-informationmsg">
                    {gt text="With this function <strong>Weblinks-Votes</strong> will be imported to the <strong>Rating</strong> module.<br /><strong>!Attention!</strong> - befor you use this function you should set the style of the Ratings."}
                </div>
                <input type="hidden" name="csrftoken" value="{insert name="csrftoken"}" />
                <div class="z-formbuttons">
                    {button src=button_ok.gif set=icons/small __alt="OK" __title="OK"}
                </div>
                {else}
                <div class="z-informationmsg">
                    {gt text="Rating-Hook isn't activated for the Weblinks module!"} -&gt; <a href="{modurl modname=Module type=admin func=hooks id=$info.id}">{gt text="to the hooks of Weblinks module"}</a>
                </div>
                {/if}
            </fieldset>
        </form>
        <form class="z-form" action="{modurl modname='Weblinks' type='admin' func='importezcomments'}" method="post" enctype="application/x-www-form-urlencoded">
            <fieldset>
                <legend>{gt text="EZComments"}</legend>
                {if $ezcomments eq 1}
                <div class="z-informationmsg">
                    {gt text="With this function <strong>Weblinks-Comments</strong> will be imported to the <strong>EZComments</strong> module."}
                </div>
                <input type="hidden" name="csrftoken" value="{insert name="csrftoken"}" />
                <div class="z-formbuttons">
                    {button src=button_ok.gif set=icons/small alt="OK" title="OK"}
                </div>
                {else}
                <div class="z-informationmsg">
                    {gt text="EZComments-Hook isn't activated for the Weblinks module!"} -&gt; <a href="{modurl modname=Module type=admin func=hooks id=$info.id}">{gt text="to the hooks of Weblinks module"}</a>
                </div>
                {/if}
            </fieldset>
        </form>
    -->
        <form class="z-form" action="{modurl modname='Weblinks' type='admin' func='importcmodsweblinks'}" method="post" enctype="application/x-www-form-urlencoded">
            <fieldset>
                <legend>{gt text="CmodsWebLinks"}</legend>
                {if $cmodsweblinks eq 1}
                <div class="z-informationmsg">
                    {gt text="With this function <strong>CmodsWebLinks</strong> will be imported to the <strong>Weblinks</strong> module.<br /><strong>!Attention!</strong> - this function add the CmodsWebLinks categories and links to the Weblinks module and don't replace it."}
                </div>
                <input type="hidden" name="csrftoken" value="{insert name="csrftoken"}" />
                <div class="z-formbuttons">
                    {button src=button_ok.gif set=icons/small alt="OK" title="OK"}
                </div>
                {else}
                <div class="z-informationmsg">
                    {gt text="CmodsWebLinks isn't activated! Try to import?<br /><strong>!Attention!</strong> - this function add the CmodsWebLinks categories and links to the Weblinks module and don't replace it."}
                </div>
                <input type="hidden" name="csrftoken" value="{insert name="csrftoken"}" />
                <div class="z-formbuttons">
                    {button src=button_ok.gif set=icons/small alt="OK" title="OK"}
                </div>
                {/if}
            </fieldset>
        </div>
    </form>
</div>
{include file="admin/footer.tpl"}