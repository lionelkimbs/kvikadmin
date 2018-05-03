<?php

namespace Kvik\AdminBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Kvik\AdminBundle\Entity\Post;
use Kvik\AdminBundle\Entity\Term;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PostManager{

    private $em;
    private $container;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->em = $entityManager;
        $this->container = $container;
    }

    /*
     * Add a post to Uncategorized
    **/
    public function addToUncategorized(Post $post){
        if( $post->getPostType() == 'post' && $post->getTerms()->isEmpty() ){
            $cat = $this->em->getRepository(Term::class)->findOneBy(['slug' => 'uncategorized']);
            if( $cat === null ) $cat = $this->getNewUncategorized();
            $post->addTerm($cat);
        }
    }

    /***
     * When a category is deleted, all posts without any category goes to Uncategorized
     */
    public function addPostsToUncategorized(PersistentCollection $posts){
        foreach ($posts as $post){
            if( $post->getTerms()->isEmpty() ) $this->addToUncategorized($post);
            $this->em->persist($post);
        }
    }

    /**
     * If parent added to $page is in his children list, remove it. A parent can't be a child at the same time
     * @param Post $page
     */
    public function removeParentInChildren(Post $page){
        foreach ($page->getChildren() as $child){
            if( $child === $page->getParent() ) $child->setParent(null);
        }
    }

    /*
     * Create and return Uncategorized term
    **/
    private function getNewUncategorized(){
        $cat = new Term();
        $cat->setTermType(1);
        $cat->setSlug($this->container->get('kvik.sanitize')->slugify('uncategorized'));
        $cat->setResume('Catégorie par défaut, tous les articles sans catégorie sont affectées à celle-ci.');
        $cat->setName('Uncategorized');
        $this->em->persist($cat);
        $this->em->flush();

        return $cat;
    }

}
