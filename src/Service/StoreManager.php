<?php

namespace App\Service;

use App\Entity\Store;
use App\Entity\StoreAuth;
use App\Repository\StoreRepository;
use CodeCloud\Bundle\ShopifyBundle\Api\CredentialsResolverInterface;
use CodeCloud\Bundle\ShopifyBundle\Api\PrivateAppCredentials;
use CodeCloud\Bundle\ShopifyBundle\Api\PublicAppCredentials;
use CodeCloud\Bundle\ShopifyBundle\Exception\StoreNotAuthenticatedException;
use CodeCloud\Bundle\ShopifyBundle\Exception\StoreNotFoundException;
use CodeCloud\Bundle\ShopifyBundle\Model\Session;
use CodeCloud\Bundle\ShopifyBundle\Model\ShopifyStoreManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

class StoreManager implements ShopifyStoreManagerInterface, CredentialsResolverInterface
{
    /**
     * @var StoreRepository
     */
    private $stores;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param StoreRepository $stores
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(StoreRepository $stores, EntityManagerInterface $entityManager)
    {
        $this->stores = $stores;
        $this->entityManager = $entityManager;
    }

    public function getAccessToken($storeName): string
    {
        $store = $this->findStore($storeName);

        if (!$store) {
            throw new StoreNotFoundException($storeName);
        }

        if (!$store->getAccessToken()) {
            throw new StoreNotAuthenticatedException($storeName);
        }

        return $store->getAccessToken();
    }

    public function storeExists($storeName): bool
    {
        return (bool) $this->findStore($storeName);
    }

    public function preAuthenticateStore($storeName, $nonce)
    {
        if (!$store = $this->findStore($storeName)) {
            $store = new Store($storeName);
            $this->entityManager->persist($store);
        }

        $store->setNonce($nonce);

        $this->entityManager->flush();
    }

    public function authenticateStore($storeName, $accessToken, $nonce)
    {
        /** @var Store $store */
        $store = $this->stores->findOneBy([
            'name' => $storeName,
            'nonce' => $nonce,
        ]);

        if (!$store) {
            throw new \InvalidArgumentException(sprintf(
                'Could not find store with name "%s" and nonce "%s".',
                $storeName,
                $nonce
            ));
        }

        $store->setAccessToken($accessToken);

        $this->entityManager->flush();
    }

    public function getCredentials($storeName)
    {
        $store = $this->findStore($storeName);

        if (!$store) {
            throw new StoreNotFoundException($storeName);
        }

        if (!$accessToken = $store->getAccessToken()) {
            throw new StoreNotAuthenticatedException($storeName);
        }

        if (false === strpos($accessToken, ':')) {
            return new PublicAppCredentials($accessToken);
        }

        $bits = explode(':', $accessToken);

        return new PrivateAppCredentials($bits[0], $bits[1]);
    }

    private function findStore(string $storeName): ?Store
    {
        return $this->stores->findOneBy(['name' => $storeName]);
    }

    public function authenticateSession(Session $session)
    {
        $store = $this->findStore($session->storeName);
        $auth = new StoreAuth($store, $session->sessionId);

        try {
            $this->entityManager->persist($auth);
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            // do nothing
        }
    }

    public function findStoreNameBySession(string $sessionId): ?string
    {
        /** @var StoreAuth $auth */
        $auth = $this->entityManager->getRepository(StoreAuth::class)
            ->findOneBy([
                'sessionId' => $sessionId,
            ]);

        if (!$auth) {
            return null;
        }

        return $auth->getStore()->getName();
    }
}
