<?php
/**
 * Zikula Application Framework
 *
 * Web_Links
 *
 * @version $Id: function.ezcommentscounter.php 40 2009-01-09 14:13:23Z herr.vorragend $
 * @copyright 2008 by Petzi-Juist
 * @link http://www.petzi-juist.de
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
 
/**
 * Smarty function to obtain the EZComments counter
 *
 * @author       Andreas Krapohl
 * @since        15.07.04
 * @param        array       $params      All attributes passed to this function from the template
 * @param        object      &$smarty     Reference to the Smarty object
 * @return       string      the comments counter
 */
function smarty_function_ezcommentscounter($params, &$smarty)
{
    // get the parameters
    extract($params);
    unset($params);

    if (!isset($lid)) {
        $smarty->trigger_error('ezcommentscounter: attribute sid required');
        return false;
    }

    // set default
    $ezcommentscounter = "";

    // Security check
    if (!pnSecAuthAction(0, 'EZComments::', "Web_Links:$lid:", ACCESS_READ)) {
        return $ezcommentsounter;
    }

    if (pnModAvailable('EZComments'))   {
        $dbconn =& pnDBGetConn(true);
        pnModDBInfoLoad("EZComments");
        $pntable =& pnDBGetTables();
        $EZCommentstable = $pntable['EZComments'];
        $EZCommentscolumn = &$pntable['EZComments_column'];
        $sql = "SELECT COUNT(1) FROM $EZCommentstable WHERE $EZCommentscolumn[modname] = 'Web_Links' AND $EZCommentscolumn[objectid] = '$lid'";
        $result =& $dbconn->Execute($sql);
        list($ezcommentscounter) = $result->fields;

        if ($ezcommentscounter=="0") $comments = _WL_FEELFREE2ADD;
        elseif ($ezcommentscounter=="1") $comments = "1 "._WL_COMMENT;
        else $comments = "$ezcommentscounter "._WL_COMMENTS;


    }
    if (isset($assign)) {
        $smarty->assign($assign, $comments);
    } else {
        return $comments;
    }
}
?>
