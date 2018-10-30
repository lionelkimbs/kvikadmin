<?php

namespace Kvik\AdminBundle\Repository;

class TermRepository extends \Doctrine\ORM\EntityRepository
{
    /*
     * Return array of term by $type params
     */
    public function findTerms($type){
        return $this->createQueryBuilder('t')
            ->where('t.termType = :type')
            ->setParameter('type', $type)
            ->orderBy('t.name', 'ASC')
        ;
    }
    public function getTheTerms($type, $params){
        $offset = !is_null($params['pge']) ? ($params['pge'] - 1)*20 : 0;

        return $this->findTerms($type)
            ->setFirstResult( $offset )
            ->setMaxResults(20)
        ;
    }

    public function getTotalTerms($type){
        return $this->findTerms($type)
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleResult()
        ;
    }

}
