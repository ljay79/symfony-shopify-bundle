<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\DeleteParams;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PostJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PutJson;
use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;

class DraftOrderEndpoint extends AbstractEndpoint
{
    /**
     * @param array $query
     * @return array|\CodeCloud\Bundle\ShopifyBundle\Api\GenericResource[]
     */
    public function findAll(array $query = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/draft_orders.json', $query);
        $response = $this->sendPaged($request, 'draft_orders');
        return $this->createCollection($response);
    }

    /**
     * @param int $orderId
     * @param array $fields
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function findOne($orderId, array $fields = array())
    {
        $params = $fields ? array('fields' => implode(',', $fields)) : array();
        $request = new GetJson('/admin/api/' . $this->version . '/draft_orders/' . $orderId . '.json', $params);
        $response = $this->send($request);
        return $this->createEntity($response->get('draft_order'));
    }

    /**
     * @param array $query
     * @return int
     */
    public function countAll(array $query = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/draft_orders/count.json', $query);
        $response = $this->send($request);
        return $response->get('count');
    }

    /**
     * @param \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource $order
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function create(GenericResource $order)
    {
        $request = new PostJson('/admin/api/' . $this->version . '/draft_orders.json', array('draft_order' => $order->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('draft_order'));
    }

    /**
     * @param int $orderId
     * @param \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource $order
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function update($orderId, GenericResource $order)
    {
        $request = new PutJson('/admin/api/' . $this->version . '/draft_orders/' . $orderId . '.json', array('draft_order' => $order->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('draft_order'));
    }

    /**
     * @param int $orderId
     */
    public function delete($orderId)
    {
        $request = new DeleteParams('/admin/api/' . $this->version . '/draft_orders/' . $orderId . '.json');
        $this->send($request);
    }

    /**
     * @param int $orderId
     */
    public function sendInvoice($orderId)
    {
        $request = new PostJson('/admin/api/' . $this->version . '/draft_orders/' . $orderId . '/send_invoice.json');
        $this->send($request);
    }

    /**
     * @param int $orderId
     * @param bool $paymentPending
     */
    public function complete($orderId, bool $paymentPending = false)
    {
        $request = new PutJson('/admin/api/' . $this->version . '/draft_orders/' . $orderId . '/complete.json', array('payment_pending' => $paymentPending ? 'true' : 'false'));
        $this->send($request);
    }
}
