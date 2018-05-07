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
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations\Get;

class ReviewApiController extends BaseApiController
{
    /**
     * List book reviews
     *
     * This call returns a collection of genres as a paginated response
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated collection of reviews for a given book",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Review::class),
     *         @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="id", type="id", example="1"),
     *              @SWG\Property(property="title", type="string", example="Not one of the best"),
     *              @SWG\Property(property="text", type="string", example="I can't say I was particularly enthralled, too many plotholes."),
     *              @SWG\Property(property="rating", type="integer", example="3"),
     *              @SWG\Property(property="timestamp", type="datetime", example="2017-12-25T00:00:00+00:00")
     *          )
     *     )
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="string",
     *     description="The field used to determine the number of genres returned"
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
     * @Security(name="Bearer")
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
}