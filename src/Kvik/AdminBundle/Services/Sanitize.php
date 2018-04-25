<?php

namespace Kvik\AdminBundle\Services;

use Doctrine\ORM\EntityManagerInterface;

class Sanitize{

    private $em;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /*
     * Remove all specils chars and check if that slug already exist and add "-1" at the end
     */
    public function slugify($text, $name, $object){
        if( $text == null ) $text = $name;

        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = mb_strtolower($text);
        if (empty($text)) return 'n-a';

        $class = get_class($object);
        $check_object = $this->em->getRepository($class)->findOneBy([
            'slug' => $text
        ]);
        if( $check_object === $object || $check_object === null ) return $text;
        else return $text .'-1';
    }

}
