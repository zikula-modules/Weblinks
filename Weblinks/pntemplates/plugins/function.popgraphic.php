<?php
/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @version $Id: function.popgraphic.php 40 2009-01-09 14:13:23Z herr.vorragend $
 * @copyright 2010 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

function smarty_function_popgraphic($params, &$smarty)
{
    if ($params['hits'] >= pnModGetVar('Weblinks', 'popular')) {
        echo "&nbsp;<img src=\"images/icons/extrasmall/flag.gif\" alt=\""._WL_POPULAR."\" title=\""._WL_POPULAR."\" />";
    }

    return;
}