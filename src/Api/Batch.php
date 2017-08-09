<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
namespace OmegaCommerce\Api;

class Batch
{
    protected $action;
    protected $batch = array();
    protected $batchSize = 100;
    protected $entityType;
    protected $additionalData;

    public function __construct(
        Client $client,
        \OmegaCommerce\Api\Indexer\Helper $indexerHelper
    )
    {
        $this->client = $client;
        $this->indexerHelper = $indexerHelper;
    }

    /**
     * @return void
     */
    public function startBatch($action, $entityType, $additionalData = array())
    {
        $this->action = $action;
        $this->entityType = $entityType;
        $this->additionalData = $additionalData;
    }

    /**
     * @param array $data
     * @return void
     */
    public function addEntity(array $data)
    {

        $data = $this->indexerHelper->prepareDataForApi($data);

        $this->batch[] = $data;
        if (count($this->batch) >= $this->batchSize) {
            $this->client->request(Client::METHOD_POST, $this->batchUrl(), array(), $this->batchData());
            $this->batch = array();
        }
    }

    /**
     * @param array $item
     * @return void
     */
    public function addItem($item)
    {
        $this->batch[] = $item;
        if (count($this->batch) >= $this->batchSize) {
            $this->client->request(Client::METHOD_POST, $this->batchUrl(), array(), $this->batchData());
            $this->batch = array();
        }
    }


    /**
     * @return void
     */
    public function finishBatch()
    {
        if (count($this->batch)) {
            $this->client->request(Client::METHOD_POST, $this->batchUrl(), array(), $this->batchData());
        }
        $this->batch = array();
    }


    /**
     * @return string
     */
    protected function batchUrl() {
        return '/entity/'.$this->entityType.'/'.$this->action;
    }
    /**
     * @return array
     */
    protected function batchData() {
        $result =  array(
            "items" => $this->batch
        );
        $result = array_merge($result,$this->additionalData);
        return $result;
    }
}