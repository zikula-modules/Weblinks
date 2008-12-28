<?php
/**
 * Zikula Application Framework
 *
 * Web_Links
 *
 * @version $Id$
 * @copyright 2008 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

/**
 * initialise block
 */
function Web_Links_lastweblinksblock_init()
{
    // Security
    pnSecAddSchema('Web_linksblock::', 'Block title::');
}

/**
 * get information on block
 */
function Web_Links_lastweblinksblock_info()
{
    // Values
    return array('text_type' => 'lastweblinks',
                 'module' => 'Web_Links',
                 'text_type_long' => 'Latest Web Links',
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true,
                 'admin_tableless' => true);
}

/**
 * display block
 */
function Web_Links_lastweblinksblock_display($blockinfo)
{
    // Security check
    if (!SecurityUtil::checkPermission('Web_Linksblock::', "$blockinfo[title]::", ACCESS_READ)) {
        return;
    }

    // check if the quotes module is available
    if (!pnModAvailable('Web_Links')) {
        return;
    }

    // Create output object
    $pnRender = pnRender::getInstance('Web_Links');

    //  Check if the block is cached
    if ($pnRender->is_cached('weblinks_block_lastweblinks.html')) {
        $blockinfo['content'] = $pnRender->fetch('weblinks_block_lastweblinks.html');
        return pnBlockThemeBlock($blockinfo);
    }

    $pnRender->assign('weblinks', pnModAPIFunc('Web_Links', 'user', 'lastweblinks'));

    // Populate block info and pass to theme
    $blockinfo['content'] = $pnRender->fetch('weblinks_block_lastweblinks.html');

    return pnBlockThemeBlock($blockinfo);
}

/**
 * modify block settings
 */
function Web_Links_lastweblinksblock_modify($blockinfo)
{
    // Create output object
    $pnRender = pnRender::getInstance('Feeds', false);

    // Get current content
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    // Defaults
    if (empty($vars['feedid'])) {
        $vars['feedid'] = 1;
    }
    if (empty($vars['displayimage'])) {
        $vars['displayimage'] = 0;
    }
    if (empty($vars['numitems'])) {
        $vars['numitems'] = -1;
    }

    // The API function is called.  The arguments to the function are passed in
    // as their own arguments array
    $items = pnModAPIFunc('Feeds', 'user', 'getall');

    // create an array for feednames and id's for the template
    $allfeeds = array();
    foreach($items as $item) {
        $allfeeds[$item['fid']] = $item['name'];
    }
    $pnRender->assign('allfeeds', $allfeeds);

    // assign the block vars
    $pnRender->assign($vars);

    // Return output
    return $pnRender->fetch('feeds_block_displayfeed_modify.htm');
}

/**
 * update block settings
 */
function Web_Links_lastweblinksblock_update($blockinfo)
{
    $vars['feedid'] = FormUtil::getPassedValue('feedid', 1, 'POST');
    $vars['numitems'] = FormUtil::getPassedValue('numitems', 0, 'POST');
    $vars['displayimage'] = FormUtil::getPassedValue('displayimage', -1, 'POST');

    $blockinfo['content'] = pnBlockVarsToContent($vars);

    return $blockinfo;
}
