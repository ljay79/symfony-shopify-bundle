<?php

namespace CodeCloud\Bundle\ShopifyBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class WebhookEvent extends Event
{
    const NAME = 'codecloud_shopify.webhook';

    /**
     * @var string
     */
    private $topic;

    /**
     * @var string
     */
    private $store;

    /**
     * @var string
     */
    private $resource;

    /**
     * @param string $topic
     * @param string $store
     * @param string $resource
     */
    public function __construct(string $topic, string $store, string $resource)
    {
        $this->topic = $topic;
        $this->store = $store;
        $this->resource = $resource;
    }

    /**
     * @return string
     */
    public function getTopic(): string
    {
        return $this->topic;
    }

    /**
     * @return string
     */
    public function getStore(): string
    {
        return $this->store;
    }

    /**
     * @return string
     */
    public function getResource(): string
    {
        return $this->resource;
    }
}
