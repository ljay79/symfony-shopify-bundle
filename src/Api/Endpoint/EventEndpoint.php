<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;

class EventEndpoint extends AbstractEndpoint
{
    /**
     * @param array $query
     * @param array $links
     * @return array|GenericEntity[]
     */
    public function findAll(array $query = array(), array &$links = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/events.json', $query);
        $response = $this->sendPaged($request, 'events', $links);
        return $this->createCollection($response);
    }

    /**
     * @param array $query
     * @return int
     */
    public function countAll(array $query = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/events/count.json', $query);
        $response = $this->send($request);
        return $response->get('count');
    }

    /**
     * @param int $eventId
     * @return GenericEntity
     */
    public function findOne($eventId)
    {
        $request = new GetJson('/admin/api/' . $this->version . '/events/' . $eventId . '.json');
        $response = $this->send($request);
        return $this->createEntity($response->get('event'));
    }
}
