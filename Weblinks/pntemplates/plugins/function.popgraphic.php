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

function smarty_function_popgraphic($params, &$smarty)
{
    extract($params);
    unset($params);

    if ($hits>=pnModGetVar('Web_Links', 'popular')) {
        echo "&nbsp;<img src=\"images/icons/extrasmall/flag.gif\" alt=\""._WL_POPULAR."\" title=\""._WL_POPULAR."\" />";
    }
    return;
}