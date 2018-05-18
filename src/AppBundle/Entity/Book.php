<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ Exclude;
use JMS\Serializer\Annotation as JMS;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;


/**
 * Book

 * @ORM\Table(name="books")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BookRepository")
 * @Vich\Uploadable
 * @Serializer\AccessorOrder("custom", custom = {"id", "isbn", "title", "summary","book_cover", "author"})
 * @Hateoas\Relation("self", href = "expr('/api/v1/books/' ~ object.getId())")
 *
 */
class Book
{
    /**
     * @var int
     *
     * @SWG\Property(description="ID of this book")
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"books", "book"})
     */
    private $id;

    /**
     * @var string
     *
     * @SWG\Property(description="13-digit ISBN of this book")
     *
     * @ORM\Column(name="isbn", type="string", length=255, unique=true)
     *
     * @Assert\Isbn(
     *     type = "isbn13",
     *     message = "This value is not  valid."
     * )
     * @Groups({"books", "book"})
     */
    private $isbn;

    /**
     * @var string
     *
     * @SWG\Property(description="Title of this book")
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Groups({"books", "book"})
     */
    private $title;

    /**
     * @var string|null
     *
     * @SWG\Property(description="Summary of this book")
     *
     * @ORM\Column(name="summary", type="text", nullable=true)
     *
     */
    private $summary;

    /**
     * @var string
     *
     * @SWG\Property(description="Name of this book")
     *
     * @ORM\Column(name="bookCover", type="string", length=255, nullable=true)
     * @Exclude
     */
    private $bookCover;

    /**
     * @Vich\UploadableField(mapping="book_cover", fileNameProperty="bookCover")
     *
     * @var File
     * @Exclude
     */
    private $bookCoverFile;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Author", inversedBy="books")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     * @Exclude
     *
     */
    private $authors;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="books")
     * @ORM\JoinColumn(name="genre_id", referencedColumnName="id")
     * @Exclude
     */
    private $genres;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Review", mappedBy="book")
     * @Exclude
     */
    private $reviews;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="books")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     * @Exclude
     */
    private $user;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="wishlist")
     * @Exclude
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
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     * @Exclude
     */
    private $updatedAt;

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
     * Get Authors
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAuthor()
    {
        return $this->authors;
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
     * Gets an array representation of reviews
     *
     * @return array
     */
    public function getReviewsArray()
    {
        return $this->reviews->getValues();
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

    public function getBookCoverFile() {
        return $this->bookCoverFile;
    }

    public function setBookCoverFile(File $image = null)
    {
        $this->bookCoverFile = $image;

        $this->bookCover = $image->getFilename();

        if ($image instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTime());
        }
    }

    public function setIsLovedByCurrentUser($isLovedByCurrentUser)
    {
        $this->isLovedByCurrentUser = $isLovedByCurrentUser;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return Book
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Type("array<string>")
     * @Serializer\SerializedName("authors")
     */
    public function getAuthorName() {

        $authors = [];

        foreach($this->getAuthors() as $author) {
            array_push($authors, $author->getName());
        }

        return $authors;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Type("array<string>")
     * @Serializer\SerializedName("genres")
     */
    public function getGenreName() {

        $genres = [];

        foreach($this->getGenres() as $genre) {
            array_push($genres, $genre->getName());
        }

        return $genres;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("cover")
     */
    public function getBookCoverLocation() {
        return 'http://localhost:8003/uploads/books/covers/' . $this->getBookCover();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("user")
     */
    public function getUsername() {
        return $this->getUser()->getUsername();
    }
}
