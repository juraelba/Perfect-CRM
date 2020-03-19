<?php

namespace Omnipay\AuthorizeNet\Message\Query;

use Omnipay\AuthorizeNet\Message\AIMAbstractRequest;

/**
 * Authorize.Net AIM Authorize Request
 */
class AIMPaymentPlansQueryRequest extends AIMAbstractRequest
{
    protected $action = '';
    protected $requestType = 'ARBGetSubscriptionListRequest';
    protected $limit = 1000;
    protected $offset = 1;

    /**
     * Get Limit.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Get data to send.
     */
    public function getData()
    {
        $data = $this->getBaseData();
        $data->searchType = 'subscriptionActive';
        $data->sorting->orderBy = 'id';
        $data->sorting->orderDescending = true;
        $data->paging->limit = $this->getLimit();
        $data->paging->offset = $this->getOffset();
        return $data;
    }

    protected function addTransactionType(\SimpleXMLElement $data)
    {
    }

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');
        $data = $data->saveXml();
        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint(), $headers, $data);

        return $this->response = new AIMPaymentPlansQueryResponse($this, $httpResponse->getBody()->getContents());
    }
}
