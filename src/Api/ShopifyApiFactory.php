<?php

namespace CodeCloud\Bundle\ShopifyBundle\Api;

use CodeCloud\Bundle\ShopifyBundle\Model\ShopifyStoreManagerInterface;

/**
 * Creates ShopifyApi instances.
 */
class ShopifyApiFactory
{
    /**
     * @var ShopifyStoreManagerInterface
     */
    private $storeManager;

    /**
     * @var HttpClientFactoryInterface
     */
    private $httpClientFactory;

    /**
     * @var string
     */
    private $version;

    /**
     * @param ShopifyStoreManagerInterface $storeManager
     * @param HttpClientFactoryInterface $httpClientFactory
     * @param string $version
     */
    public function __construct(
        ShopifyStoreManagerInterface $storeManager,
        HttpClientFactoryInterface $httpClientFactory,
        string $version
    ) {
        $this->storeManager = $storeManager;
        $this->httpClientFactory = $httpClientFactory;
        $this->version = $version;
    }

    /**
     * @param string $storeName
     * @return ShopifyApi
     */
    public function getForStore($storeName)
    {
        $accessToken = $this->storeManager->getAccessToken($storeName);
        $client = $this->httpClientFactory->createHttpClient($storeName, new PublicAppCredentials($accessToken));

        return new ShopifyApi($client, $this->version);
    }
}
