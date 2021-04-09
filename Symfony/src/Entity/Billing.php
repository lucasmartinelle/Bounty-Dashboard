<?php

namespace App\Entity;

use App\Repository\BillingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BillingRepository::class)
 */
class Billing
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=36, unique=true)
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=14)
     */
    private $SIRET;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $VAT;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $BANK;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $BIC;

    /**
     * @ORM\Column(type="string", length=34)
     */
    private $IBAN;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserId(): ?string
    {
        return $this->user_id;
    }

    public function setUserId(string $user_id): self
    {
        $this->user_id = $user_id;

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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSIRET(): ?string
    {
        return $this->SIRET;
    }

    public function setSIRET(string $SIRET): self
    {
        $this->SIRET = $SIRET;

        return $this;
    }

    public function getVAT(): ?string
    {
        return $this->VAT;
    }

    public function setVAT(string $VAT): self
    {
        $this->VAT = $VAT;

        return $this;
    }

    public function getBANK(): ?string
    {
        return $this->BANK;
    }

    public function setBANK(string $BANK): self
    {
        $this->BANK = $BANK;

        return $this;
    }

    public function getBIC(): ?string
    {
        return $this->BIC;
    }

    public function setBIC(string $BIC): self
    {
        $this->BIC = $BIC;

        return $this;
    }

    public function getIBAN(): ?string
    {
        return $this->IBAN;
    }

    public function setIBAN(string $IBAN): self
    {
        $this->IBAN = $IBAN;

        return $this;
    }
}
