<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 24/12/2017
 * Time: 16:01
 */

namespace AppBundle\Service;


use AppBundle\Controller\BookController;
use AppBundle\Entity\Book;
use AppBundle\Entity\User;
use AppBundle\Repository\BookRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * A class that exposes a number of functionalities concerned with
 * book entities
 *
 * Class BookService
 * @package AppBundle\Service
 */
class BookService extends EntityService
{
    private $tokenStorage;
    private $user;
    private $reviewService;

    public function __construct(BookRepository $repo,
                                EntityManager $em,
                                PaginationService $pageService,
                                TokenStorage $tokenStorage,
                                ReviewService $reviewService)
    {
        parent::__construct($repo, $em, $pageService);
        $this->tokenStorage = $tokenStorage;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->reviewService = $reviewService;
    }

    /**
     * Adds a given book
     *
     * @param Book $book
     * @param User|null $user
     */
    public function add(Book $book, User $user = null)
    {
        $book->setUser($user);
        $this->getEm()->persist($book);
        $this->getEm()->flush($book);
    }

    /**
     * Updates a given book
     *
     * @param Book $book
     * @param User|null $user
     */
    public function update(Book $book, User $user = null)
    {
        $this->add($book, $user);
    }

    /**
     * Retrieves a single book based on a given id
     *
     * @param int $id
     * @return null|object
     */
    public function getBook(int $id) : Book
    {
        return $this->getRepo()->find($id);
    }

    /**
     * Retrieves all books
     *
     * @return array
     */
    public function getBooks()
    {
        return $this->getRepo()->findAll();
    }

    /**
     * Returns books based on a set of given filters
     *
     * @param array $filters
     * @return array
     */
    public function filter(array $filters)
    {
        return $this->getRepo()->filter($filters);
    }

    /**
     * Searches for books based on a given search term
     *
     * @param string $query
     * @return static
     */
    public function searchTitle(string $query)
    {
        return $this->getRepo()->searchTitle($query);
    }

    /**
     * Retrieves a set number of books in order of most recent
     */
    public function getRecentBooks(int $limit)
    {
        return $this->prepareBooks($this->getRepo()->getRecentBooks($limit));
    }

    /**
     * Appends the average review rating and whether or not the current user has added
     * this book to their reading list
     *
     * @param $books
     * @param User|null $user
     * @param ReviewService $reviewService
     * @return mixed
     */
    public function prepareBooks($books)
    {
        if('string' === gettype($this->user)) {
            $this->user = null;
        }

        foreach ($books as $book) {
            $book->setAvgRating($this->reviewService->averageBookReviewRating($book));
            if($this->user) {
                $wishlist = $this->user->getWishlist();
                if($wishlist->contains($book)) {
                    $book->setIsLovedByCurrentUser(true);
                }
            }
        }

        return $books;
    }

    /**
     * Adds pagination functionality to a given collection
     *
     * @param $books
     * @param string $pageParamKey
     * @return mixed
     */
    public function paginate($books, $pageParamKey=EntityService::DEFAULT_PAGE_PARAM_KEY)
    {
        return $this->prepareBooks($this->getPagination()->paginate($books, $pageParamKey));
    }
}