<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Service\BookService;
use AppBundle\Service\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Retrieves the current users wishlist
     *
     * @return Response
     */
    public function wishlistAction(BookService $bookService, ReviewService $reviewService) : Response {

        $books = $this->getUser()->getWishlist();
        $books= $bookService->getPreparedBooks($books, $this->getUser(), $reviewService);

        return $this->render(':user:wishlist.html.twig', [
           'books' => $books,
        ]);
    }

    public function addToWishlistAction(int $id) {
        $wishlist = $this->getUser()->getWishlist();

        foreach ($wishlist as $wishlistBook) {
            if($id === $wishlistBook->getId()) {
                return new Response('already added');
            }
        }

        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
        $this->getUser()->addWishlist($book);
        $em = $this->getDoctrine()->getManager();
        $em->persist($this->getUser());
        dump($this->getUser());
        $em->flush();
        return $this->redirectToRoute('worth_reading');
    }

    public function removeFromWishlistAction(int $id) {
        $wishlist = $this->getUser()->getWishlist();
        $book = null;

        foreach ($wishlist as $wishlistBook) {
            if($id === $wishlistBook->getId()) {
                $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
                break;
            }
        }

        if($book) {
            $this->getUser()->removeWishlist($book);
            $em = $this->getDoctrine()->getManager();
            $em->persist($this->getUser());
            $em->flush();
            return $this->redirectToRoute('worth_reading');
        } else {
            return new Response('the wishlist doesnt currently contain that book');
        }
    }

    public function myBooksAction(BookService $bookService, ReviewService $reviewService) {
        $books = $this->getUser()->getBooks();
        $books= $bookService->getPreparedBooks($books, $this->getUser(), $reviewService);

        return $this->render(':user:my_books.html.twig', [
            'books' => $books,
        ]);
    }
}
