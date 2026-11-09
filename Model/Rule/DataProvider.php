<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Rule;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;
use PriyankJivani\ShippingRules\Model\ResourceModel\Rule\CollectionFactory;
use PriyankJivani\ShippingRules\Api\RuleRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as CustomerGroupCollectionFactory;

class DataProvider extends AbstractDataProvider
{
    private DataPersistorInterface $dataPersistor;
    private RuleRepositoryInterface $ruleRepository;
    private CustomerGroupCollectionFactory $groupCollectionFactory;
    private array $loadedData = [];

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        RuleRepositoryInterface $ruleRepository,
        CustomerGroupCollectionFactory $groupCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->ruleRepository = $ruleRepository;
        $this->groupCollectionFactory = $groupCollectionFactory;
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

            // Ensure string arrays for multi-selects so UI components strictly match option values
            $storeIds = $rule->getStoreIds();
            $data['store_ids'] = !empty($storeIds) ? array_map('strval', $storeIds) : ['0'];

            $groupIds = $rule->getCustomerGroupIds();
            $data['customer_group_ids'] = !empty($groupIds) ? array_map('strval', $groupIds) : ['0', '1', '2', '3'];

            $data['carriers'] = !empty($rule->getCarriers()) ? explode(',', (string)$rule->getCarriers()) : [];
            $data['methods'] = !empty($rule->getMethods()) ? explode(',', (string)$rule->getMethods()) : [];
            $data['days_of_week'] = !empty($rule->getDaysOfWeek()) ? explode(',', (string)$rule->getDaysOfWeek()) : [];

            $this->loadedData[$rule->getId()] = $data;
        }

        // Fetch all customer group IDs for default selection
        $allCustomerGroups = [];
        try {
            foreach ($this->groupCollectionFactory->create() as $group) {
                $allCustomerGroups[] = (string)$group->getId();
            }
        } catch (\Throwable $e) {
            $allCustomerGroups = ['0', '1', '2', '3'];
        }
        if (!in_array('0', $allCustomerGroups, true)) {
            array_unshift($allCustomerGroups, '0');
        }

        $defaultData = [
            'rule_id' => null,
            'is_active' => '1',
            'priority' => '0',
            'calculation_type' => 'replace',
            'store_ids' => ['0'],
            'customer_group_ids' => $allCustomerGroups,
            'carriers' => [],
            'methods' => [],
            'days_of_week' => [],
            'calc_base_rate' => '0.0000',
            'calc_per_product' => '0.0000',
            'calc_per_weight' => '0.0000',
            'calc_percent_product' => '0.0000',
            'calc_handling_percent' => '0.0000',
            'min_rate_change' => '',
            'max_rate_change' => '',
            'min_total_rate' => '',
            'max_total_rate' => '',
            'apply_in_admin' => '1',
            'discard_subsequent_rules' => '0',
            'coupon_type' => '0',
            'backorder_mode' => '0'
        ];

        $this->loadedData[''] = $defaultData;
        $this->loadedData[0] = $defaultData;

        $data = $this->dataPersistor->get('shippingrules_rule');
        if (!empty($data)) {
            $ruleId = $data['rule_id'] ?? '';
            $this->loadedData[$ruleId] = array_merge($defaultData, $data);
            $this->dataPersistor->clear('shippingrules_rule');
        }

        return $this->loadedData;
    }
}
