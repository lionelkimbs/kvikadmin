<?php

namespace Kvik\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @ORM\Table(name="kb_post")
 * @ORM\Entity(repositoryClass="Kvik\AdminBundle\Repository\PostRepository")
 */
class Post
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="excerpt", type="string", length=255, nullable=true)
     */
    private $excerpt;

    /**
     * @var string
     *
     * @ORM\Column(name="post_status", type="string", length=20)
     */
    private $postStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="comment_status", type="string", length=20)
     */
    private $commentStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="post_password", type="string", length=255, nullable=true)
     */
    private $postPassword;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_edit", type="datetimetz")
     */
    private $dateEdit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_pub", type="datetimetz", nullable=true)
     */
    private $datePub;

    /**
     * @var int
     *
     * @ORM\Column(name="menu_order", type="integer", nullable=true)
     */
    private $menuOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="post_type", type="string", length=20)
     */
    private $postType;

    /**
     * @var string
     *
     * @ORM\Column(name="metadescription", type="string", length=255, nullable=true)
     */
    private $metadescription;


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
     * Set title
     *
     * @param string $title
     *
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Post
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Post
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
     * Set excerpt
     *
     * @param string $excerpt
     *
     * @return Post
     */
    public function setExcerpt($excerpt)
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    /**
     * Get excerpt
     *
     * @return string
     */
    public function getExcerpt()
    {
        return $this->excerpt;
    }

    /**
     * Set postStatus
     *
     * @param string $postStatus
     *
     * @return Post
     */
    public function setPostStatus($postStatus)
    {
        $this->postStatus = $postStatus;

        return $this;
    }

    /**
     * Get postStatus
     *
     * @return string
     */
    public function getPostStatus()
    {
        return $this->postStatus;
    }

    /**
     * Set commentStatus
     *
     * @param string $commentStatus
     *
     * @return Post
     */
    public function setCommentStatus($commentStatus)
    {
        $this->commentStatus = $commentStatus;

        return $this;
    }

    /**
     * Get commentStatus
     *
     * @return string
     */
    public function getCommentStatus()
    {
        return $this->commentStatus;
    }

    /**
     * Set postPassword
     *
     * @param string $postPassword
     *
     * @return Post
     */
    public function setPostPassword($postPassword)
    {
        $this->postPassword = $postPassword;

        return $this;
    }

    /**
     * Get postPassword
     *
     * @return string
     */
    public function getPostPassword()
    {
        return $this->postPassword;
    }

    /**
     * Set dateEdit
     *
     * @param \DateTime $dateEdit
     *
     * @return Post
     */
    public function setDateEdit($dateEdit)
    {
        $this->dateEdit = $dateEdit;

        return $this;
    }

    /**
     * Get dateEdit
     *
     * @return \DateTime
     */
    public function getDateEdit()
    {
        return $this->dateEdit;
    }

    /**
     * Set datePub
     *
     * @param \DateTime $datePub
     *
     * @return Post
     */
    public function setDatePub($datePub)
    {
        $this->datePub = $datePub;

        return $this;
    }

    /**
     * Get datePub
     *
     * @return \DateTime
     */
    public function getDatePub()
    {
        return $this->datePub;
    }

    /**
     * Set menuOrder
     *
     * @param integer $menuOrder
     *
     * @return Post
     */
    public function setMenuOrder($menuOrder)
    {
        $this->menuOrder = $menuOrder;

        return $this;
    }

    /**
     * Get menuOrder
     *
     * @return int
     */
    public function getMenuOrder()
    {
        return $this->menuOrder;
    }

    /**
     * Set postType
     *
     * @param string $postType
     *
     * @return Post
     */
    public function setPostType($postType)
    {
        $this->postType = $postType;

        return $this;
    }

    /**
     * Get postType
     *
     * @return string
     */
    public function getPostType()
    {
        return $this->postType;
    }

    /**
     * Set metadescription
     *
     * @param string $metadescription
     *
     * @return Post
     */
    public function setMetadescription($metadescription)
    {
        $this->metadescription = $metadescription;

        return $this;
    }

    /**
     * Get metadescription
     *
     * @return string
     */
    public function getMetadescription()
    {
        return $this->metadescription;
    }
}

