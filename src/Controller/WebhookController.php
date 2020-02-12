<?php

namespace CodeCloud\Bundle\ShopifyBundle\Controller;

use CodeCloud\Bundle\ShopifyBundle\Event\WebhookEvent;
use CodeCloud\Bundle\ShopifyBundle\Model\ShopifyStoreManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WebhookController
{
    /**
     * @var ShopifyStoreManagerInterface
     */
    private $storeManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param ShopifyStoreManagerInterface $storeManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ShopifyStoreManagerInterface $storeManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->storeManager = $storeManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handleWebhook(Request $request)
    {
        $topic     = $request->query->get('topic') ?: $request->headers->get('x-shopify-topic');
        $storeName = $request->query->get('store') ?: $request->headers->get('x-shopify-shop-domain');
        // $hmac      = $request->headers->get('x-shopify-hmac-sha256');

        if (!$topic || !$storeName) {
            throw new NotFoundHttpException('Request is missing required parameters: "topic", "store" or headers: "x-shopify-topic", "x-shopify-shop-domain"');
        }

        if (!$this->storeManager->storeExists($storeName)) {
            throw new NotFoundHttpException('Store does not exist');
        }

        if (empty($request->getContent())) {
            // todo log!
            throw new BadRequestHttpException('Webhook must have body content');
        }

        $payload = \GuzzleHttp\json_decode($request->getContent(), true);

        $this->eventDispatcher->dispatch(WebhookEvent::NAME, new WebhookEvent(
            $topic,
            $storeName,
            $payload
        ));

        return new Response('Shopify Webhook Received');
    }
}
