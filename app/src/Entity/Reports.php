<?php

namespace App\Entity;

use App\Repository\ReportsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass=ReportsRepository::class)
 */
class Reports
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
     * @ORM\Column(type="string", length=200)
     */
    private $title;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $severity;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $endpoint;

    /**
     * @ORM\Column(type="string", length=200, unique=true)
     */
    private $identifiant;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $status = 'New';

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $gain = 0;

    /**
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $template_id = NULL;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $program_id;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $stepsToReproduce;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $impact;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $mitigation;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $ressources;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function __construct()
    {
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSeverity(): ?float
    {
        return $this->severity;
    }

    public function setSeverity(?float $severity): self
    {
        $this->severity = $severity;

        return $this;
    }

    public function getDate()
    {
        if($this->date){
            return $this->date->format('d-m-Y');
        }
        return null;
    }

    public function setDate($date): self
    {
        if(gettype($date) == "object"){
            $this->date = $date;
        } else {
            if(\preg_match("(([0][1-9]|[1-2][0-9]|[3][0-1])-([0][1-9]|[1][0-2])-([2][0][0-9]{2}))", $date)){
                $this->date = new \DateTime($date);
            }
        }

        return $this;
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    public function setEndpoint(?string $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant(?string $identifiant): self
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getGain(): ?int
    {
        return $this->gain;
    }

    public function setGain(?int $gain): self
    {
        $this->gain = $gain;

        return $this;
    }

    public function getTemplateId(): ?string
    {
        return $this->template_id;
    }

    public function setTemplateId(?string $template_id): self
    {
        $this->template_id = $template_id;

        return $this;
    }

    public function getProgramId(): ?string
    {
        return $this->program_id;
    }

    public function setProgramId(?string $program_id): self
    {
        $this->program_id = $program_id;

        return $this;
    }

    public function getStepsToReproduce()
    {
        return $this->stepsToReproduce;
    }

    public function setStepsToReproduce($stepsToReproduce): self
    {
        $this->stepsToReproduce = $stepsToReproduce;

        return $this;
    }

    public function getImpact()
    {
        return $this->impact;
    }

    public function setImpact($impact): self
    {
        $this->impact = $impact;

        return $this;
    }

    public function getMitigation()
    {
        return $this->mitigation;
    }

    public function setMitigation($mitigation): self
    {
        $this->mitigation = $mitigation;

        return $this;
    }

    public function getRessources()
    {
        return $this->ressources;
    }

    public function setRessources($ressources): self
    {
        $this->ressources = $ressources;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'identifiant',
            'errorPath' => 'identifiant',
            'message' => 'This identifier is already in use.',
        ]));
    }
}
