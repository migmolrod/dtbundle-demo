<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Post
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
     * @Assert\Length(
     *     min = "10",
     *     minMessage = "Post content is too short ({{ limit }} characters minimum)"
     * )
     */
    private $content;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $visible;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\Email()
     */
    private $authorEmail;

    /**
     * @var \datetime
     *
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $publishedAt;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Comment",
     *     mappedBy="post",
     *     orphanRemoval=true
     * )
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->publishedAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->title;
    }

    /**
     * Is the given User the author of this Post?
     *
     * @param User $user
     *
     * @return boolean
     */
    public function isAuthor(User $user = null)
    {
        return $user->getEmail() == $this->getAuthorEmail();
    }

    /**
     * Get id.
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
     * Set content.
     *
     * @param text $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return text
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Add comment.
     *
     * @param Comment $comment
     *
     * @return $this
     */
    public function addComment(Comment $comment)
    {
        $this->comments->add($comment);
        $comment->setPost($this);

        return $this;
    }

    /**
     * Remove comment.
     *
     * @param Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
        $comment->setPost(null);
    }

    /**
     * Get comments.
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set visible.
     *
     * @param boolean $visible
     *
     * @return $this
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible.
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
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
     * @param \datetime $publishedAt
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
