<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 01/04/2018
 * Time: 01:38
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use AppBundle\Form\Api\AuthorApiType;
use AppBundle\Form\GenreType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

class UserApiController extends BaseApiController
{
    /**
     * Retrieve Users
     *
     * Returns a paginated collection of user resources
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated collection of user resources",
     *     examples={
     *          "": {
     *                  { "id": "1", "username": "lewis", "_self" : "{ href: /api/v1/users/1 }" }
     *              },
     *          "Pagination Links": {
     *              "self": {
     *                  "href": "/api/v1/users?page=1&limit=10"
     *              },
     *                  "first": {
     *                      "href": "/api/v1/users?page=1&limit=10"
     *              },
     *                  "last": {
     *                      "href": "/api/v1/users?page=2&limit=10"
     *              },
     *                  "next": {
     *                      "href": "/api/v1/users?page=2&limit=10"
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
     *     description="The number of users to return"
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     default=1,
     *     description="The page index of the paginated response"
     * )
     *
     * @SWG\Tag(name="users")
     *
     */
    public function getUsersAction(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->paginatedResponse(
            $request,
            $users,
            'worth_reading_api_get_books'
        );
    }


    /**
     * Retrieve a user
     *
     * Retrieves a user resource based on a given id
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the user associated with the requested id",
     *     examples={
     *          "": {
     *                  { "id": "1", "username": "Lewis", "_self" : "{ href: /api/v1/users/1 }" }
     *              },
     *     }
     * )
     *
     * @SWG\Response(
     *     response=404,
     *     description="ID not found or invalid",
     *     examples={
     *          "": "A user with that ID could not be found"
     *     }
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="The id of the user to retrieve",
     *     required=true,
     *     type="integer"
     * )
     * @SWG\Tag(name="users")
     *
     * @param $id
     * @return null|object
     */
    public function getUserAction($id) {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if(!$user) {
            $this->createApiResponse("The user with that id could not be found", 404);
        }

        return $user;
    }
}