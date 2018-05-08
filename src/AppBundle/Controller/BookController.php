<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Review;
use AppBundle\Form\BookType;

use AppBundle\Form\ReviewType;
use AppBundle\Service\AuthorService;
use AppBundle\Service\BookService;
use AppBundle\Service\ReviewService;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Facades\Auth;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use GuzzleHttp\Client;

/**
 * Handles the control of book related pages
 *
 * Class BookController
 * @package AppBundle\Controller
 */
class BookController extends BaseController
{
     const BOOKS_PER_PAGE = 6;

    /**
     * Retrieves all books
     *
     * @Template(":book:index.html.twig")
     * @param BookService $bookService
     * @return array
     */
    public function indexAction(BookService $bookService) : array
    {
        $books = $bookService->getRecentBooks(BookController::BOOKS_PER_PAGE);

        return ['books' => $books];
    }

    /**
     * Retrieves books based on the currently selected filters
     *
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
     * @param Request $request
     * @param Book $book
     * @param ReviewService $reviewService
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function showAction(Request $request, Book $book, ReviewService $reviewService)
    {
        // get user
        $user = $this->getUser();

        $good_reads_reviews = "";

        $review = $user ? $reviewService->userBookReview($book, $user) : new Review();

        $review_form = $this->createForm(ReviewType::class, $review);

        // retrieve extra information from Google Books API

        // create guzzle client
        $client = new Client(['base_uri' => 'https://foo.com/api/']);

        // fetch response from google books api with this isbn
        $response = $client->get('https://www.googleapis.com/books/v1/volumes',
            [
                'query' =>
                [
                    'q' => 'isbn:' . $book->getIsbn()
                ]
            ]
        );

        $google_book = json_decode($response->getBody(), true);

        // get goodreads review widget

        $client = new Client();
        $res = $client->get('https://www.goodreads.com/book/isbn/'.$book->getIsbn().'?key=Y2L2h66TxHsrUF3sAH71dA&format=xml');

        $xmlstring = $res->getBody();

        $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);

        if(isset($array['book']['reviews_widget'])) {
            $good_reads_reviews = $array['book']['reviews_widget'];
        }

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
            'google_book' => isset($google_book["items"]) ? $google_book["items"] : [],
            'review_form' => $review_form->createView(),
            'review' => $review,
            'avgRating' => $reviewService->averageBookReviewRating($book),
            'numReviews' => $reviewService->numBookReviews($book),
            'good_reads_reviews' => $good_reads_reviews,
        ];
    }

    /**
     * Creates a new book entry
     *
     * @Template(":book:new.html.twig")
     * @Security("has_role('ROLE_USER')")
     */
    public function newAction(Request $request, BookService $bookService)
    {
        $book = new Book();
        $book_image = "";
        $full_image = "";

        // pre-populate fields from Google Books search
        if( $request->request->has('isbn') && $request->request->has('title') )  {
            $book->setIsbn($request->request->get('isbn'));
            $book->setTitle($request->request->get('title'));

            if ( $request->request->has('description') ) {
                $book->setSummary($request->request->get('description'));
            } else {
                $book->setSummary("");
            }


            // create guzzle client
            $client = new Client();

            // fetch response from google books api with this isbn
            $responseSingle = $client->get($request->request->get('selfLink'));

            // fetch response from google books api with this isbn
            $book_single = json_decode($responseSingle->getBody(), true);

            if($book_single) {
                if(isset($book_single["volumeInfo"]["imageLinks"]["small"])) {
                    $small_image_url = $book_single["volumeInfo"]["imageLinks"]["small"];

                    $filename = md5(uniqid());
                    $path = $this->getParameter('book_covers_directory');


                    copy($small_image_url, $path . '/' . $filename );

                    $file = new UploadedFile($path . '/' . $filename, $filename, "image/png", null, null, true);

                    $book->setBookCoverFile($file);

                    $book_image = "/uploads/books/covers/" . $filename;
                    $full_image = $path . '/' . $filename;
                }
            }

            $authors = json_decode($request->request->get('authors'), true);

            // go through each author and add them to the database if they are not available
            foreach($authors as $author_name) {
                $author = new Author();
                $em = $this->getDoctrine()->getManager();

                $author_names = $this->getDoctrine()->getRepository(Author::class)->findBy(["name" => $author_name]);

                // if this author exists don't add them
                if(!$author_names) {
                    $author->setName($author_name);

                    $author->setAvatarFile(
                        $this->convertBase64Image(
                            $this->getParameter('author_avatars_directory'),
                            base64_encode('https://themainstage.com/assets/cdn/users/profileImgs/default.png')
                        )
                    );

                    $filename = md5(uniqid());
                    $path = $this->getParameter('author_avatars_directory');

                    copy('https://themainstage.com/assets/cdn/users/profileImgs/default.png', $path . '/' . $filename );

                    $file = new UploadedFile($path . '/' . $filename, $filename, "image/png", null, null, true);

                    $author->setAvatar($file);

                    $filename = md5(uniqid());
                    $path = $this->getParameter('author_signatures_directory');

                    copy('https://themainstage.com/assets/cdn/users/profileImgs/default.png', $path . '/' . $filename );

                    $file = new UploadedFile($path . '/' . $filename, $filename, "image/png", null, null, true);

                    $author->setSignatureFile($file);


                    $author->setBiography("No Bio Available");
                    $em->persist($author);
                    $em->flush();
                }

                $author_names = $this->getDoctrine()->getRepository(Author::class)->findBy(["name" => $author_name]);

                if($author_names[0]) {
                    $book->addAuthor($author_names[0]);
                }
            }

            $categories = json_decode($request->request->get('categories'), true);

            foreach($categories as $category_name) {
                $genre = new Genre();
                $em = $this->getDoctrine()->getManager();

                $genre_names = $this->getDoctrine()->getRepository(Genre::class)->findBy(["name" => $category_name]);

                // if this genre doesn't exist add it
                if(!$genre_names) {
                    $genre->setName($category_name);

                    $em->persist($genre);
                    $em->flush();
                }

                $genre_names = $this->getDoctrine()->getRepository(Genre::class)->findBy(["name" => $category_name]);

                if($genre_names[0]) {
                    $book->addGenre($genre_names[0]);
                }
            }
        }


        // create new book
        $form = $this->createForm(BookType::class, $book);
        $arr = ["form"=>$form->createView()];

        if('POST' === $request->getMethod()) {

            if ($request->request->has('appbundle_book')) {
                $form->handleRequest($request);

                if($form->isSubmitted() && $form->isValid()) {
                    $foundBook = $this->getDoctrine()->getRepository(Book::class)->findBy(['isbn' => $book->getIsbn()]);
                    if(!$foundBook) {
                        if($request->request->has('full_image')) {
                            $path = $request->request->get('full_image');
                            $filename = basename($path);
                            $file = new UploadedFile($path, $filename, "image/png", null, null, true);
                            $book->setBookCoverFile($file);
                        }
                        $bookService->add($book, $this->getUser());
                        return $this->redirectToRoute('worth_reading_books_show', ['id'=>$book->getId()]);
                    } else {
                        $this->get('session')->getFlashBag()->add('error', 'A book with that ISBN already exists');
                    }
                }
            }


            // handle google books search query
            if ($request->request->has('search_term')) {

                $term = $request->request->get('search_term');

                // create guzzle client
                $client = new Client();

                // fetch response from google books api with this isbn
                $response = $client->get('https://www.googleapis.com/books/v1/volumes',
                    [
                        'query' =>
                            [
                                'q' => $term,
                                'maxResults' => 8,
                            ]
                    ]
                );

                $google_book = json_decode($response->getBody(), true);

                $arr["books"] = $google_book["items"];
            }
        }


        return [
            "form" => $arr["form"],
            "books" => isset($arr["books"]) ? $arr["books"] : [],
            "book" => $book,
            "book_image" => $book_image,
            "full_image" => $full_image
        ];
    }

    /**
     * Edits a single book entry
     *
     * @Template(":book:edit.html.twig")
     * @Security("has_role('ROLE_USER')")
     * @param Book $book
     * @return array
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
     * @Security("has_role('ROLE_USER')")
     * @param Book $book
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
     * Removes a given review
     *
     * @param Review $review
     */
    public function removeReviewAction(Review $review)
    {
        $user = $this->getUser();

        if(!$user || $user !== $review->getUser()) {
            return $this->redirectToRoute('worth_reading');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($review);
        $em->flush();

        return $this->redirectToRoute('worth_reading');
    }


    /**
     * Retrieves books based on the current search query
     *
     * @Template(":book:search.html.twig")
     * @param Request $request
     * @return array
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
