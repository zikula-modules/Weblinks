<?php

/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
function smarty_function_countlinks($params, Zikula_View $view)
{
    $comparisonDate = new DateTime();
    $comparisonDate->modify("-$params[days] days");
    $comparisonDate->setTime(0, 0, 0);
    
    $em = ServiceUtil::getService('doctrine.entitymanager');
    $dql = "SELECT COUNT(DISTINCT a.lid) FROM Weblinks_Entity_Link a";
    $dql .= " WHERE a.status = :status";
    $dql .= " AND a.date >= :comparisondate";

    $query = $em->createQuery($dql);
    $query->setParameter('status', Weblinks_Entity_Link::ACTIVE);
    $query->setParameter('comparisondate', $comparisonDate);

    return $query->getResult(Doctrine\ORM\Query::HYDRATE_SINGLE_SCALAR);
}