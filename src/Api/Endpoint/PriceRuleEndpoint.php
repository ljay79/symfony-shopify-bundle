<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\DeleteParams;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PostJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PutJson;
use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;

class PriceRuleEndpoint extends AbstractEndpoint
{
    /**
     * @param array $query
     * @return array|\CodeCloud\Bundle\ShopifyBundle\Api\GenericResource[]
     */
    public function findAll(array $query = array())
    {
        $request = new GetJson('/admin/price_rules.json', $query);
        $response = $this->sendPaged($request, 'price_rules');
        return $this->createCollection($response);
    }

    /**
     * @param int $priceRuleId
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function findOne($priceRuleId)
    {
        $request = new GetJson('/admin/price_rules/' . $priceRuleId . '.json');
        $response = $this->send($request);
        return $this->createEntity($response->get('price_rule'));
    }

    /**
     * @return int
     */
    public function countAll()
    {
        $request = new GetJson('/admin/price_rules/count.json');
        $response = $this->send($request);
        return $response->get('count');
    }

    /**
     * @param \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource $priceRule
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function create(GenericResource $priceRule)
    {
        $request = new PostJson('/admin/price_rules.json', array('price_rule' => $priceRule->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('price_rule'));
    }

    /**
     * @param int $priceRuleId
     * @param \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource $priceRule
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function update($priceRuleId, GenericResource $priceRule)
    {
        $request = new PutJson('/admin/price_rules/' . $priceRuleId . '.json', array('price_rule' => $priceRule->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('price_rule'));
    }

    /**
     * @param int $priceRuleId
     */
    public function delete($priceRuleId)
    {
        $request = new DeleteParams('/admin/price_rules/' . $priceRuleId . '.json');
        $this->send($request);
    }
}
