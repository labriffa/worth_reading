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
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations\Route;

class BookApiController extends BaseApiController
{
    /**
     * List books
     *
     * Gets book details over a paginated collection
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated collection of books",
     *     examples={
     *          "": {
     *                  { "id": 1, "isbn": "9780450031069", "title": "Salem's Lot", "summary": "Thousands of miles away from the small township of 'Salem's Lot..." },
     *                  { "id": 2, "isbn": "9780450040184", "title": "The Shining", "summary": "Jack Torrance's new job at the Overlook Hotel is the perfect..." }
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
     * @SWG\Tag(name="books")
     * @Security(name="Bearer")
     */
    public function getBooksAction(Request $request)
    {
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();

        return $this->paginatedResponse(
            $request,
            $books,
            'worth_reading_api_get_books'
        );
    }

    /**
     * Retrieve a book
     *
     * Gets the details of an individual book
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the details of a book associated with the requested ID",
     *     examples={
     *          "": {
     *                  { "id": 9, "isbn": "9780450040184", "title":"Fantastic Beasts and Where to Find Them", "summary": "When Magizoologist Newt Scamander arrives in New York, he intends...", "book_cover": "5f8e3b28be356a1cb2fbe453ef98f974" }
     *              }
     *     },
     * ),
     * @SWG\Response(
     *     response=404,
     *     description="ID not found or invalid",
     *     examples={
     *          "": {
     *                  { "error": { "message": "The requested book with the provided id could not be found" } }
     *              }
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
     * @Security(name="Bearer")
     */
    public function getBookAction($id)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);

        if(!$book) {
            return $this->createApiResponse(Response::HTTP_NOT_FOUND);
        }

        return $this->createApiResponse($book);
    }

    /**
     * Create a book
     *
     * /**
     * Delete a book
     *
     * Deletes an individual book entry corresponding to a given ID
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a success message on successful deletion",
     *     examples={
     *          "": {
     *                  { "success": { "message": "The book with an id of 9 was deleted" } }
     *              }
     *     }
     * ),
     * @SWG\Response(
     *     response=409,
     *     description="ID not found or invalid",
     *     examples={
     *          "": {
     *                  { "error": { "message": "The requested book with the provided id could not be found" } }
     *              }
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
                return $this->handleView($this->view($form , Response::HTTP_BAD_REQUEST));
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
            return $this->createApiResponse("dfdfdf", Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Delete a book
     *
     * Deletes an individual book entry corresponding to a given ID
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a success message on successful deletion",
     *     examples={
     *          "": {
     *                  { "success": { "message": "The book with an id of 9 was deleted" } }
     *              }
     *     }
     * ),
     * @SWG\Response(
     *     response=404,
     *     description="ID not found or invalid",
     *     examples={
     *          "": {
     *                  { "error": { "message": "The requested book with the provided id could not be found" } }
     *              }
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