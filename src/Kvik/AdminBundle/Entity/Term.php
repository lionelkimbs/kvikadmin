<?php

namespace Kvik\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Term
 *
 * @ORM\Table(name="kb_term")
 * @ORM\Entity(repositoryClass="Kvik\AdminBundle\Repository\TermRepository")
 */
class Term
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="resume", type="text", nullable=true)
     */
    private $resume;

    /**
     * @var int
     *
     * @ORM\Column(name="term_type", type="smallint")
     */
    private $termType;


    /**
     * One Category has Many Categories.
     * @ORM\OneToMany(targetEntity="Kvik\AdminBundle\Entity\Term", mappedBy="parent")
     */
    private $children;
    /**
     * Many Categories have One Category.
     * @ORM\ManyToOne(targetEntity="Kvik\AdminBundle\Entity\Term", inversedBy="children")
     */
    private $parent;
    /**
     * Many Terms have Many Posts.
     * @ORM\ManyToMany(targetEntity="Kvik\AdminBundle\Entity\Post", mappedBy="terms")
     */
    private $posts;

    /**
     * Term constructor.
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Term
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Term
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set resume
     *
     * @param string $resume
     *
     * @return Term
     */
    public function setResume($resume)
    {
        $this->resume = $resume;

        return $this;
    }

    /**
     * Get resume
     *
     * @return string
     */
    public function getResume()
    {
        return $this->resume;
    }

    /**
     * Set termType
     *
     * @param integer $termType
     *
     * @return Term
     */
    public function setTermType($termType)
    {
        $this->termType = $termType;

        return $this;
    }

    /**
     * Get termType
     *
     * @return int
     */
    public function getTermType()
    {
        return $this->termType;
    }

    /**
     * Add child
     *
     * @param \Kvik\AdminBundle\Entity\Term $child
     *
     * @return Term
     */
    public function addChild(\Kvik\AdminBundle\Entity\Term $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \Kvik\AdminBundle\Entity\Term $child
     */
    public function removeChild(\Kvik\AdminBundle\Entity\Term $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \Kvik\AdminBundle\Entity\Term $parent
     *
     * @return Term
     */
    public function setParent(\Kvik\AdminBundle\Entity\Term $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Kvik\AdminBundle\Entity\Term
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add post
     *
     * @param \Kvik\AdminBundle\Entity\Post $post
     *
     * @return Term
     */
    public function addPost(\Kvik\AdminBundle\Entity\Post $post)
    {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * Remove post
     *
     * @param \Kvik\AdminBundle\Entity\Post $post
     */
    public function removePost(\Kvik\AdminBundle\Entity\Post $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }
}
