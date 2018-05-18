<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use JMS\Serializer\Annotation\ Exclude;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Author
 *
 * @ORM\Table(name="authors")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AuthorRepository")
 * @Serializer\AccessorOrder("custom", custom = {"id", "name", "biography", "avatar"})
 * @Hateoas\Relation("self", href = "expr('/api/v1/authors/' ~ object.getId())")
 * @Vich\Uploadable
 */
class Author
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Groups({"books"})
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     * @Exclude
     */
    private $avatar;

    /**
     * @Vich\UploadableField(mapping="author_avatar", fileNameProperty="avatar")
     *
     * @var File
     * @Exclude
     */
    private $avatarFile;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Book", mappedBy="authors")
     * @Exclude
     */
    private $books;

    /**
     * @var string
     *
     * @ORM\Column(name="signature", type="string", length=255)
     * @Exclude
     */
    private $signature;

    /**
     * @Vich\UploadableField(mapping="author_signature", fileNameProperty="signature")
     *
     * @var File
     * @Exclude
     */
    private $signatureFile;

    /**
     * @var string|null
     *
     * @Groups({"authors"})
     *
     * @ORM\Column(name="biography", type="text", nullable=true)
     */
    private $biography;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Author
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set avatar.
     *
     * @param string $avatar
     *
     * @return Author
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar.
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->books = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add book.
     *
     * @param \AppBundle\Entity\Book $book
     *
     * @return Author
     */
    public function addBook(\AppBundle\Entity\Book $book)
    {
        $this->books[] = $book;

        return $this;
    }

    /**
     * Remove book.
     *
     * @param \AppBundle\Entity\Book $book
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBook(\AppBundle\Entity\Book $book)
    {
        return $this->books->removeElement($book);
    }

    /**
     * Get books.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * Set signature.
     *
     * @param string $signature
     *
     * @return Author
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Get signature.
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Set biography.
     *
     * @param string|null $biography
     *
     * @return Author
     */
    public function setBiography($biography = null)
    {
        $this->biography = $biography;

        return $this;
    }

    /**
     * Get biography.
     *
     * @return string|null
     */
    public function getBiography()
    {
        return $this->biography;
    }

    public function getAvatarFile() {
        return $this->avatarFile;
    }

    public function getSignatureFile()
    {
        return $this->signatureFile;
    }

    public function setSignatureFile(File $image = null)
    {
        $this->signatureFile = $image;
    }

    public function setAvatarFile(File $image = null)
    {
        $this->avatarFile = $image;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("avatar")
     */
    public function getBookCoverLocation() {
        return 'http://localhost:8003/uploads/authors/avatars/' . $this->getAvatar();
    }
}
