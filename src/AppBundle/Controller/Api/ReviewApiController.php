<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 11/04/2018
 * Time: 11:49
 */

namespace AppBundle\Controller\Api;


use AppBundle\Entity\Book;
use AppBundle\Entity\Review;
use AppBundle\Form\Api\ReviewApiType;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\Response;

class ReviewApiController extends BaseApiController
{
    /**
     * List book reviews
     *
     * This call returns a collection of review resources as a paginated response
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated collection of review sub-resources based on a given book resource id",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Review::class),
     *         @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="id", type="id", example="1"),
     *              @SWG\Property(property="title", type="string", example="Not one of the best"),
     *              @SWG\Property(property="text", type="string", example="I can't say I was particularly enthralled, too many plotholes."),
     *              @SWG\Property(property="rating", type="integer", example="3"),
     *              @SWG\Property(property="timestamp", type="datetime", example="2017-12-25T00:00:00+00:00"),
     *              @SWG\Property(property="user", type="string", example="lewis"),
     *              @SWG\Property(property="_links", type="string", example="_self:{ href: /api/v1/books/3/reviews/1 }")
     *
     *          )
     *     )
     * ),
     * @SWG\Response(
     *     response=404,
     *     description="Parent book resource could not be found",
     *     examples={
     *          "": "A book with that associated id does not exist"
     *     }
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="string",
     *     description="The field used to determine the number of reviews to return"
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="string",
     *     description="The field used to resolve the requested page"
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="The unique identifier of the requested book"
     * )
     * @SWG\Tag(name="books")
     *
     * GET Route annotation.
     * @Get("/books/{id}/reviews")
     */
    public function getReviewsAction($id, Request $request)
    {
        // get the requested book
        $books = $this->getDoctrine()->getRepository(Book::class)->find($id);

        // get all reviews for the selected book
        $reviews = $books->getReviewsArray();

        return $this->paginatedResponse(
            $request,
            $reviews,
            'worth_reading_api_get_reviews',
            $id
        );

    }

