<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Rule;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;
use PriyankJivani\ShippingRules\Model\ResourceModel\Rule\CollectionFactory;
use PriyankJivani\ShippingRules\Api\RuleRepositoryInterface;

class DataProvider extends AbstractDataProvider
{
    private DataPersistorInterface $dataPersistor;
    private RuleRepositoryInterface $ruleRepository;
    private array $loadedData = [];

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        RuleRepositoryInterface $ruleRepository,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->ruleRepository = $ruleRepository;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $rule = $this->ruleRepository->getById((int)$model->getId());
            $data = $rule->getData();

            // Ensure arrays for multi-selects
            $data['store_ids'] = $rule->getStoreIds();
            $data['customer_group_ids'] = $rule->getCustomerGroupIds();
            $data['carriers'] = $rule->getCarriers();
            $data['methods'] = $rule->getMethods();
            $data['days_of_week'] = $rule->getDaysOfWeek();

            $this->loadedData[$rule->getId()] = $data;
        }

        $data = $this->dataPersistor->get('shippingrules_rule');
        if (!empty($data)) {
            $ruleId = $data['rule_id'] ?? null;
            $this->loadedData[$ruleId] = $data;
            $this->dataPersistor->clear('shippingrules_rule');
        }

        return $this->loadedData;
    }
}
