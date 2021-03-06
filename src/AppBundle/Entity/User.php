<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Review", mappedBy="user")
     */
    private $reviews;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Book", mappedBy="user")
     */
    private $books;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Book", inversedBy="lovedBy")
     * @ORM\JoinTable(name="wishlist")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $wishlist;

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
     * Add review.
     *
     * @param \AppBundle\Entity\Review $review
     *
     * @return User
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
     * Add book.
     *
     * @param \AppBundle\Entity\Book $book
     *
     * @return User
     */
    public function addBook(\AppBundle\Entity\Book $book)
    {
        $this->books[] = $book;

        return $this;
    }

    /**
     * Remove book.
     *
     * @param \AppBundle\Entity\Book $book
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBook(\AppBundle\Entity\Book $book)
    {
        return $this->books->removeElement($book);
    }

    /**
     * Get books.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * Add wishlist.
     *
     * @param \AppBundle\Entity\book $wishlist
     *
     * @return User
     */
    public function addWishlist(\AppBundle\Entity\book $wishlist)
    {
        $this->wishlist[] = $wishlist;

        return $this;
    }

    /**
     * Remove wishlist.
     *
     * @param \AppBundle\Entity\book $wishlist
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeWishlist(\AppBundle\Entity\book $wishlist)
    {
        return $this->wishlist->removeElement($wishlist);
    }

    /**
     * Get wishlist.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWishlist()
    {
        return $this->wishlist;
    }
}
