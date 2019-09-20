<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\DeleteParams;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PostJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PutJson;
use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;

class DiscountCodeEndpoint extends AbstractEndpoint
{
    /**
     * @param int $priceRuleId
     * @return array|\CodeCloud\Bundle\ShopifyBundle\Api\GenericResource[]
     */
    public function findAll($priceRuleId)
    {
        $request = new GetJson('/admin/api/' . $this->version . '/price_rules/' . $priceRuleId . '.json');
        $response = $this->sendPaged($request, 'discount_codes');
        return $this->createCollection($response);
    }

    /**
     * @param int $priceRuleId
     * @param int $discountCodeId
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function findOne($priceRuleId, $discountCodeId)
    {
        $request = new GetJson('/admin/api/' . $this->version . '/price_rules/' . $priceRuleId . '/discount_codes/' . $discountCodeId . '.json');
        $response = $this->send($request);
        return $this->createEntity($response->get('price_rule'));
    }

    /**
     * @param string $code
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function lookup($code)
    {
        $request = new GetJson('/admin/api/' . $this->version . '/discount_codes/lookup.json', array('code' => $code));
        $response = $this->send($request);
        return $response->getHttpResponse()->getHeader('Location'); // @todo
    }

    /**
     * @param int $priceRuleId
     * @param \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource $discountCode
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function create($priceRuleId, GenericResource $discountCode)
    {
        $request = new PostJson('/admin/api/' . $this->version . '/price_rules/' . $priceRuleId . '/discount_codes.json', array('discount_code' => $discountCode->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('discount_code'));
    }

    /**
     * @param int $priceRuleId
     * @param int $discountCodeId
     * @param \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource $discountCode
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function update($priceRuleId, $discountCodeId, GenericResource $discountCode)
    {
        $request = new PutJson('/admin/api/' . $this->version . '/price_rules/' . $priceRuleId . '/discount_codes/' . $discountCodeId . '.json', array('discount_code' => $discountCode->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('discount_code'));
    }

    /**
     * @param int $priceRuleId
     * @param int $discountCodeId
     */
    public function delete($priceRuleId, $discountCodeId)
    {
        $request = new DeleteParams('/admin/api/' . $this->version . '/price_rules/' . $priceRuleId . '/discount_codes/' . $discountCodeId . '.json');
        $this->send($request);
    }

    // @todo: discount code creation job
}
