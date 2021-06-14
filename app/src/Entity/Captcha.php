<?php

namespace App\Entity;

use App\Repository\CaptchaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CaptchaRepository::class)
 */
class Captcha
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $private_key;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $public_key;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrivateKey(): ?string
    {
        return $this->private_key;
    }

    public function setPrivateKey(string $private_key): self
    {
        $this->private_key = $private_key;

        return $this;
    }

    public function getPublicKey(): ?string
    {
        return $this->public_key;
    }

    public function setPublicKey(string $public_key): self
    {
        $this->public_key = $public_key;

        return $this;
    }
}
