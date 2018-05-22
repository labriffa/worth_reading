<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 01/04/2018
 * Time: 00:12
 */

namespace AppBundle\Entity\Api;

use AppBundle\Entity\User;
use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("oauth2_clients")
 * @ORM\Entity
 */
class Client extends BaseClient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @var String
     *
     * @ORM\Column(name="app_name", type="string", length=255, nullable=true)
     */
    protected $applicationName;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="clients")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id", nullable=true)
     */
    protected $user;

    function setUser(User $user) {
        $this->user = $user;
    }

    function getApplicationName() {
        return $this->applicationName;
    }

    function setApplicationName($name) {
        $this->applicationName = $name;
    }
}