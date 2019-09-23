<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\DeleteParams;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PostJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PutJson;
use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;

class CustomerSavedSearchEndpoint extends AbstractEndpoint
{
    /**
     * @param array $query
     * @param array $links
     * @return array|GenericResource[]
     */
    public function findAll(array $query = array(), array &$links = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/customer_saved_searches.json', $query);
        $response = $this->sendPaged($request, 'customer_saved_searches', $links);
        return $this->createCollection($response);
    }

    /**
     * @param array $query
     * @return int
     */
    public function countAll(array $query = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/customer_saved_searches/count.json', $query);
        $response = $this->send($request);
        return $response->get('count');
    }

    /**
     * @param int $savedSearchId
     * @param array $fields
     * @return GenericResource
     */
    public function findOne($savedSearchId, array $fields = array())
    {
        $params = $fields ? array('fields' => $fields) : array();
        $request = new GetJson('/admin/api/' . $this->version . '/customer_saved_searches/' . $savedSearchId . '.json', $params);
        $response = $this->send($request);
        return $this->createEntity($response->get('customer_saved_search'));
    }

    /**
     * @param int $savedSearchId
     * @param array $links
     * @return array|\CodeCloud\Bundle\ShopifyBundle\Api\GenericResource[]
     */
    public function findCustomersForSavedSearch($savedSearchId, array $query = array(), array &$links = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/customer_saved_searches/' . $savedSearchId . '/customers.json', $query);
        $response = $this->sendPaged($request, 'customers', $links);
        return $this->createCollection($response);
    }

    /**
     * @param GenericResource $savedSearch
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function create(GenericResource $savedSearch)
    {
        $request = new PostJson('/admin/api/' . $this->version . '/customer_saved_searches.json', array('customer_saved_search' => $savedSearch->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('customer_saved_search'));
    }

    /**
     * @param int $savedSearchId
     * @param GenericResource $savedSearch
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function update($savedSearchId, GenericResource $savedSearch)
    {
        $request = new PutJson('/admin/api/' . $this->version . '/customer_saved_searches/' . $savedSearchId . '.json', array('customer_saved_search' => $savedSearch->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('customer_saved_search'));
    }

    /**
     * @param int $savedSearchId
     */
    public function delete($savedSearchId)
    {
        $request = new DeleteParams('/admin/api/' . $this->version . '/customer_saved_searches/' . $savedSearchId . '.json');
        $this->send($request);
    }
}
