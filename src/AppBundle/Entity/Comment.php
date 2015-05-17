<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Comment
 *
 * @ORM\Table()
 * @ORM\Entity
 *
 * @package AppBundle\Entity
 */
class Comment
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var text
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Please don't leave your comment blank!")
     * @Assert\Length(
     *     min = "5",
     *     minMessage = "Comment is too short ({{ limit }} characters minimum)",
     *     max = "10000",
     *     maxMessage = "Comment is too long ({{ limit }} characters maximum)"
     * )
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $authorEmail;

    /**
     * @var \datetime
     *
     * @ORM\Column(type="datetime")
     */
    private $publishedAt;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->title;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set comment.
     *
     * @param string $comment
     *
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set post.
     *
     * @param Post $post
     *
     * @return $this
     */
    public function setPost(Post $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post.
     *
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set author email.
     *
     * @param string $authorEmail
     *
     * @return $this
     */
    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;

        return $this;
    }

    /**
     * Get author email.
     *
     * @return string
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    /**
     * Set published at.
     *
     * @param $publishedAt
     *
     * @return $this
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get published at.
     *
     * @return \datetime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }
}
