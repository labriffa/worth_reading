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
     * List Users
     *
     * This call returns a collection of users as a paginated response
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a paginated collection of users",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=User::class),
     *         @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="id", type="integer", example="1"),
     *              @SWG\Property(property="name", type="string", example="Horror")
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
     * @SWG\Tag(name="users")
     * @Security(name="Bearer")
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
}