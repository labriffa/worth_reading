<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Handles the control of admin related pages
 *
 * Class AdminController
 * @package AppBundle\Controller
 */
class AdminController extends Controller
{
    /**
     * Controls the index page for the admin panel
     *
     * @Template(":admin:index.html.twig")
     * @param UserService $userService
     * @return array
     */
    public function indexAction(UserService $userService) : array
    {
        $users = $userService->getAllUsers();
        $curretntUser = $this->getUser();

        $securityRoles = $this->getParameter('security.role_hierarchy.roles');

        return [
            'users' => $users,
            'security_roles' => $securityRoles,
            'currentUser' => $curretntUser,
        ];
    }

    /**
     * Changes the role for a given user
     *
     * @param string $role
     * @param int $userId
     * @return Response
     */
    public function changeRoleAction(string $role, int $userId)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        if($user) {
            $securityRoles = $this->getParameter('security.role_hierarchy.roles');

            $role = isset($securityRoles[$role]) ? $role : 'ROLE_USER';

            $user->setRoles([$role]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return new Response('Role Succesfully Changed');
    }
}
