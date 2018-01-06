<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 25/12/2017
 * Time: 02:12
 */

namespace AppBundle\Service;


use AppBundle\Entity\Author;
use AppBundle\Repository\AuthorRepository;
use Doctrine\ORM\EntityManager;

class AuthorService
{
    private $repository;
    private $entityManager;

    // image uploaders
    private $avatarFileUploader;
    private $signatureFileUploader;

    public function __construct(AuthorRepository $repository, EntityManager $entityManager, AvatarFileUploader $avatarFileUploader, SignatureFileUploader $signatureFileUploader)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->avatarFileUploader = $avatarFileUploader;
        $this->signatureFileUploader = $signatureFileUploader;
    }

    public function add(Author $author)
    {
        $avatarFile    = $author->getAvatar();
        $signatureFile = $author->getSignature();

        $avatarFileName = $this->avatarFileUploader->upload($avatarFile);
        $signatureFileName = $this->signatureFileUploader->upload($signatureFile);

        $author->setAvatar($avatarFileName);
        $author->setSignature($signatureFileName);

        $this->entityManager->persist($author);
        $this->entityManager->flush();
    }

    public function searchName(string $query)
    {
        return $this->repository->searchName($query);
    }
}