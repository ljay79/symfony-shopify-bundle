<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\DeleteParams;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PostJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PutJson;
use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;

class ProductVariantEndpoint extends AbstractEndpoint
{
    /**
     * @param int $productId
     * @param array $query
     * @param array $links
     * @return array|GenericResource[]
     */
    public function findByProduct($productId, array $query = array(), array &$links = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/products/' . $productId . '/variants.json', $query);
        $response = $this->sendPaged($request, 'variants', $links);
        return $this->createCollection($response);
    }

    /**
     * @param int $productId
     * @return int
     */
    public function countByProduct($productId)
    {
        $request = new GetJson('/admin/api/' . $this->version . '/products/' . $productId . '/variants/count.json');
        $response = $this->send($request);
        return $response->get('count');
    }

    /**
     * @param int $variantId
     * @return GenericResource
     */
    public function findOne($variantId)
    {
        $request = new GetJson('/admin/api/' . $this->version . '/variants/' . $variantId . '.json');
        $response = $this->send($request);
        return $this->createEntity($response->get('variant'));
    }

    /**
     * @param int $productId
     * @param \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource $variant
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function create($productId, GenericResource $variant)
    {
        $request = new PostJson('/admin/api/' . $this->version . '/products/' . $productId . '/variants.json', array('variant' => $variant->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('variant'));
    }

    /**
     * @param int $variantId
     * @param \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource $variant
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function update($variantId, GenericResource $variant)
    {
        $request = new PutJson('/admin/api/' . $this->version . '/variants/' . $variantId . '.json', array('variant' => $variant->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('variant'));
    }

    /**
     * @param int $variantId
     */
    public function delete($variantId)
    {
        $request = new DeleteParams('/admin/api/' . $this->version . '/variants/' . $variantId . '.json');
        $this->send($request);
    }
}
