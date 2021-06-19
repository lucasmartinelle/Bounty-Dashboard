<?php

namespace App\Entity;

use App\Repository\ProgramsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass=ProgramsRepository::class)
 */
class Programs
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $creator_id;

    /**
     * @ORM\Column(type="string", length=200, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $scope;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $tags;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $platform_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->date->format('d-m-Y');
        $this->created_at = new \DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCreatorId(): ?string
    {
        return $this->creator_id;
    }

    public function setCreatorId(string $creator_id): self
    {
        $this->creator_id = $creator_id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getPlatformId(): ?string
    {
        return $this->platform_id;
    }

    public function setPlatformId(string $platform_id): self
    {
        $this->platform_id = $platform_id;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt(): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'name',
            'errorPath' => 'name',
            'message' => 'This name is already in use.',
        ]));
    }
}
