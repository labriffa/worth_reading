<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 29/03/2018
 * Time: 02:33
 */

namespace AppBundle\Controller\Api;

use AppBundle\AppBundle;
use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Form\Api\BookApiType;
use AppBundle\Form\GenreType;
use AppBundle\Repository\BookRepository;
use AppBundle\Service\BookService;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations as Annotations;
use FOS\RestBundle as Rest;

class BookApiController extends BaseApiController
{
    /**
     * Retrieve books
     *
     * Returns a paginated collection of book resources
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated collection of book resources",
     *     examples={
     *          "": {
     *                  { "id": 1, "isbn": "9780450031069", "title": "Salem's Lot", "summary": "Thousands of miles away from the small township of 'Salem's Lot...", "authors": "[ Stephen King ]", "genres": "[ Horror, Thriller ]", "cover": "http://localhost:8003/uploads/books/covers/bad2be8c3bbb539f8abe32c723cc4929", "user": "lewis", "_links": "_self : { href: /api/v1/books/1 }" }
     *              },
     *          "Pagination Links": {
     *              "self": {
     *                  "href": "/api/v1/books?page=1&limit=10"
     *              },
     *                  "first": {
     *                      "href": "/api/v1/books?page=1&limit=10"
     *              },
     *                  "last": {
     *                      "href": "/api/v1/books?page=2&limit=10"
     *              },
     *                  "next": {
     *                      "href": "/api/v1/books?page=2&limit=10"
     *              }
     *          }
     *     }
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     default=10,
     *     required=false,
     *     description="The number of books to return"
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     default=1,
     *     description="The page index of the paginated response"
     * )
     * @SWG\Parameter(
     *     name="q",
     *     in="query",
     *     type="string",
     *     required=false,
     *     description="The search term to query on for searching against a book's title"
     * )
     *
     * @SWG\Tag(name="books")
     */
    public function getBooksAction(Request $request)
    {

        // check if were searching
        $query = $request->query->get('q');

        if($query) {
            $books = $bookRepo = $this->getDoctrine()->getRepository(Book::class)->searchTitle($query)->execute();
        } else {
            $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
        }

        return $this->paginatedResponse(
            $request,
            $books,
            'worth_reading_api_get_books'
        );
    }

