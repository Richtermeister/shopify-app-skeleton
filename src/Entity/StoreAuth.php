<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="store_auths")
 */
class StoreAuth
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var Store
     * @ORM\ManyToOne(targetEntity="App\Entity\Store")
     * @ORM\JoinColumn(nullable=false)
     */
    private $store;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $sessionId;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $authenticatedAt;

    /**
     * @param Store $store
     * @param string $sessionId
     */
    public function __construct(Store $store, string $sessionId)
    {
        $this->store = $store;
        $this->sessionId = $sessionId;
        $this->authenticatedAt = new \DateTimeImmutable();
    }

    /**
     * @return Store
     */
    public function getStore(): Store
    {
        return $this->store;
    }

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getAuthenticatedAt(): \DateTimeImmutable
    {
        return $this->authenticatedAt;
    }
}
