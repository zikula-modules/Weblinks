{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="cubes" size="small"}
    <h3>{gt text='Modify Category'}</h3>
</div>
<form class="z-form" action="{modurl modname='Weblinks' type='admin' func='savemodcategory'}" method="post" enctype="application/x-www-form-urlencoded">
    <div>
        <fieldset>
            <legend>{gt text="Modify a category"}</legend>
            <input type="hidden" name="csrftoken" value="{insert name="csrftoken"}" />
            <input type="hidden" name="modifiedcategory[cat_id]" value="{$category.cat_id}" />
            <div class="z-formrow">
                <label>{gt text="Name"}</label>
                <input id="title" type="text" name="modifiedcategory[title]" value="{$category.title|safetext}" size="51" maxlength="50" />
            </div>
            <div class="z-formrow">
                <label>{gt text="Parent category"}</label>
                <select id="pid" name="modifiedcategory[parent_id]"><option value="0">{gt text="None"}</option>{catlist scat=0 sel=$category.parent_id}</select>
            </div>
            <div class="z-formrow">
                <label>{gt text="Description"}</label>
                <textarea id="description" name="modifiedcategory[cdescription]" cols="50" rows="10">{$category.cdescription|safetext}</textarea>
            </div>
        </fieldset>
        <div class="z-formbuttons">
            {button src='button_ok.png' set='icons/small' __alt="Modify category" __title="Modify category"}
            <a href="{modurl modname='Weblinks' type='admin' func='catview'}">{img modname='core' src='button_cancel.png' set='icons/small' __alt="Back" __title="Back"}</a>
        </div>
    </div>
</form>