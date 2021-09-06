<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StoreRepository")
 * @ORM\Table(name="stores")
 */
class Store
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $nonce;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $accessToken;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $installedAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="uninstalled_at", nullable=true)
     */
    private $unInstalledAt;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->installedAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $nonce
     * @return $this
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;

        return $this;
    }

    /**
     * @param string $accessToken
     * @return $this
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getInstalledAt(): \DateTime
    {
        return $this->installedAt;
    }

    public function markUnInstalled()
    {
        $this->unInstalledAt = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getUnInstalledAt(): ?\DateTime
    {
        return $this->unInstalledAt;
    }
}
