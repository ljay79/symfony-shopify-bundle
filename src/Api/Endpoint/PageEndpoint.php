<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\DeleteParams;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PostJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PutJson;
use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;

class PageEndpoint extends AbstractEndpoint
{
    /**
     * @param array $query
     * @param array $links
     * @return array|GenericResource
     */
    public function findAll(array $query = array(), array &$links = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/pages.json', $query);
        $response = $this->sendPaged($request, 'pages', $links);
        return $this->createCollection($response->get('pages'));
    }

    /**
     * @param array $query
     * @return array
     */
    public function countAll(array $query = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/pages.json', $query);
        $response = $this->send($request);
        return $response->get('count');
    }

    /**
     * @param int $pageId
     * @param array $fields
     * @return GenericResource
     */
    public function findOne($pageId, array $fields = array())
    {
        $params = $fields ? array('fields' => implode(',', $fields)) : array();
        $request = new GetJson('/admin/api/' . $this->version . '/pages/' . $pageId . '.json', $params);
        $response = $this->send($request);
        return $this->createEntity($response->get('page'));
    }

    /**
     * @param GenericResource $page
     * @return GenericResource
     */
    public function create(GenericResource $page)
    {
        $request = new PostJson('/admin/api/' . $this->version . '/pages.json', array('page' => $page->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('page'));
    }

    /**
     * @param int $pageId
     * @param GenericResource $page
     * @return GenericResource
     */
    public function update($pageId, GenericResource $page)
    {
        $request = new PutJson('/admin/api/' . $this->version . '/pages/' . $pageId. '.json', array('page' => $page->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('page'));
    }

    /**
     * @param int $pageId
     */
    public function delete($pageId)
    {
        $request = new DeleteParams('/admin/api/' . $this->version . '/pages/' . $pageId . '.json');
        $this->send($request);
    }
}
