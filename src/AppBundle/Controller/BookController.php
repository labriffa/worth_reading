<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\Review;
use AppBundle\Form\BookType;

use AppBundle\Form\ReviewType;
use AppBundle\Service\AuthorService;
use AppBundle\Service\BookService;
use AppBundle\Service\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BookController extends Controller
{
     const BOOKS_PER_PAGE = 6;

    /**
     * Retrieves all books
     *
     * @Template(":book:index.html.twig")
     * @return Response
     */
    public function indexAction(BookService $bookService) : array
    {
        $books = $bookService->getRecentBooks(BookController::BOOKS_PER_PAGE);

        return ['books' => $books];
    }

    /**
     * @Template(":book:books.html.twig")
     * @param Request $request
     * @param BookService $bookService
     * @return array
     */
    public function booksAction(Request $request, BookService $bookService) : array
    {
        $genre = $request->query->get('genre');
        $author = $request->query->get('author');

        $arr = $bookService->filter(['genres' => $genre, 'authors' => $author]);

        $pagination = $bookService->paginate($arr['books'], 'page');

        return [
            'books' => $pagination,
            'filters' => $arr['filters']
        ];
    }

    /**
     * Retrieves a single book entry
     *
     * @Template(":book:single.html.twig")
     * @param int $id
     * @return Response
     */
    public function showAction(Request $request, Book $book, ReviewService $reviewService) : array
    {
        // get user
        $user = $this->getUser();

        $review = $user ? $reviewService->userBookReview($book, $user) : new Review();

        $review_form = $this->createForm(ReviewType::class, $review);

        // if the user is logged in
        if($user) {
            $review_form->handleRequest($request);

            // if this is a new review or the user is editing their existing review
            if(!$review || $review->getId() == $review_form->getData()->getId()) {
                if($review_form->isSubmitted() && $review_form->isValid()) {
                    $reviewService->addReview($review_form->getData(), $book, $user);
                    return $this->redirect($request->getUri());
                }
            }
        }

        return [
            'book' => $book,
            'review_form' => $review_form->createView(),
            'review' => $review,
            'avgRating' => $reviewService->averageBookReviewRating($book),
            'numReviews' => $reviewService->numBookReviews($book),
        ];
    }

    /**
     * Creates a new book entry
     *
     * @Template(":book:new.html.twig")
     * @return Response
     */
    public function newAction(Request $request, BookService $bookService) : array
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $foundBook = $this->getDoctrine()->getRepository(Book::class)->findBy(['isbn' => $book->getIsbn()]);
            if(!$foundBook) {
                $bookService->add($book, $this->getUser());
                return $this->redirectToRoute('worth_reading_books_show', ['id'=>$book->getId()]);
            } else {
                $this->get('session')->getFlashBag()->add('error', 'A book with that ISBN already exists');
            }
        }

        return ['form' => $form->createView()];
    }

    /**
     * Edits a single book entry
     *
     * @Template(":book:edit.html.twig")
     * @param Book $book
     * @return Response
     */
    public function editAction(Request $request, Book $book, BookService $bookService) : array
    {
        // access control covers anonymous users, but we also need to make sure this is a user book
        if($book->getUser() !== $this->getUser()) {
            throw new AccessDeniedException('You do not have permission to view this page');
        }

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $bookService->update($book, $this->getUser());
        }

        return ['form' => $form->createView(), 'book' => $book];
    }

    /**
     * Removes a single book entry
     *
     * @param int $id
     * @return Response
     */
    public function removeAction(Book $book) : Response
    {
        if($book->getUser() !== $this->getUser()) {
            throw new AccessDeniedException('You do not have permission to perform this action');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($book);
        $em->flush();

        return $this->redirectToRoute('worth_reading_user_my_books');
    }


    /**
     * @Template(":book:search.html.twig")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request, BookService $bookService, AuthorService $authorService) : array
    {
        $query = $request->query->get('q');

        $books = $bookService->paginate($bookService->searchTitle($query), 'page-book');
        $authors = $authorService->paginate($authorService->searchName($query), 'page-author');

        return [
            'books' => $books,
            'authors' => $authors,
            'searchQuery' => $query,
        ];
    }
}
