<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\DeleteParams;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PostJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PutJson;
use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;

class ProductEndpoint extends AbstractEndpoint
{
    /**
     * @param array $query
     * @return array|\CodeCloud\Bundle\ShopifyBundle\Api\GenericResource[]
     */
    public function findAll(array $query = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/products.json', $query);
        $response = $this->sendPaged($request, 'products');
        return $this->createCollection($response);
    }

    /**
     * @param array $query
     * @return int
     */
    public function countAll(array $query = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/products/count.json', $query);
        $response = $this->send($request);
        return $response->get('count');
    }

    /**
     * @param int $productId
     * @param array $fields
     * @return GenericResource
     */
    public function findOne($productId, array $fields = array())
    {
        $params = $fields ? array('fields' => implode(',', $fields)) : array();
        $request = new GetJson('/admin/api/' . $this->version . '/products/' . $productId . '.json', $params);
        $response = $this->send($request);
        return $this->createEntity($response->get('product'));
    }

    /**
     * @param \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource $product
     * @return GenericResource
     */
    public function create(GenericResource $product)
    {
        $request = new PostJson('/admin/api/' . $this->version . '/products.json', array('product' => $product->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('product'));
    }

    /**
     * @param int $productId
     * @param GenericResource $product
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function update($productId, GenericResource $product)
    {
        $request = new PutJson('/admin/api/' . $this->version . '/products/' . $productId . '.json', array('product' => $product->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('product'));
    }

    /**
     * @param int $productId
     */
    public function delete($productId)
    {
        $request = new DeleteParams('/admin/api/' . $this->version . '/products/' . $productId . '.json');
        $this->send($request);
    }
}