    /**
     *
     * Get a book review
     *
     * Retrieves a book review sub-resource based on a given book resource id and a given review resource id
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a single review resource",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Review::class),
     *         @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="id", type="id", example="1"),
     *              @SWG\Property(property="title", type="string", example="Not one of the best"),
     *              @SWG\Property(property="text", type="string", example="I can't say I was particularly enthralled, too many plotholes."),
     *              @SWG\Property(property="rating", type="integer", example="3"),
     *              @SWG\Property(property="timestamp", type="datetime", example="2017-12-25T00:00:00+00:00"),
     *              @SWG\Property(property="user", type="string", example="lewis"),
     *              @SWG\Property(property="_links", type="string", example="_self:{ href: /api/v1/books/3/reviews/1 }")
     *
     *          )
     *     )
     * ),
     *
     *  @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="The unique identifier of the requested book"
     * ),
     *
     *  @SWG\Parameter(
     *     name="id_review",
     *     in="path",
     *     type="integer",
     *     description="The unique identifier of the requested review"
     * )
     *
     * @SWG\Response(
     *     response=404,
     *     description="The parent book resource could not be found or the id of the requested review could not be found",
     *     examples={
     *          "": "A book with that associated id does not exist",
     *          "": "A review with that associated id does not exist"
     *     }
     * )
     *
     * @Get("/books/{id}/reviews/{id_review}")
     * @SWG\Tag(name="books")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getReviewAction($id, $id_review)
    {
        // get the requested book
        $books = $this->getDoctrine()->getRepository(Book::class)->find($id);

        // get all reviews for the selected book
        $reviews = $books->getReviewsArray();

        foreach ($reviews as $review) {
            if($review->getId() == $id_review) {
                return $this->createApiResponse($review);
            }
        }

        return $this->createApiResponse([], 404);
    }

    /**
     *
     * Deletes a book review resource
     *
     * Deletes a book review based on the parent book resources id and the id of the review resource
     *
     * @SWG\Response(
     *     response=200,
     *     description=""
     * ),
     *
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized Access",
     *     examples={
     *          "": "OAuth2 Authentication Required"
     *     }
     * ),
     *
     * @SWG\Response(
     *     response=404,
     *     description="The parent book resource could not be found or the id of the requested review could not be found",
     *     examples={
     *          "": "A book with that associated id does not exist",
     *          "": "A review with that associated id does not exist"
     *     }
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="The unique identifier of the requested book"
     * ),
     *
     *  @SWG\Parameter(
     *     name="id_review",
     *     in="path",
     *     type="integer",
     *     description="The unique identifier of the requested review"
     * )
     *
     * @Delete("/books/{id}/reviews/{id_review}")
     * @SWG\Tag(name="books")
     * @Security(name="Bearer")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteReviewAction($id, $id_review)
    {
        // get the requested book
        $books = $this->getDoctrine()->getRepository(Book::class)->find($id);

        if(!$books) {
            return $this->createApiResponse("Could not find the associated book object", "404");
        }

        // get all reviews for the selected book
        $reviews = $books->getReviewsArray();

        foreach ($reviews as $review) {
            if($review->getId() == $id_review) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($review);
                $em->flush();
                return $this->createApiResponse(null, 200);
            }
        }

        return $this->createApiResponse("Could not find the associated review object", 404);
    }

    /**
     * Create a book review
     *
     * Creates a book review resource based on a given parent book id
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
     *     response=404,
     *     description="The id of the parent book resource could not be found"
     * ),
     *
     * @SWG\Parameter(
     *         description="A short title for the review",
     *         in="body",
     *         name="title",
     *         required=true,
     *         @SWG\Schema(type="Not one of the best")
     *  ),
     *
     * @SWG\Parameter(
     *         description="The body of the review",
     *         in="body",
     *         name="text",
     *         required=true,
     *      @SWG\Schema(type="I can't say I was particularly...")
     *  ),
     *
     * @SWG\Parameter(
     *         description="A 1-5 star rating for the book",
     *         in="body",
     *         name="rating",
     *         required=true,
     *         @SWG\Schema(type="3")
     *  )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="The unique identifier of the requested book"
     * ),
     *
     *
     * @SWG\Tag(name="books")
     * @Security(name="Bearer")
     *
     * @Post("/books/{id}/reviews")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postReviewAction($id, Request $request)
    {
        // get the requested book
        $books = $this->getDoctrine()->getRepository(Book::class)->find($id);

        if(!$books) {
            return $this->createApiResponse("Could not find the associated book object", "404");
        }

        // check if the content type matches the expected json
        if($request->getContentType() != 'json') {
            return $this->createApiResponse(
                "Invalid JSON",
                Response::HTTP_BAD_REQUEST
            );
        }

        $review = new Review();

        $form = $this->createForm(ReviewApiType::class, $review);

        // submit the form
        $form->submit(json_decode($request->getContent(), true));

        if($form->isValid()) {

            $form->handleRequest($request);

            // associate authenticated user to review
            $review->setUser($this->getUser());
            $review->setBook($books);
            $review->setTimestamp(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($review);
            $em->flush();

            return $this->handleView($this->view(null, Response::HTTP_CREATED)
                ->setLocation($this->generateUrl('worth_reading_api_get_review', [
                    'id' => $id,
                    'id_review' => $review->get
                ])));

        }


        return $this->createApiResponse("Could not find the associated review object", 404);
    }


    /**
     * Updates a book review
     *
     * Updates a book review resource associated with the specified book resource id and review id
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
     *     response=404,
     *     description="The id of the parent book resource could not be found or the review id could not be found"
     * ),
     *
     * @SWG\Parameter(
     *         description="A short title for the review",
     *         in="body",
     *         name="title",
     *         required=true,
     *         @SWG\Schema(type="Not one of the best")
     *  ),
     *
     * @SWG\Parameter(
     *         description="The body of the review",
     *         in="body",
     *         name="text",
     *         required=true,
     *      @SWG\Schema(type="I can't say I was particularly...")
     *  ),
     *
     * @SWG\Parameter(
     *         description="A 1-5 star rating for the book",
     *         in="body",
     *         name="rating",
     *         required=true,
     *         @SWG\Schema(type="3")
     *  )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="The unique identifier of the requested book"
     * ),
     *
     * @SWG\Parameter(
     *     name="id_review",
     *     in="path",
     *     type="integer",
     *     description="The unique identifier of the requested review"
     * ),
     *
     * @Put("/books/{id}/reviews/{id_review}")
     * @SWG\Tag(name="books")
     * @Security(name="Bearer")
     * */
    public function putReviewAction($id, $review_id, Request $request)
    {
        // get the requested book
        $books = $this->getDoctrine()->getRepository(Book::class)->find($id);

        if(!$books) {
            return $this->createApiResponse("Could not find the associated book object", "404");
        }

        // check if the content type matches the expected json
        if($request->getContentType() != 'json') {
            return $this->createApiResponse(
                "Invalid JSON",
                Response::HTTP_BAD_REQUEST
            );
        }

        $review = new Review();

        $form = $this->createForm(ReviewApiType::class, $review);

        // submit the form
        $form->submit(json_decode($request->getContent(), true));

        if($form->isValid()) {

            $form->handleRequest($request);

            // associate authenticated user to review
            $review->setUser($this->getUser());
            $review->setBook($books);
            $review->setTimestamp(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($review);
            $em->flush();

            return $this->handleView($this->view(null, Response::HTTP_CREATED)
                ->setLocation($this->generateUrl('worth_reading_api_get_review', [
                    'id' => $id,
                    'id_review' => $review->get
                ])));

        }


        return $this->createApiResponse("Could not find the associated review object", 404);
    }
}