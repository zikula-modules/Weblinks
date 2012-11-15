{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="info" size="small"}
    <h3>{gt text='User-reported broken links'} ({$totalbrokenlinks|safetext})</h3>
</div>

<div class="z-informationmsg">
    {gt text="Instructions: confirm the validity of the link by clicking on the link in the left column. Contact the link submitter/owner by clicking their name. Choose which action to take:"}<br />
    <ul>
        <li>{gt text="Ignore: converts link to normal, active status."}</li>
        <li>{gt text="Delete: removes link from database completely."}</li>
    </ul>
</div>

{if $totalbrokenlinks == 0}
<div class="z-warningmsg">
    {gt text="No broken links found"}
</div>
{else}

<table class="z-admintable">
    <thead>
        <tr>
            <th>{gt text="Link"}</th>
            <th>{gt text="Submitter"}</th>
            <th>{gt text="Link owner"}</th>
            <th>{gt text="Ignore"}</th>
            <th>{gt text="Delete"}</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$brokenlinks item=brokenlinks}
        <tr class="{cycle values="z-odd,z-even" name=abacs}">

            <td><a href="{$brokenlinks.url|safetext}">{$brokenlinks.title|safetext}</a></td>
            {usergetidfromname uname=$brokenlinks.modifysubmitter assign='modifysubmitterid'}
            {usergetvar id=$modifysubmitterid name='email' assign='submitteremail'}
            {if $submitteremail == ""}
            <td>{$brokenlinks.modifysubmitter|safetext}</td>
            {else}
            <td><a href="mailto:{$submitteremail}">{$brokenlinks.modifysubmitter|safetext}</a></td>
            {/if}

            {if $brokenlinks.email == ""}
            <td>{$brokenlinks.name|safetext}</td>
            {else}
            <td><a href="mailto:{$brokenlinks.email}">{$brokenlinks.name|safetext}</a></td>
            {/if}

            <td>
                <a href="{modurl modname='Weblinks' type='admin' func='ignorebrokenlinks' lid=$brokenlinks.lid}">
                    {img modname='core' src='button_ok.png' set='icons/extrasmall' __alt="Ignore" __title="Ignore"}
                </a>
            </td>
            <td>
                <a href="{modurl modname='Weblinks' type='admin' func='dellink' lid=$brokenlinks.lid}">
                    {img modname='core' src='14_layer_deletelayer.png' set='icons/extrasmall' __alt="Delete" __title="Delete"}
                </a>
            </td>

        </tr>
        {/foreach}
    </tbody>
</table>

{/if}