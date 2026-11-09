<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Service;

use Magento\Framework\File\Csv;
use PriyankJivani\ShippingRules\Api\RuleRepositoryInterface;
use PriyankJivani\ShippingRules\Api\Data\RuleInterfaceFactory;
use PriyankJivani\ShippingRules\Api\ShippingAreaRepositoryInterface;
use PriyankJivani\ShippingRules\Api\Data\ShippingAreaInterfaceFactory;
use Psr\Log\LoggerInterface;

class ImportService
{
    private Csv $csvReader;
    private RuleRepositoryInterface $ruleRepository;
    private RuleInterfaceFactory $ruleFactory;
    private ShippingAreaRepositoryInterface $areaRepository;
    private ShippingAreaInterfaceFactory $areaFactory;
    private LoggerInterface $logger;

    public function __construct(
        Csv $csvReader,
        RuleRepositoryInterface $ruleRepository,
        RuleInterfaceFactory $ruleFactory,
        ShippingAreaRepositoryInterface $areaRepository,
        ShippingAreaInterfaceFactory $areaFactory,
        LoggerInterface $logger
    ) {
        $this->csvReader = $csvReader;
        $this->ruleRepository = $ruleRepository;
        $this->ruleFactory = $ruleFactory;
        $this->areaRepository = $areaRepository;
        $this->areaFactory = $areaFactory;
        $this->logger = $logger;
    }

    /**
     * Import rules from CSV file path
     *
     * @param string $filePath
     * @return array{success_count: int, error_count: int, errors: array<string>}
     */
    public function importRulesFromCsv(string $filePath): array
    {
        $rows = $this->csvReader->getData($filePath);
        if (empty($rows)) {
            return ['success_count' => 0, 'error_count' => 1, 'errors' => ['CSV file is empty.']];
        }

        $headers = array_shift($rows);
        $headerMap = array_flip($headers);

        $successCount = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $lineNum = $index + 2;
            try {
                $name = $row[$headerMap['name'] ?? 0] ?? '';
                if (empty(trim($name))) {
                    $errors[] = "Row $lineNum: Name is required.";
                    continue;
                }

                $rule = $this->ruleFactory->create();
                $rule->setName(trim($name));
                $rule->setDescription($row[$headerMap['description'] ?? -1] ?? '');
                $rule->setIsActive((bool)(int)($row[$headerMap['is_active'] ?? -1] ?? 1));
                $rule->setPriority((int)($row[$headerMap['priority'] ?? -1] ?? 0));

                $stores = !empty($row[$headerMap['stores'] ?? -1]) ? explode(',', $row[$headerMap['stores']]) : [0];
                $rule->setStoreIds(array_map('intval', $stores));

                $groups = !empty($row[$headerMap['customer_groups'] ?? -1]) ? explode(',', $row[$headerMap['customer_groups']]) : [0, 1, 2, 3];
                $rule->setCustomerGroupIds(array_map('intval', $groups));

                $carriers = !empty($row[$headerMap['carriers'] ?? -1]) ? explode(',', $row[$headerMap['carriers']]) : [];
                $rule->setCarriers($carriers);

                $methods = !empty($row[$headerMap['methods'] ?? -1]) ? explode(',', $row[$headerMap['methods']]) : [];
                $rule->setMethods($methods);

                $calcType = $row[$headerMap['calculation_type'] ?? -1] ?? 'replace';
                $rule->setCalculationType($calcType);

                $rule->setCalcBaseRate((float)($row[$headerMap['calc_base_rate'] ?? -1] ?? 0));
                $rule->setCalcPerProduct((float)($row[$headerMap['calc_per_product'] ?? -1] ?? 0));
                $rule->setCalcPerWeight((float)($row[$headerMap['calc_per_weight'] ?? -1] ?? 0));
                $rule->setCalcPercentProduct((float)($row[$headerMap['calc_percent_product'] ?? -1] ?? 0));
                $rule->setCalcHandlingPercent((float)($row[$headerMap['calc_handling_percent'] ?? -1] ?? 0));

                if (isset($headerMap['min_rate_change']) && $row[$headerMap['min_rate_change']] !== '') {
                    $rule->setMinRateChange((float)$row[$headerMap['min_rate_change']]);
                }
                if (isset($headerMap['max_rate_change']) && $row[$headerMap['max_rate_change']] !== '') {
                    $rule->setMaxRateChange((float)$row[$headerMap['max_rate_change']]);
                }
                if (isset($headerMap['min_total_rate']) && $row[$headerMap['min_total_rate']] !== '') {
                    $rule->setMinTotalRate((float)$row[$headerMap['min_total_rate']]);
                }
                if (isset($headerMap['max_total_rate']) && $row[$headerMap['max_total_rate']] !== '') {
                    $rule->setMaxTotalRate((float)$row[$headerMap['max_total_rate']]);
                }

                $rule->setCustomCarrierTitle($row[$headerMap['custom_carrier_title'] ?? -1] ?? null);
                $rule->setCustomMethodTitle($row[$headerMap['custom_method_title'] ?? -1] ?? null);
                $rule->setDiscardSubsequentRules((bool)(int)($row[$headerMap['discard_subsequent_rules'] ?? -1] ?? 0));

                $rule->setFromDate(!empty($row[$headerMap['from_date'] ?? -1]) ? $row[$headerMap['from_date']] : null);
                $rule->setToDate(!empty($row[$headerMap['to_date'] ?? -1]) ? $row[$headerMap['to_date']] : null);

                $days = !empty($row[$headerMap['days_of_week'] ?? -1]) ? explode(',', $row[$headerMap['days_of_week']]) : [];
                $rule->setDaysOfWeek($days);

                $rule->setTimeFrom(!empty($row[$headerMap['time_from'] ?? -1]) ? $row[$headerMap['time_from']] : null);
                $rule->setTimeTo(!empty($row[$headerMap['time_to'] ?? -1]) ? $row[$headerMap['time_to']] : null);
                $rule->setCouponType($row[$headerMap['coupon_type'] ?? -1] ?? 'all');
                $rule->setCouponCode(!empty($row[$headerMap['coupon_code'] ?? -1]) ? $row[$headerMap['coupon_code']] : null);
                $rule->setBackorderMode($row[$headerMap['backorder_mode'] ?? -1] ?? 'all');

                if (!empty($row[$headerMap['conditions_serialized'] ?? -1])) {
                    $rule->setConditionsSerialized($row[$headerMap['conditions_serialized']]);
                }

                $this->ruleRepository->save($rule);
                $successCount++;
            } catch (\Throwable $e) {
                $errors[] = "Row $lineNum: " . $e->getMessage();
                $this->logger->error("ShippingRules import error on row $lineNum: " . $e->getMessage());
            }
        }

