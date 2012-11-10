<?php

/**
 * Zikula Application Framework
 *
 * Weblinks
 *
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Weblinks_Entity_Link as Link;

class Weblinks_Entity_Repository_LinkRepository extends EntityRepository
{

    /**
     * Retrieve count of categories
     * 
     * @return Scalar 
     */
    public function getCount($status = Link::ACTIVE, $comp = ">=")
    {
        $dql = "SELECT COUNT(DISTINCT a.lid) FROM Weblinks_Entity_Link a";
        $dql .= " WHERE a.status $comp :status";
        
        $query = $this->_em->createQuery($dql);
        
        $query->setParameter('status', $status);

        return $query->getResult(Query::HYDRATE_SINGLE_SCALAR);
    }
    
    /**
     * Retrieve collection of links
     * 
     * @param integer $status
     * @return array 
     */
    public function getLinks($status = Link::ACTIVE)
    {
        $dql = "SELECT a FROM Weblinks_Entity_Link a";
        $dql .= " WHERE a.status = :status";
        
        $query = $this->_em->createQuery($dql);

        $query->setParameter('status', $status);
        
        return $query->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * Increment the hit count for an item
     * 
     * @param object $item
     * @param integer $increment
     */
    public function addHit($item, $increment = 1)
    {
        $currentValue = $item->getHits();
        try {
            $item->setHits($currentValue + $increment);
            $this->_em->persist($item);
            $this->_em->flush();
        } catch (Exception $e) {
            echo "<pre>";
            var_dump($e->getMessage());
            die;
        }
    }

}