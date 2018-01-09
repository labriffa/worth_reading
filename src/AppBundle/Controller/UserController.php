<?php

namespace AppBundle\Controller;

use AppBundle\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends Controller
{
    /**
     * Retrieves the current users wishlist
     *
     * @Template(":user:wishlist.html.twig")
     * @Security("has_role('ROLE_USER')")
     * @return array
     */
    public function wishlistAction(UserService $userService) : array
    {
        $books= $userService->getWishlist($this->getUser());

        return ['books' => $books];
    }

    /**
     * Handles the adding of book items to the current user's reading list
     *
     * @param int $id
     * @param UserService $userService
     * @return Response
     */
    public function addToWishlistAction(int $id, UserService $userService) : Response
    {
        $userService->addToWishlist($id, $this->getUser());

        return $this->redirectToRoute('worth_reading');
    }


    /**
     * Handles the removing of book items from the current user's reading
     * list
     *
     * @param int $id
     * @param UserService $userService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeFromWishlistAction(int $id, UserService $userService)
    {
        $userService->removeFromWishlist($id, $this->getUser());

        return $this->redirectToRoute('worth_reading');
    }

    /**
     * Retrieves all books for the current user
     *
     * @Template(":user:my_books.html.twig")
     * @Security("has_role('ROLE_USER')")
     * @param UserService $userService
     * @return Response
     */
    public function myBooksAction(UserService $userService) : array
    {
        $books = $userService->getBooks($this->getUser());

        return ['books' => $books];
    }
}