    /**
     * Retrieve a book
     *
     * Returns a book resource specified by the passed id
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the details of a book resource associated with the requested ID",
     *     examples={
     *          "": {
     *                  { "id": 1, "isbn": "9780450031069", "title": "Salem's Lot", "summary": "Thousands of miles away from the small township of 'Salem's Lot...", "authors": "[ Stephen King ]", "genres": "[ Horror, Thriller ]", "cover": "http://localhost:8003/uploads/books/covers/bad2be8c3bbb539f8abe32c723cc4929", "user": "lewis", "_links": "_self : { href: /api/v1/books/1 }" }
     *              }
     *     },
     * ),
     * @SWG\Response(
     *     response=404,
     *     description="ID not found or invalid",
     *     examples={
     *          "": "A book with that ID could not be found"
     *     }
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="The unique identifier of the requested book"
     * )
     *
     * @SWG\Tag(name="books")
     */
    public function getBookAction($id)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);

        if(!$book) {
            return $this->createApiResponse("A book with that ID could not be found", Response::HTTP_NOT_FOUND);
        }

        return $this->createApiResponse($book);
    }

    /**
     * Create a book
     *
     * Creates a book resource
     *
     * @SWG\Response(
     *     response=200,
     *     description="Sets the location header to the newly created resource",
     * ),
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized Access",
     *     examples={
     *          "": "OAuth2 Authentication Required"
     *     }
     * ),
     *
     * @SWG\Response(
     *     response=409,
     *     description="Duplicate Book Error",
     *     examples={
     *          "": "A book with that ISBN already exists"
     *     }
     * ),
     *
     * @SWG\Parameter(
     *         description="A 13 Digit ISBN Number",
     *         in="body",
     *         name="isbn",
     *         required=true,
     *         @SWG\Schema(type="9781447252313")
     *  ),
     *
     * @SWG\Parameter(
     *         description="Book title",
     *         in="body",
     *         name="title",
     *         required=true,
     *      @SWG\Schema(type="Tell Tale")
     *  ),
     *
     * @SWG\Parameter(
     *         description="A short description of the book",
     *         in="body",
     *         name="summary",
     *         required=true,
     *         @SWG\Schema(type="Author of the bestselling Clifton Chronicles, Jeffrey Archer, gives us...")
     *  ),
     *
     * @SWG\Parameter(
     *         description="The id of the associated author. (Also accepts an array of author ids)",
     *         in="body",
     *         name="authors",
     *         required=true,
     *         @SWG\Schema(type="[2,4]")
     *  ),
     *
     * @SWG\Parameter(
     *         description="The id of the associated genre. (Also accepts an array of genre ids)",
     *         in="body",
     *         name="genres",
     *         required=true,
     *         @SWG\Schema(type="[1,3]")
     *  ),
     *
     * @SWG\Parameter(
     *         description="A Base64 encoding of the book cover image",
     *         in="body",
     *         name="book_cover",
     *         required=true,
     *         @SWG\Schema(type="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAA...")
     *  ),
     *
     * @SWG\Tag(name="books")
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @return Response
     */
    public function postBookAction(Request $request)
    {
        $book = new Book();
        $form = $this->createForm(BookApiType::class, $book);

        // check if the content type matches the expected json
        if($request->getContentType() != 'json') {
            return $this->createApiResponse(
                "Invalid JSON",
                Response::HTTP_BAD_REQUEST
            );
        }

        // submit the form
        $form->submit(json_decode($request->getContent(), true));

        // check if the form is valid
        if($form->isValid()) {

            $book->setBookCoverFile(
                $this->convertBase64Image(
                    $this->getParameter('book_covers_directory'),
                    $form->get('bookCoverFile')->getData()
                )
            );

            // get current user
            $user = $this->get('security.token_storage')->getToken()->getUser();

            $author_id = $form->get('authors')->getData();
            $genre_id = $form->get('genres')->getData();

            $author = $this->getDoctrine()->getRepository(Author::class)->find($author_id);
            $genre = $this->getDoctrine()->getRepository(Genre::class)->find($genre_id);

            if(!$author || !$genre) {
                return $this->handleView($this->view("Must supply at least one valid author and genre" , Response::HTTP_BAD_REQUEST));
            }

            $book->addAuthor($author);
            $book->addGenre($genre);

            // set oauth user
            $book->setUser($user);

            $form->handleRequest($request);

            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            // set the header to point to the new resource
            return $this->handleView($this->view(null, Response::HTTP_CREATED)
                ->setLocation($this->generateUrl('worth_reading_api_get_book', [
                    'id' => $book->getId()
                ])));

        } else {
            return $this->createApiResponse("Invalid Data", Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Updates a book
     *
     * Updates a book resource
     *
     * @SWG\Response(
     *     response=200,
     *     description="",
     * ),
     * @SWG\Response(
     *     response=401,
     *     description="OAuth2 Authentication Required"
     * ),
     *
     * @SWG\Response(
     *     response=404,
     *     description="A book with that id could not be found"
     * ),
     *
     * @SWG\Response(
     *     response=409,
     *     description="A book with that ISBN already exists"
     * ),
     *
     * @SWG\Parameter(
     *         description="A 13 Digit ISBN Number",
     *         in="body",
     *         name="isbn",
     *         required=true,
     *         @SWG\Schema(type="9781447252313")
     *  ),
     *
     * @SWG\Parameter(
     *         description="Book title",
     *         in="body",
     *         name="title",
     *         required=true,
     *      @SWG\Schema(type="Tell Tale")
     *  ),
     *
     * @SWG\Parameter(
     *         description="A short description of the book",
     *         in="body",
     *         name="summary",
     *         required=true,
     *         @SWG\Schema(type="Author of the bestselling Clifton Chronicles, Jeffrey Archer, gives us...")
     *  ),
     *
     * @SWG\Parameter(
     *         description="The id of the associated author. (Also accepts an array of author ids)",
     *         in="body",
     *         name="authors",
     *         required=true,
     *         @SWG\Schema(type="[2,4]")
     *  ),
     *
     * @SWG\Parameter(
     *         description="The id of the associated genre. (Also accepts an array of genre ids)",
     *         in="body",
     *         name="genres",
     *         required=true,
     *         @SWG\Schema(type="[1,3]")
     *  ),
     *
     * @SWG\Parameter(
     *         description="A Base64 encoding of the book cover image",
     *         in="body",
     *         name="book_cover",
     *         required=true,
     *         @SWG\Schema(type="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAA...")
     *  ),
     *
     * @SWG\Tag(name="books")
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @return Response
     */
    public function putBookAction($id, Request $request)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);

        if(!$book) {
            return $this->createApiResponse("The requested book could not be found", 404);
        }

        $form = $this->createForm(BookApiType::class, $book);

        // check if the content type matches the expected json
        if($request->getContentType() != 'json') {
            return $this->createApiResponse(
                "Invalid JSON",
                Response::HTTP_BAD_REQUEST
            );
        }

        // submit the form
        $form->submit(json_decode($request->getContent(), true));

        // check if the form is valid
        if($form->isValid()) {

            $book->setBookCoverFile(
                $this->convertBase64Image(
                    $this->getParameter('book_covers_directory'),
                    $form->get('bookCoverFile')->getData()
                )
            );

            // get current user
            $user = $this->get('security.token_storage')->getToken()->getUser();

            $author_id = $form->get('authors')->getData();
            $genre_id = $form->get('genres')->getData();

            $author = $this->getDoctrine()->getRepository(Author::class)->find($author_id);
            $genre = $this->getDoctrine()->getRepository(Genre::class)->find($genre_id);

            if(!$author || !$genre) {
                return $this->handleView($this->view("Must supply at least one valid author and genre" , Response::HTTP_BAD_REQUEST));
            }

            $book->addAuthor($author);
            $book->addGenre($genre);

            // set oauth user
            $book->setUser($user);

            $form->handleRequest($request);

            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            // set the header to point to the new resource
            return $this->handleView($this->view(null, Response::HTTP_CREATED)
                ->setLocation($this->generateUrl('worth_reading_api_get_book', [
                    'id' => $book->getId()
                ])));

        } else {
            return $this->createApiResponse("Invalid Data", Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Delete a book
     *
     * Deletes an individual book resource corresponding to a given ID
     *
     * @SWG\Response(
     *     response=200,
     *     description="",
     * ),
     * @SWG\Response(
     *     response=404,
     *     description="ID not found or invalid",
     *     examples={
     *          "": "A book with that ID could not be found"
     *     }
     * ),
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized Access",
     *     examples={
     *          "": "OAuth2 Authentication Required"
     *     }
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="The unique identifier of the book to delete",
     * )
     *
     * @SWG\Tag(name="books")
     * @Security(name="Bearer")
     *
     * @param $id
     * @return Response
     */
    public function deleteBookAction($id)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);

        if(!$book) {
            return $this->createApiResponse("A book with that id doesn't exist", Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($book);
        $em->flush();

        return $this->createApiResponse(null);
    }


    /**
     * Disallow puts on entire collections
     *
     * @return Response
     */
    public function putBooks() {
        return $this->createApiResponse("Can't update entire resource", Response::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * Disallow the deletion of entire collections
     *
     * @return Response
     */
    public function deleteBooks() {
        return $this->createApiResponse("Can't delete entire resource", Response::HTTP_METHOD_NOT_ALLOWED);
    }
}