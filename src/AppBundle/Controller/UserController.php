<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Service\BookService;
use AppBundle\Service\ReviewService;
use AppBundle\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UserController extends Controller
{
    /**
     * Retrieves the current users wishlist
     *
     * @Template(":user:wishlist.html.twig")
     * @return Response
     */
    public function wishlistAction(UserService $userService) : array
    {
        $books= $userService->getWishlist($this->getUser());

        return ['books' => $books];
    }

    public function addToWishlistAction(int $id, UserService $userService) : Response
    {
        $userService->addToWishlist($id, $this->getUser());

        return $this->redirectToRoute('worth_reading');
    }


    public function removeFromWishlistAction(int $id, UserService $userService)
    {
        $userService->removeFromWishlist($id, $this->getUser());

        return $this->redirectToRoute('worth_reading');
    }

    /**
     * @Template(":user:my_books.html.twig")
     * @param UserService $userService
     * @return Response
     */
    public function myBooksAction(UserService $userService) : array
    {
        $books = $userService->getBooks($this->getUser());

        return ['books' => $books];
    }
}