        return [
            'success_count' => $successCount,
            'error_count' => count($errors),
            'errors' => $errors
        ];
    }

    /**
     * Import shipping areas from CSV file path
     *
     * @param string $filePath
     * @return array{success_count: int, error_count: int, errors: array<string>}
     */
    public function importAreasFromCsv(string $filePath): array
    {
        $rows = $this->csvReader->getData($filePath);
        if (empty($rows)) {
            return ['success_count' => 0, 'error_count' => 1, 'errors' => ['CSV file is empty.']];
        }

        $headers = array_shift($rows);
        $headerMap = array_flip($headers);

        $successCount = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $lineNum = $index + 2;
            try {
                $name = $row[$headerMap['name'] ?? 0] ?? '';
                if (empty(trim($name))) {
                    $errors[] = "Row $lineNum: Name is required.";
                    continue;
                }

                $area = $this->areaFactory->create();
                $area->setName(trim($name));
                $area->setDescription($row[$headerMap['description'] ?? -1] ?? '');
                $area->setIsActive((bool)(int)($row[$headerMap['is_active'] ?? -1] ?? 1));

                $countries = !empty($row[$headerMap['countries'] ?? -1]) ? explode(',', $row[$headerMap['countries']]) : [];
                $area->setCountries($countries);

                $regions = !empty($row[$headerMap['regions'] ?? -1]) ? explode(',', $row[$headerMap['regions']]) : [];
                $area->setRegions($regions);

                $citiesRaw = $row[$headerMap['cities'] ?? -1] ?? '';
                $cities = preg_split('/[\r\n,]+/', (string)$citiesRaw, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $area->setCities(array_map('trim', $cities));

                $postcodesRaw = $row[$headerMap['postcodes'] ?? -1] ?? '';
                $postcodes = preg_split('/[\r\n,]+/', (string)$postcodesRaw, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $area->setPostcodes(array_map('trim', $postcodes));

                $addressRaw = $row[$headerMap['address_lines'] ?? -1] ?? '';
                $addressLines = preg_split('/[\r\n,]+/', (string)$addressRaw, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $area->setAddressLines(array_map('trim', $addressLines));

                $area->setAddressLinesType($row[$headerMap['address_lines_type'] ?? -1] ?? 'include');

                $this->areaRepository->save($area);
                $successCount++;
            } catch (\Throwable $e) {
                $errors[] = "Row $lineNum: " . $e->getMessage();
                $this->logger->error("ShippingArea import error on row $lineNum: " . $e->getMessage());
            }
        }

        return [
            'success_count' => $successCount,
            'error_count' => count($errors),
            'errors' => $errors
        ];
    }
}
