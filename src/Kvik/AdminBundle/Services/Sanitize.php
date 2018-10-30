<?php

namespace Kvik\AdminBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Kvik\AdminBundle\Entity\Post;
use Kvik\AdminBundle\Entity\Term;

class Sanitize{
    private $em;
    public function __construct(EntityManagerInterface $entityManager){
        $this->em = $entityManager;
    }

    /*
     * Remove all specils chars and check if that slug already exist and add "-1" at the end
     */
    public function slugify($text, $name = null, $object = null){

        if( $text == null ) $text = $name;

        $text = htmlentities( $text, ENT_NOQUOTES, 'utf-8' );
        $text = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $text );
        $text = preg_replace( '#&([A-za-z]{2})(?:lig);#', '\1', $text );

        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//IGNORE', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = mb_strtolower($text);
        if (empty($text)){
            $date = new \DateTime();
            return 'n-a-' .$date->getTimestamp();
        }

        //*: Check if $object is a Post or a Term
        if (is_a($object, Post::class)) $check_object = $this->em->getRepository(Post::class)->findOneBy(['slug' => $text]);
        else $check_object = $this->em->getRepository(Term::class)->findOneBy(['slug' => $text]);

        if( $check_object === $object || $check_object === null ) return $text;
        else return $text .'-1';
    }
}
