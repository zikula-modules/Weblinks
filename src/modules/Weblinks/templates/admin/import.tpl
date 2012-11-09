{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="import" size="small"}
    <h3>{gt text='Import'}</h3>
</div>

{if $ratings eq 1}
<form class="z-form" action="{modurl modname='Weblinks' type='admin' func='importratings'}" method="post" enctype="application/x-www-form-urlencoded">
<div>
    <fieldset>
        <legend>{gt text="Ratings"}</legend>
        <div class="z-informationmsg">
            {gt text="With this function <strong>Weblinks-Votes</strong> will be imported to the <strong>Rating</strong> module.<br /><strong>!Attention!</strong> - befor you use this function you should set the style of the Ratings."}
        </div>
        <input type="hidden" name="csrftoken" value="{insert name="csrftoken"}" />
        <div class="z-formbuttons">
            {button src='button_ok.png' set='icons/small' __alt="OK" __title="OK"}
        </div>
    </fieldset>
</div>
</form>
{/if}
{if $ezcomments eq 1}
<form class="z-form" action="{modurl modname='Weblinks' type='admin' func='importezcomments'}" method="post" enctype="application/x-www-form-urlencoded">
<div>
    <fieldset>
        <legend>{gt text="EZComments"}</legend>
        <div class="z-informationmsg">
            {gt text="With this function <strong>Weblinks-Comments</strong> will be imported to the <strong>EZComments</strong> module."}
        </div>
        <input type="hidden" name="csrftoken" value="{insert name="csrftoken"}" />
        <div class="z-formbuttons">
            {button src='button_ok.png' set='icons/small' alt="OK" title="OK"}
        </div>
    </fieldset>
</div>
</form>
{/if}
{if $cmodsweblinks eq 1}
<form class="z-form" action="{modurl modname='Weblinks' type='admin' func='importcmodsweblinks'}" method="post" enctype="application/x-www-form-urlencoded">
<div>
    <fieldset>
        <legend>{gt text="CmodsWebLinks"}</legend>
        <div class="z-informationmsg">
            {gt text="With this function <strong>CmodsWebLinks</strong> will be imported to the <strong>Weblinks</strong> module.<br /><strong>!Attention!</strong> - this function add the CmodsWebLinks categories and links to the Weblinks module and don't replace it."}
        </div>
        <input type="hidden" name="csrftoken" value="{insert name="csrftoken"}" />
        <div class="z-formbuttons">
            {button src='button_ok.png' set='icons/small' alt="OK" title="OK"}
        </div>
        {else}
        <div class="z-informationmsg">
            {gt text="CmodsWebLinks isn't activated! Try to import?<br /><strong>!Attention!</strong> - this function add the CmodsWebLinks categories and links to the Weblinks module and don't replace it."}
        </div>
        <input type="hidden" name="csrftoken" value="{insert name="csrftoken"}" />
        <div class="z-formbuttons">
            {button src='button_ok.png' set='icons/small' alt="OK" title="OK"}
        </div>
    </fieldset>
</div>
</form>
{/if}