{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="cubes" size="small"}
    <h3>{gt text='elete Category'}</h3>
</div>

<form class="z-form" action="{modurl modname='Weblinks' type='admin' func='delcategory'}" method="post" enctype="application/x-www-form-urlencoded">
    <div>
        <fieldset>
            <legend>{gt text="Delete Category"}</legend>
            <input type="hidden" name="csrftoken" value="{insert name="csrftoken"}" />
            <input type="hidden" name="cid" value="{$cid}" />

            <div class="z-formrow">
                <p style="font-weight:bold">{gt text="Are you sure you want to delete this category, ALL subcategories and ALL links in this category and these subcategories?"}</p>
            </div>
        </fieldset>

        <div class="z-formbuttons">
            {button src='button_ok.png' set='icons/small' __alt="Delete Category" __title="Delete Category"}
            <a href="{modurl modname='Weblinks' type='admin' func='catview'}">{img modname='core' src='button_cancel.png' set='icons/small' __alt="Back" __title="Back"}</a>
        </div>

    </div>
</form>