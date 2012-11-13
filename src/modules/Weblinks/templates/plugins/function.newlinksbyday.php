<?php

/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
function smarty_function_newlinksbyday($params, Zikula_View $view)
{
    if (($params['newlinkshowdays'] != "7" && $params['newlinkshowdays'] != "14" && $params['newlinkshowdays'] != "30") ||
            (!is_numeric($params['newlinkshowdays'])) || (!isset($params['newlinkshowdays']))) {
        $params['newlinkshowdays'] = "7";
    }
    
    $beginning = new DateTime();
    $beginning->setTime(0, 0, 0);
    $end = new DateTime();
    $end->setTime(23, 59, 59);
    
    $em = ServiceUtil::getService('doctrine.entitymanager');
    
    $counter = 0;
    while ($counter < (int)$params['newlinkshowdays']) {
        $dql = "SELECT COUNT(DISTINCT a.lid) FROM Weblinks_Entity_Link a";
        $dql .= " WHERE a.status = :status";
        $dql .= " AND a.date >= :beginning";
        $dql .= " AND a.date <= :end";

        $query = $em->createQuery($dql);
        $query->setParameter('status', Weblinks_Entity_Link::ACTIVE);
        $query->setParameter('beginning', $beginning);
        $query->setParameter('end', $end);
        $count = $query->getResult(Doctrine\ORM\Query::HYDRATE_SINGLE_SCALAR);

        echo "<a href='" . DataUtil::formatForDisplay(ModUtil::url('Weblinks', 'user', 'newlinksdate', array('selectdate' => $beginning->format("U")))) . "'>" . DataUtil::formatForDisplay($beginning->format("M j, Y")) . "</a>&nbsp;(" . DataUtil::formatForDisplay($count) . ")<br />";
        $beginning->modify("-1 day");
        $end->modify("-1 day");
        $counter++;
    }
}