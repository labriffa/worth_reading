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
use Doctrine\ORM\EntityManager;

class ReviewService
{
    private $repository;
    private $entityManager;

    public function __construct(ReviewRepository $repository, EntityManager $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function userBookReview(Book $book, User $user) {
        $userReviews = $user->getReviews();
        $userReview = null;

        foreach($userReviews as $review) {
            if($review->getBook() === $book) {
                $userReview = $review;
            }
        }

        return $userReview;
    }

    public function numBookReviews(Book $book) {
        return count($book->getReviews());
    }

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

    public function addReview(Review $review, Book $book, User $user)
    {
        dump($review);
        $review->setTimestamp(new \DateTime(date("Y/m/d")));
        $review->setBook($book);
        $review->setUser($user);
        $this->entityManager->persist($review);
        $this->entityManager->flush();
    }
}