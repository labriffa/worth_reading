<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 24/12/2017
 * Time: 16:01
 */

namespace AppBundle\Service;


use AppBundle\Entity\Book;
use AppBundle\Entity\User;
use AppBundle\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

class BookService
{
    private $repository;
    private $entityManager;

    // image uploader
    private $coverFileUploader;

    public function __construct(BookRepository $repository, EntityManager $entityManager, CoverFileUploader $coverFileUploader)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->coverFileUploader = $coverFileUploader;
    }

    public function add(Book $book, User $user = null)
    {
        $coverFile = $book->getBookCover();

        $coverFileName = $this->coverFileUploader->upload($coverFile);
        $book->setBookCover($coverFileName);
        $book->setUser($user);
        $this->entityManager->persist($book);
        $this->entityManager->flush($book);
    }

    public function update(Book $book, User $user = null)
    {
        $this->add($book, $user);
    }

    public function getBook(int $id)
    {
        return $this->repository->find($id);
    }

    public function getBooks()
    {
        return $this->repository->findAll();
    }

    public function getRandomBooks($num)
    {
        return $this->repository->findRandom($num);
    }

    public function searchTitle(string $query)
    {
        return $this->repository->searchTitle($query);
    }

    public function getPreparedBooks($books, User $user = null, ReviewService $reviewService)
    {
        foreach ($books as $book) {
            $book->setAvgRating($reviewService->averageBookReviewRating($book));
            if($user) {
                $wishlist = $user->getWishlist();
                if($wishlist->contains($book)) {
                    $book->setIsLovedByCurrentUser(true);
                }
            }
        }

        return $books;
    }
}