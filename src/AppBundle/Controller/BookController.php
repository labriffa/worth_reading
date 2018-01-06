<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\Review;
use AppBundle\Form\BookType;

use AppBundle\Form\ReviewType;
use AppBundle\Service\AuthorService;
use AppBundle\Service\BookService;
use AppBundle\Service\CoverFileUploader;
use AppBundle\Service\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BookController extends Controller
{
    /**
     * Retrieves all books
     *
     * @return Response
     */
    public function indexAction(Request $request, BookService $bookService, ReviewService $reviewService) : Response
    {
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $books,
            $request->query->getInt('page', 1),
            $this->getParameter('num_items_per_page') /*limit per page*/
        );

        $books = $bookService->getPreparedBooks($pagination, $this->getUser(), $reviewService);

        return $this->render(':book:index.html.twig', [
            'books' => $books,
            'showcase_books' => $books,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Retrieves a single book entry
     *
     * @param int $id
     * @return Response
     */
    public function showAction(Request $request, Book $book, ReviewService $reviewService) : Response
    {
        // get user
        $user = $this->getUser();

        $review = new Review();

        // check if user has already reviewed this book
        if($user) {
            $review = $reviewService->userBookReview($book, $user);
        }

        // create the review form
        $review_form = $this->createForm(ReviewType::class, $review);

        // get this books average rating and number of reviews
        $bookAvgRating = $reviewService->averageBookReviewRating($book);
        $bookNumReviews = $reviewService->numBookReviews($book);

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

        return $this->render(':book:single.html.twig', [
            'book' => $book,
            'review_form' => $review_form->createView(),
            'review' => $review,
            'avgRating' => $bookAvgRating,
            'numReviews' => $bookNumReviews,
        ]);
    }

    /**
     * Creates a new book entry
     *
     * @return Response
     */
    public function newAction(Request $request, BookService $bookService) : Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $bookService->add($book, $this->getUser());
        }

        return $this->render(':book:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edits a single book entry
     *
     * @param Book $book
     * @return Response
     */
    public function editAction(Request $request, Book $book, BookService $bookService) : Response
    {
        if($book->getUser() !== $this->getUser()) {
            throw new AccessDeniedException('You do not have permission to view this page');
        }

        $book->setBookCover(new File($this->getParameter('book_covers_directory').'/'.$book->getBookCover()));
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $bookService->update($book, $this->getUser());
        }

        return $this->render(':book:edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Removes a single book entry
     *
     * @param int $id
     */
    public function removeAction(int $id)
    {

    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request, BookService $bookService, AuthorService $authorService, ReviewService $reviewService)
    {
        $searchQuery = $request->query->get('q');

        $books = $bookService->searchTitle($searchQuery);
        $authors = $authorService->searchName($searchQuery);

        $paginator  = $this->get('knp_paginator');
        $bookPagination = $paginator->paginate(
            $books,
            $request->query->getInt('page-book', 1),
            $this->getParameter('num_items_per_page') /*limit per page*/,
            [
                'pageParameterName' => 'page-book'
            ]
        );

        $authorPagination = $paginator->paginate(
            $authors,
            $request->query->getInt('page-author', 1),
            $this->getParameter('num_items_per_page') /*limit per page*/,
            [
                'pageParameterName' => 'page-author'
            ]
        );

        $books = $bookService->getPreparedBooks($bookPagination, $this->getUser(), $reviewService);

        return $this->render(':book:search.html.twig', [
            'books' => $books,
            'authors' => $authors,
            'searchQuery' => $searchQuery,
            'bookPagination' => $bookPagination,
            'authorPagination' => $authorPagination,
        ]);
    }
}
