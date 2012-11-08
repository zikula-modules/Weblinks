{include file="admin/header.tpl"}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname='core' src='info.png' set='icons/large' __alt="Overview" __title="Overview"}</div>
    <h2>{gt text="User-reported broken links"} ({$totalbrokenlinks|safetext})</h2>

    <div class="z-informationmsg">
        {gt text="Ignore (deletes all <strong><em>requests</em></strong> for a given link)"}<br />
        {gt text="Delete (deletes <strong><em>broken link</em></strong> and <strong><em>requests</em></strong> for a given link)"}
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
                {if $brokenlinks.submitteremail == ""}
                <td>{$brokenlinks.submitter|safetext}</td>
                {else}
                <td><a href="mailto:{$brokenlinks.submitteremail}">{$brokenlinks.submitter|safetext}</a></td>
                {/if}

                {if $brokenlinks.owneremail == ""}
                <td>{$brokenlinks.owner|safetext}</td>
                {else}
                <td><a href="mailto:{$brokenlinks.owneremail}">{$brokenlinks.owner|safetext}</a></td>
                {/if}

                <td>
                    <a href="{modurl modname='Weblinks' type='admin' func='ignorebrokenlinks' rid=$brokenlinks.rid authid=$authid}">
                        {img modname='core' src='button_ok.png' set='icons/extrasmall' __alt="Ignore" __title="Ignore"}
                    </a>
                </td>
                <td>
                    <a href="{modurl modname='Weblinks' type='admin' func='delbrokenlinks' rid=$brokenlinks.rid lid=$brokenlinks.lid authid=$authid}">
                        {img modname='core' src='14_layer_deletelayer.png' set='icons/extrasmall' __alt="Delete" __title="Delete"}
                    </a>
                </td>

            </tr>
            {/foreach}
        </tbody>
    </table>

    {/if}

</div>
{include file="admin/footer.tpl"}