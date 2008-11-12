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
*
*/
function Web_Links_userapi_categories($args)
{
    // Security check
    if (!SecurityUtil::checkPermission('Web_Links::', '::', ACCESS_READ)) {
        return $categories;
    }

    // define the permission filter to apply
    $permFilter = array(array('realm'           => 0,
                              'component_left'  => 'Web_Links',
                              'component_right' => 'Category',
                              'instance_left'   => 'title',
                              'instance_right'  => 'cat_id',
                              'level'           => ACCESS_READ));

	// get the objects from the db
    $objArray = DBUtil::selectObjectArray('links_categories', '', '', '-1', '-1', '', $permFilter);

    // Check for an error with the database code, and if so set an appropriate
    // error message and return
    if ($objArray === false) {
        return LogUtil::registerError (_GETFAILED);
    }

    return $objArray;
}
?>