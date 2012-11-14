{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="info" size="small"}
    <h3>{gt text='User link modification requests'} ({$totalmodrequests|safetext})</h3>
</div>

{foreach from=$modrequests item=modrequest}
<table class="z-admintable">
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th>{gt text="Original"}</th>
            <th>{gt text="Proposed"}</th>
        </tr>
    </thead>
    <tbody>
        <tr class="{cycle values="z-odd,z-even" name=abacs}">
            <td>{gt text="Link title"}</td>
            <td>{$modrequest.origtitle|safetext}</td>
            <td{if $modrequest.origtitle <> $modrequest.title} class='wl-red'{/if}>{$modrequest.title|safetext}</td>
        </tr>
        <tr class="{cycle values="z-odd,z-even" name=abacs}">
            <td>{gt text="URL"}</td>
            <td><a href="{$modrequest.origurl|safetext}">{$modrequest.origurl|safetext}</a></td>
            <td{if $modrequest.origurl <> $modrequest.url} class='wl-red'{/if}><a href="{$modrequest.url|safetext}">{$modrequest.url|safetext}</a></td>
        </tr>
        <tr class="{cycle values="z-odd,z-even" name=abacs}">
            <td>{gt text="Category"}</td>
            <td>{$modrequest.origcidtitle|safetext}</td>
            <td{if $modrequest.origcidtitle <> $modrequest.cidtitle} class='wl-red'{/if}>{$modrequest.cidtitle|safetext}</td>
        </tr>
        <tr class="{cycle values="z-odd,z-even" name=abacs}">
            <td>{gt text="Description"}:</td>
            <td>{$modrequest.origdescription|safetext}</td>
            <td{if $modrequest.origdescription <> $modrequest.description} class='wl-red'{/if}>{$modrequest.description|safetext}</td>
        </tr>

        <tr style="border-top: 1px solid #999;">
            <td colspan="3">
                {if $modrequest.submitteremail == ""}
                {gt text="Submitter"}: {$modrequest.submitter|safetext}
                {else}
                {gt text="Submitter"}: <a href="mailto:{$modrequest.submitteremail|safetext}">{$modrequest.submitter|safetext}</a>
                {/if}
            </td>
        </tr>
        <tr>
            <td colspan="3">
                {if $modrequest.owneremail == ""}
                {gt text="Owner"}: {$modrequest.owner|safetext}
                {else}
                {gt text="Owner"}: <a href="mailto:{$modrequest.owneremail|safetext}">{$modrequest.owner|safetext}</a>
                {/if}
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div class="z-formbuttons">
                    <a href="{modurl modname='Weblinks' type='admin' func='changemodrequests' lid=$modrequest.lid}">{img modname='core' src='button_ok.png' set='icons/small' __alt="Accept" __title="Accept"}</a>
                    <a href="{modurl modname='Weblinks' type='admin' func='delmodrequests' lid=$modrequest.lid}">{img modname='core' src=editdelete.png set='icons/small' __alt="Ignore" __title="Ignore"}</a>
                </div>
            </td>
        </tr>
    </tbody>
</table>

{/foreach}

{if $totalmodrequests == 0}
<p class="wl-center">{gt text="No link modification requests"}</p>
{/if}
