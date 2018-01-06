<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Book
 *
 * @ORM\Table(name="books")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BookRepository")
 */
class Book
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
     * @ORM\Column(name="isbn", type="string", length=255, unique=true)
     */
    private $isbn;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="summary", type="text", nullable=true)
     */
    private $summary;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bookCover", type="string", length=255, nullable=true)
     */
    private $bookCover;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Author", inversedBy="books")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $authors;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="books")
     * @ORM\JoinColumn(name="genre_id", referencedColumnName="id")
     */
    private $genres;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Review", mappedBy="book")
     */
    private $reviews;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="books")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="wishlist")
     */
    private $lovedBy;

    /**
     * @var boolean
     */
    private $isLovedByCurrentUser;

    /**
     * @var float
     */
    private $avgRating;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set isbn.
     *
     * @param string $isbn
     *
     * @return Book
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * Get isbn.
     *
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Book
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
     * Set summary.
     *
     * @param string|null $summary
     *
     * @return Book
     */
    public function setSummary($summary = null)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary.
     *
     * @return string|null
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set bookCover.
     *
     * @param string|null $bookCover
     *
     * @return Book
     */
    public function setBookCover($bookCover = null)
    {
        $this->bookCover = $bookCover;

        return $this;
    }

    /**
     * Get bookCover.
     *
     * @return string|null
     */
    public function getBookCover()
    {
        return $this->bookCover;
    }

    /**
     * Set publicationDate.
     *
     * @param \DateTime $publicationDate
     *
     * @return Book
     */
    public function setPublicationDate($publicationDate)
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * Set author.
     *
     * @param \AppBundle\Entity\Author|null $author
     *
     * @return Book
     */
    public function setAuthor(\AppBundle\Entity\Author $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author.
     *
     * @return \AppBundle\Entity\Author|null
     */
    public function getAuthor()
    {
        return $this->author;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->authors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add author.
     *
     * @param \AppBundle\Entity\Author $author
     *
     * @return Book
     */
    public function addAuthor(\AppBundle\Entity\Author $author)
    {
        $this->authors[] = $author;

        return $this;
    }

    /**
     * Remove author.
     *
     * @param \AppBundle\Entity\Author $author
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAuthor(\AppBundle\Entity\Author $author)
    {
        return $this->authors->removeElement($author);
    }

    /**
     * Get authors.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Add genre.
     *
     * @param \AppBundle\Entity\Genre $genre
     *
     * @return Book
     */
    public function addGenre(\AppBundle\Entity\Genre $genre)
    {
        $this->genres[] = $genre;

        return $this;
    }

    /**
     * Remove genre.
     *
     * @param \AppBundle\Entity\Genre $genre
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeGenre(\AppBundle\Entity\Genre $genre)
    {
        return $this->genres->removeElement($genre);
    }

    /**
     * Get genres.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Add review.
     *
     * @param \AppBundle\Entity\Review $review
     *
     * @return Book
     */
    public function addReview(\AppBundle\Entity\Review $review)
    {
        $this->reviews[] = $review;

        return $this;
    }

    /**
     * Remove review.
     *
     * @param \AppBundle\Entity\Review $review
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeReview(\AppBundle\Entity\Review $review)
    {
        return $this->reviews->removeElement($review);
    }

    /**
     * Get reviews.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User|null $user
     *
     * @return Book
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getAvgRating() {
        return $this->avgRating;
    }

    public function setAvgRating(float $avgRating) {
        $this->avgRating = $avgRating;
    }

    /**
     * Add lovedBy.
     *
     * @param \AppBundle\Entity\User $lovedBy
     *
     * @return Book
     */
    public function addLovedBy(\AppBundle\Entity\User $lovedBy)
    {
        $this->lovedBy[] = $lovedBy;

        return $this;
    }

    /**
     * Remove lovedBy.
     *
     * @param \AppBundle\Entity\User $lovedBy
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeLovedBy(\AppBundle\Entity\User $lovedBy)
    {
        return $this->lovedBy->removeElement($lovedBy);
    }

    /**
     * Get lovedBy.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLovedBy()
    {
        return $this->lovedBy;
    }

    public function getIsLovedByCurrentUser()
    {
        return $this->isLovedByCurrentUser;
    }

    public function setIsLovedByCurrentUser($isLovedByCurrentUser)
    {
        $this->isLovedByCurrentUser = $isLovedByCurrentUser;
    }

    public function __toString()
    {
        return $this->getTitle();
    }
}
