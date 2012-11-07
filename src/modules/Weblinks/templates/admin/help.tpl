{*  $Id: weblinks_admin_help.html 165 2010-10-19 13:28:15Z herr.vorragend $  *}
{include file="weblinks_admin_header.html"}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname=core src=agt_internet.gif set=icons/large __alt="Links administer" __title="Links administer"}</div>
    <h2>{gt text="Help"}</h2>
    <div class="z-form">
        <fieldset>
            <legend>{gt text="Permissions"}</legend>
            <div class="z-informationmsg">
                <strong>{gt text="You have some possibilities to set permissions"}</strong><br /><br />
                <strong>{gt text="Users"}</strong><br /><br />
                {gt text="Group | Weblinks:: | .* | read"}<br />
                {gt text="// the group can see all links"}<br /><br />
                {gt text="Group | Weblinks:: | .* | comment"}<br />
                {gt text="// the group can add a link and comment a link, announce a broken link, submit a modification"}<br /><br />
                {gt text="Group | Weblinks::Category | ::CatID | none"}<br />
                {gt text="// the group cant see the category with the ID"}<br /><br /><br />
                <strong>{gt text="Moduleadmins"}</strong><br /><br />
                {gt text="Group | Weblinks:: | .* | edit"}<br />
                {gt text="// the group can modify links and categories"}<br /><br />
                {gt text="Group | Weblinks:: | .* | add"}<br />
                {gt text="// the group can modify and add links and categories"}<br /><br />
                {gt text="Group | Weblinks:: | .* | delete"}<br />
                {gt text="// the group can delete, modify and add links and categories"}<br /><br />
                {gt text="Group | Weblinks::Category | .* | edit / add / delete"}<br />
                {gt text="// the group can only modify / add / delete categories"}<br /><br />
                {gt text="Group | Weblinks::Link | .* | edit / add / delete"}<br />
                {gt text="// the group can only modify / add / delete links"}<br /><br />
                {gt text="Group | Weblinks:: | .* | admin"}<br />
                {gt text="// the group can administrate the Weblinks module with all functions"}<br /><br /><br />
                <strong>{gt text="scribite"}</strong><br />
                {gt text="Group | scribite:: | Weblinks:: | comment"}<br />
                {gt text="// the group can use scribite in Weblinks textareas if they have no comment permissions (ungeristered Users)"}<br /><br />
                {gt text="// if a group has add permissions and higher, they can see an icon in their user profil to add links"}
            </div>
        </fieldset>
        <fieldset>
            <legend>{gt text="Hint for the pending module"}</legend>
            <div class="z-informationmsg">
                <a href="http://code.zikula.org/pendingcontent">{gt text="http://code.zikula.org/pendingcontent"}</a><br /><br />
                {gt text="Name: New Links"}<br />
                {gt text="URL:  index.php?module=Web_Links&amp;type=admin"}<br />
                {gt text="SQL:  select count(*) from zk_links_newlink"}<br /><br />
                {gt text="Name: Broken Links"}<br />
                {gt text="URL:  index.php?module=Web_Links&amp;type=admin&amp;func=listbrokenlinks"}<br />
                {gt text="SQL:  select count(*) from zk_links_modrequest WHERE zk_links_modrequest.pn_brokenlink = 1"}<br /><br />
                {gt text="Name: Modify Links"}<br />
                {gt text="URL:  index.php?module=Web_Links&amp;type=admin&amp;func=listmodrequests"}<br />
                {gt text="SQL:  select count(*) from zk_links_modrequest WHERE zk_links_modrequest.pn_brokenlink = 0"}<br /><br />
                {gt text="// zk_ if this is your table prefix"}
            </div>
        </fieldset>
    </div>
</div>
{include file="weblinks_admin_footer.html"}