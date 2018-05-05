<?php
namespace AppBundle\Controller\Api;

use AppBundle\Entity\Genre;
use AppBundle\Form\GenreType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;


class GenreApiController extends BaseApiController {

//    /**
//     * List genres
//     *
//     * This call returns a collection of genres as a paginated response
//     *
//     * @SWG\Response(
//     *     response=200,
//     *     description="Returns a paginated collection of genres",
//     *     @SWG\Schema(
//     *         type="array",
//     *         @Model(type=Genre::class),
//     *         @SWG\Items(
//     *              type="object",
//     *              @SWG\Property(property="id", type="integer", example="1"),
//     *              @SWG\Property(property="name", type="string", example="Horror")
//     *          )
//     *     )
//     * )
//     * @SWG\Parameter(
//     *     name="limit",
//     *     in="query",
//     *     type="string",
//     *     description="The field used to determine the number of genres returned"
//     * )
//     * @SWG\Parameter(
//     *     name="page",
//     *     in="query",
//     *     type="string",
//     *     description="The field used to resolve the requested page"
//     * )
//     * @SWG\Tag(name="genres")
//     * @Security(name="Bearer")
//     */
//    public function getGenresAction(Request $request)
//    {
//        $genres = $this->getDoctrine()->getRepository(Genre::class)->findAll();
//
//        return $this->paginatedResponse(
//            $request,
//            $genres,
//            'worth_reading_api_get_genres'
//        );
//    }
//
//    /**
//     * Retrieve a genre
//     *
//     * @SWG\Response(
//     *     response=200,
//     *     description="Returns the genre associated with the requested id",
//     *     @SWG\Schema(
//     *         type="array",
//     *         @Model(type=Genre::class),
//     *         @SWG\Items(
//     *              type="object",
//     *              @SWG\Property(property="id", type="integer", example="1"),
//     *              @SWG\Property(property="name", type="string", example="Horror")
//     *          )
//     *     )
//     * )
//     * @SWG\Parameter(
//     *     name="id",
//     *     in="path",
//     *     description="The id of the genre to retrieve",
//     *     required=true,
//     *     type="integer"
//     * )
//     * @SWG\Tag(name="genres")
//     * @Security(name="Bearer")
//     *
//     * @param Integer integer
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function getGenreAction($id)
//    {
//        $genre = $this->getDoctrine()->getRepository(Genre::class)->find($id);
//
//        if(!$genre) {
//            return $this->createApiResponse(
//                "Requested genre does not exit",
//                Response::HTTP_BAD_REQUEST);
//        }
//
//        return $this->createApiResponse($genre);
//    }
//
//    /**
//     * Create a genre
//     *
//     * @SWG\Response(
//     *     response=200,
//     *     description="Returns a paginated collection of genres",
//     * )
//     * @SWG\Tag(name="genres")
//     * @Security(name="Bearer")
//     *
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function postGenreAction(Request $request)
//    {
//        $genre = new Genre();
//        $form = $this->createForm(GenreType::class, $genre);
//
//        // check if the content type matches the expected json
//        if($request->getContentType() != 'json') {
//            return $this->createApiResponse(
//                "Invalid JSON",
//                Response::HTTP_BAD_REQUEST
//            );
//        }
//
//        // submit the form
//        $form->submit(json_decode($request->getContent(), true));
//
//        // check if the form is valid
//        if($form->isValid()) {
//
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($genre);
//            $em->flush();
//
//            // set the header to point to the new resource
//            return $this->handleView($this->view(null, Response::HTTP_CREATED)
//                ->setLocation($this->generateUrl('worth_reading_api_get_genre', [
//                    'id' => $genre->getId()
//                ])));
//
//        } else {
//            return $this->handleView($this->view($form , Response::HTTP_BAD_REQUEST));
//        }
//    }
//
//    /**
//     * Update a genre
//     *
//     * @SWG\Response(
//     *     response=200,
//     *     description="Returns a paginated collection of genres"
//     * )
//     * @SWG\Parameter(
//     *     name="id",
//     *     in="path",
//     *     description="The id of the genre to update",
//     *     required=true,
//     *     type="integer"
//     * )
//     * @SWG\Tag(name="genres")
//     * @Security(name="Bearer")
//     *
//     * @param Integer integer
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function putGenreAction($id, Request $request)
//    {
//        $genre = $this->getDoctrine()->getRepository(Genre::class)->find($id);
//
//        // Check if this genre exists
//        if(!$genre) {
//            return $this->createApiResponse(null, Response::HTTP_BAD_REQUEST);
//        } else {
//            // create the form
//            $form = $this->createForm(GenreType::class, $genre);
//
//            // check if the content type matches the expected json
//            if($request->getContentType() != 'json') {
//                return $this->createApiResponse(null, Response::HTTP_BAD_REQUEST);
//            }
//
//            // submit the form
//            $form->submit(json_decode($request->getContent(), true));
//
//            // check if the form is valid
//            if($form->isValid()) {
//
//                $em = $this->getDoctrine()->getManager();
//                $em->persist($genre);
//                $em->flush();
//
//                // set the header to point to the updated resource
//                return $this->handleView($this->view(null, 200)
//                    ->setLocation($this->generateUrl('worth_reading_api_get_genre', [
//                        'id' => $genre->getId()
//                    ])));
//
//            } else {
//                return $this->createApiResponse($form, Response::HTTP_BAD_REQUEST);
//            }
//        }
//    }
//
//    public function updateGenresAction() {
//        return $this->createApiResponse("Can't update entire resource", Response::HTTP_METHOD_NOT_ALLOWED);
//
//    }
//
//    public function deleteGenresAction() {
//        return $this->createApiResponse("Can't delete entire resource", Response::HTTP_METHOD_NOT_ALLOWED);
//
//    }
//
//    /**
//     * Remove a genre
//     *
//     * @SWG\Response(
//     *     response=200,
//     *     description="Returns a paginated collection of genres"
//     * )
//     * @SWG\Parameter(
//     *     name="id",
//     *     in="path",
//     *     description="The id of the genre to remove",
//     *     required=true,
//     *     type="integer"
//     * )
//     * @SWG\Tag(name="genres")
//     * @Security(name="Bearer")
//     *
//     * @param Integer $id
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function deleteGenreAction($id)
//    {
//        $genre = $this->getDoctrine()->getRepository(Genre::class)->find($id);
//
//        if (!$genre) {
//            return $this->createApiResponse(null, Response::HTTP_BAD_REQUEST);
//        }
//
//        $em = $this->getDoctrine()->getManager();
//        $em->remove($genre);
//        $em->flush();
//
//        return $this->createApiResponse(null, Response::HTTP_NO_CONTENT);
//    }
}

