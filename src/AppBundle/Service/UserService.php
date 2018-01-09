<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 07/01/2018
 * Time: 03:56
 */

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;

/**
 * A class that exposes a number of functionalities concerned with
 * user entities
 *
 * Class UserService
 * @package AppBundle\Service
 */
class UserService extends EntityService
{
    private $bookService;

    public function __construct(UserRepository $repo,
                                EntityManager $em,
                                PaginationService $pageService,
                                BookService $bookService)
    {
        parent::__construct($repo, $em, $pageService);
        $this->bookService = $bookService;
    }

    /**
     * Retrieves all users
     *
     * @return mixed
     */
    public function getAllUsers()
    {
        return $this->paginate($this->getRepo()->findAll());
    }

    /**
     * Gets the user's wishlist
     *
     * @param User $user
     * @return mixed
     */
    public function getWishlist(User $user)
    {
        return $this->bookService->prepareBooks($user->getWishlist());
    }

    /**
     * Gets all books added by this user
     *
     * @param User $user
     * @return mixed
     */
    public function getBooks(User $user)
    {
        return $this->paginate($this->bookService->prepareBooks($user->getBooks()));
    }

    /**
     * Adds a given book to the given user's wishlist
     *
     * @param int $id
     * @param User $user
     */
    public function addToWishlist(int $id, User $user)
    {
        // check if this user has this book already
        if(!($this->isInWishlist($id, $user)))
        {
            $book = $this->bookService->getBook($id);
            $user->addWishlist($book);
            $this->getEm()->persist($user);
            $this->getEm()->flush();
        }
    }

    /**
     * Removes a given book from the given user's wishlist
     *
     * @param int $id
     * @param User $user
     */
    public function removeFromWishlist(int $id, User $user)
    {
        // check if this user actually has this book
        if(!($this->isInWishlist($id, $user)))
        {
            $book = $this->bookService->getBook($id);
            $user->removeWishlist($book);
            $this->getEm()->persist($user);
            $this->getEm()->flush();
        }
    }

    /**
     * Checks if a given book is already in a given
     * user's wishlist
     *
     * @param int $id
     * @param User $user
     * @return bool
     */
    private function isInWishlist(int $id, User $user) : bool
    {
        // get user wishlist
        $wishlistBooks = $user->getWishlist();

        foreach($wishlistBooks as $wishlistBook)
        {
            if($id == $wishlistBook->getId())
            {
                return true;
            }
        }

        return false;
    }
}