<?php

namespace Kvik\AdminBundle\Repository;

class TermRepository extends \Doctrine\ORM\EntityRepository
{
    /*
     * Return array of term by $type params
     */
    private function findTerms($type){
        return $this->createQueryBuilder('t')
            ->where('t.termType = :type')
            ->setParameter('type', $type)
            ->orderBy('t.name', 'ASC')
        ;
    }
    public function getTerms($type){
        if( $type == 'categories' ) $type = 1;
        else $type = 2;
        return $this->findTerms($type)->getQuery()->getResult();
    }

    /*
     * Return one term object by $type and $id params
     */
    private function findOneTerm($type, $id){
        return $this->createQueryBuilder('t')
            ->where('t.termType = :type')
            ->andWhere('t.id = :id')
            ->setParameters([
                'type' => $type,
                'id' => $id
            ])
        ;
    }
    public function getOneTerm($type, $id){
        if( $type == 'categories' ) $type = 1;
        else $type = 2;
        return $this->findOneTerm($type, $id)->getQuery()->getSingleResult();
    }
}
