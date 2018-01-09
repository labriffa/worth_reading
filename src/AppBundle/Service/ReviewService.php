<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 25/12/2017
 * Time: 00:37
 */

namespace AppBundle\Service;

use AppBundle\Entity\Book;
use AppBundle\Entity\Review;
use AppBundle\Entity\User;
use AppBundle\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * A class that exposes a number of functionalities concerned with
 * review entities
 *
 * Class ReviewService
 * @package AppBundle\Service
 */
class ReviewService extends EntityService
{
    public function __construct(ReviewRepository $repo, EntityManagerInterface $em, PaginationService $pagination)
    {
        parent::__construct($repo, $em, $pagination);
    }

    /**
     * Retrieves the user's review for a given book
     * if one exists
     *
     * @param Book $book
     * @param User $user
     * @return mixed|null
     */
    public function userBookReview(Book $book, User $user)
    {
        $userReviews = $user->getReviews();
        $userReview = null;

        foreach($userReviews as $review) {
            if($review->getBook() === $book) {
                $userReview = $review;
            }
        }

        return $userReview;
    }

    /**
     * Gets the number of reviews for a given book
     *
     * @param Book $book
     * @return int
     */
    public function numBookReviews(Book $book) {
        return count($book->getReviews());
    }

    /**
     * Gets the average review rating for a given book
     *
     * @param Book $book
     * @return float|int
     */
    public function averageBookReviewRating(Book $book) {
        $bookReviews = $book->getReviews();
        $rating = 0;

        foreach ($bookReviews as $review) {
            $rating += $review->getRating();
        }

        if($rating) {
            $rating = $rating / count($bookReviews);
        }

        return $rating;
    }

    /**
     * Add's a given review for a given book
     *
     * @param Review $review
     * @param Book $book
     * @param User $user
     */
    public function addReview(Review $review, Book $book, User $user)
    {
        $review->setTimestamp(new \DateTime(date("Y/m/d")));
        $review->setBook($book);
        $review->setUser($user);
        $this->getEm()->persist($review);
        $this->getEm()->flush();
    }
}