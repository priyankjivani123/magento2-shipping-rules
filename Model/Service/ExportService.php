<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Service;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\File\Csv;
use PriyankJivani\ShippingRules\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use PriyankJivani\ShippingRules\Model\ResourceModel\ShippingArea\CollectionFactory as AreaCollectionFactory;

class ExportService
{
    private Filesystem $filesystem;
    private Csv $csvWriter;
    private RuleCollectionFactory $ruleCollectionFactory;
    private AreaCollectionFactory $areaCollectionFactory;

    public function __construct(
        Filesystem $filesystem,
        Csv $csvWriter,
        RuleCollectionFactory $ruleCollectionFactory,
        AreaCollectionFactory $areaCollectionFactory
    ) {
        $this->filesystem = $filesystem;
        $this->csvWriter = $csvWriter;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->areaCollectionFactory = $areaCollectionFactory;
    }

    /**
     * Export all rules or specific IDs to CSV string
     *
     * @param int[] $ruleIds
     * @return string
     */
    public function exportRulesToCsv(array $ruleIds = []): string
    {
        $collection = $this->ruleCollectionFactory->create();
        if (!empty($ruleIds)) {
            $collection->addFieldToFilter('rule_id', ['in' => $ruleIds]);
        }

        $headers = [
            'name',
            'description',
            'is_active',
            'priority',
            'stores',
            'customer_groups',
            'carriers',
            'methods',
            'calculation_type',
            'calc_base_rate',
            'calc_per_product',
            'calc_per_weight',
            'calc_percent_product',
            'calc_handling_percent',
            'min_rate_change',
            'max_rate_change',
            'min_total_rate',
            'max_total_rate',
            'custom_carrier_title',
            'custom_method_title',
            'discard_subsequent_rules',
            'from_date',
            'to_date',
            'days_of_week',
            'time_from',
            'time_to',
            'coupon_type',
            'coupon_code',
            'backorder_mode',
            'conditions_serialized'
        ];

        $data = [$headers];

        foreach ($collection as $rule) {
            $stores = is_array($rule->getStoreIds()) ? implode(',', $rule->getStoreIds()) : (string)$rule->getStoreIds();
            $customerGroups = is_array($rule->getCustomerGroupIds()) ? implode(',', $rule->getCustomerGroupIds()) : (string)$rule->getCustomerGroupIds();
            $carriers = is_array($rule->getCarriers()) ? implode(',', $rule->getCarriers()) : (string)$rule->getCarriers();
            $methods = is_array($rule->getMethods()) ? implode(',', $rule->getMethods()) : (string)$rule->getMethods();
            $days = is_array($rule->getDaysOfWeek()) ? implode(',', $rule->getDaysOfWeek()) : (string)$rule->getDaysOfWeek();

            $data[] = [
                $rule->getName(),
                $rule->getDescription() ?? '',
                $rule->getIsActive() ? '1' : '0',
                (int)$rule->getPriority(),
                $stores,
                $customerGroups,
                $carriers,
                $methods,
                $rule->getCalculationType(),
                (float)$rule->getCalcBaseRate(),
                (float)$rule->getCalcPerProduct(),
                (float)$rule->getCalcPerWeight(),
                (float)$rule->getCalcPercentProduct(),
                (float)$rule->getCalcHandlingPercent(),
                $rule->getMinRateChange() !== null ? (float)$rule->getMinRateChange() : '',
                $rule->getMaxRateChange() !== null ? (float)$rule->getMaxRateChange() : '',
                $rule->getMinTotalRate() !== null ? (float)$rule->getMinTotalRate() : '',
                $rule->getMaxTotalRate() !== null ? (float)$rule->getMaxTotalRate() : '',
                $rule->getCustomCarrierTitle() ?? '',
                $rule->getCustomMethodTitle() ?? '',
                $rule->getDiscardSubsequentRules() ? '1' : '0',
                $rule->getFromDate() ?? '',
                $rule->getToDate() ?? '',
                $days,
                $rule->getTimeFrom() ?? '',
                $rule->getTimeTo() ?? '',
                $rule->getCouponType() ?? 'all',
                $rule->getCouponCode() ?? '',
                $rule->getBackorderMode() ?? 'all',
                $rule->getConditionsSerialized() ?? ''
            ];
        }

        $tmpDir = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);
        $tmpFile = 'shipping_rules_export_' . time() . '.csv';
        $fullPath = $tmpDir->getAbsolutePath($tmpFile);

        $this->csvWriter->saveData($fullPath, $data);
        $content = (string)file_get_contents($fullPath);
        @unlink($fullPath);

        return $content;
    }

    /**
     * Export all shipping areas to CSV string
     *
     * @param int[] $areaIds
     * @return string
     */
    public function exportAreasToCsv(array $areaIds = []): string
    {
        $collection = $this->areaCollectionFactory->create();
        if (!empty($areaIds)) {
            $collection->addFieldToFilter('area_id', ['in' => $areaIds]);
        }

        $headers = [
            'name',
            'description',
            'is_active',
            'countries',
            'regions',
            'cities',
            'postcodes',
            'address_lines',
            'address_lines_type'
        ];

        $data = [$headers];

        foreach ($collection as $area) {
            $countries = is_array($area->getCountries()) ? implode(',', $area->getCountries()) : (string)$area->getCountries();
            $regions = is_array($area->getRegions()) ? implode(',', $area->getRegions()) : (string)$area->getRegions();
            $cities = is_array($area->getCities()) ? implode("\n", $area->getCities()) : (string)$area->getCities();
            $postcodes = is_array($area->getPostcodes()) ? implode("\n", $area->getPostcodes()) : (string)$area->getPostcodes();
            $addressLines = is_array($area->getAddressLines()) ? implode("\n", $area->getAddressLines()) : (string)$area->getAddressLines();

            $data[] = [
                $area->getName(),
                $area->getDescription() ?? '',
                $area->getIsActive() ? '1' : '0',
                $countries,
                $regions,
                $cities,
                $postcodes,
                $addressLines,
                $area->getAddressLinesType() ?? 'include'
            ];
        }

        $tmpDir = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);
        $tmpFile = 'shipping_areas_export_' . time() . '.csv';
        $fullPath = $tmpDir->getAbsolutePath($tmpFile);

        $this->csvWriter->saveData($fullPath, $data);
        $content = (string)file_get_contents($fullPath);
        @unlink($fullPath);

        return $content;
    }
}
