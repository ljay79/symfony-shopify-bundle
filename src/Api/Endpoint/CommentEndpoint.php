<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\GetJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PostJson;
use CodeCloud\Bundle\ShopifyBundle\Api\Request\PutJson;
use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;

class CommentEndpoint extends AbstractEndpoint
{
    /**
     * @param array $query
     * @return array|GenericResource[]
     */
    public function findAll(array $query = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/comments.json', $query);
        $response = $this->sendPaged($request, 'comments');
        return $this->createCollection($response);
    }

    /**
     * @param array $query
     * @return int
     */
    public function countAll(array $query = array())
    {
        $request = new GetJson('/admin/api/' . $this->version . '/comments/count.json', $query);
        $response = $this->send($request);
        return $response->get('count');
    }

    /**
     * @param int $commentId
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function findOne($commentId)
    {
        $request = new GetJson('/admin/api/' . $this->version . '/comments/' . $commentId . '.json');
        $response = $this->send($request);
        return $this->createEntity($response->get('comment'));
    }

    /**
     * @param \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource $comment
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function create(GenericResource $comment)
    {
        $request = new PostJson('/admin/api/' . $this->version . '/comments.json', array('comment' => $comment->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('comment'));
    }

    /**
     * @param int $commentId
     * @param \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource $comment
     * @return \CodeCloud\Bundle\ShopifyBundle\Api\GenericResource
     */
    public function update($commentId, GenericResource $comment)
    {
        $request = new PutJson('/admin/api/' . $this->version . '/comments/' . $commentId . '.json', array('comment' => $comment->toArray()));
        $response = $this->send($request);
        return $this->createEntity($response->get('comment'));
    }

    /**
     * @param int $commentId
     */
    public function markAsSpam($commentId)
    {
        $request = new PostJson('/admin/api/' . $this->version . '/comments/' . $commentId . '/spam.json');
        $this->send($request);
    }

    /**
     * @param int $commentId
     */
    public function markAsNotSpam($commentId)
    {
        $request = new PostJson('/admin/api/' . $this->version . '/comments/' . $commentId . '/not_spam.json');
        $this->send($request);
    }

    /**
     * @param int $commentId
     */
    public function approve($commentId)
    {
        $request = new PostJson('/admin/api/' . $this->version . '/comments/' . $commentId . '/approve.json');
        $this->send($request);
    }

    /**
     * @param int $commentId
     */
    public function remove($commentId)
    {
        $request = new PostJson('/admin/api/' . $this->version . '/comments/' . $commentId . '/remove.json');
        $this->send($request);
    }

    /**
     * @param int $commentId
     */
    public function restore($commentId)
    {
        $request = new PostJson('/admin/api/' . $this->version . '/comments/' . $commentId . '/restore.json');
        $this->send($request);
    }
}
