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

                $carriersRaw = $row[$headerMap['carriers'] ?? -1] ?? '';
                $rule->setCarriers(!empty(trim((string)$carriersRaw)) ? trim((string)$carriersRaw) : null);

                $methodsRaw = $row[$headerMap['methods'] ?? -1] ?? '';
                $rule->setMethods(!empty(trim((string)$methodsRaw)) ? trim((string)$methodsRaw) : null);

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

                $carrierTitle = $row[$headerMap['custom_carrier_title'] ?? -1] ?? null;
                $rule->setCustomCarrierLabel(!empty(trim((string)$carrierTitle)) ? trim((string)$carrierTitle) : null);

                $methodTitle = $row[$headerMap['custom_method_title'] ?? -1] ?? null;
                $rule->setCustomMethodLabel(!empty(trim((string)$methodTitle)) ? trim((string)$methodTitle) : null);

                $rule->setDiscardSubsequentRules((bool)(int)($row[$headerMap['discard_subsequent_rules'] ?? -1] ?? 0));

                $rule->setFromDate(!empty($row[$headerMap['from_date'] ?? -1]) ? $row[$headerMap['from_date']] : null);
                $rule->setToDate(!empty($row[$headerMap['to_date'] ?? -1]) ? $row[$headerMap['to_date']] : null);

                $daysRaw = $row[$headerMap['days_of_week'] ?? -1] ?? '';
                $rule->setDaysOfWeek(!empty(trim((string)$daysRaw)) ? trim((string)$daysRaw) : null);

                $rule->setTimeFrom(!empty($row[$headerMap['time_from'] ?? -1]) ? $row[$headerMap['time_from']] : null);
                $rule->setTimeTo(!empty($row[$headerMap['time_to'] ?? -1]) ? $row[$headerMap['time_to']] : null);

                $couponTypeRaw = (string)($row[$headerMap['coupon_type'] ?? -1] ?? 'all');
                $rule->setCouponType($couponTypeRaw === 'specific' || $couponTypeRaw === '1' ? 1 : 0);
                $rule->setCouponCode(!empty($row[$headerMap['coupon_code'] ?? -1]) ? (string)$row[$headerMap['coupon_code']] : null);

                $backorderRaw = (string)($row[$headerMap['backorder_mode'] ?? -1] ?? 'all');
                $backorderInt = ($backorderRaw === 'backorders' || $backorderRaw === '2') ? 2 : (($backorderRaw === 'in_stock' || $backorderRaw === '1') ? 1 : 0);
                $rule->setBackorderMode($backorderInt);

                if (!empty($row[$headerMap['conditions_serialized'] ?? -1])) {
                    $condRaw = trim((string)$row[$headerMap['conditions_serialized']]);
                    if (str_starts_with($condRaw, '{') || str_starts_with($condRaw, '[')) {
                        $rule->setConditionsSerialized($condRaw);
                    }
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

                $countriesRaw = $row[$headerMap['countries'] ?? -1] ?? '';
                $area->setCountries(!empty(trim((string)$countriesRaw)) ? trim((string)$countriesRaw) : null);

                $regionsRaw = $row[$headerMap['regions'] ?? -1] ?? '';
                $area->setRegions(!empty(trim((string)$regionsRaw)) ? trim((string)$regionsRaw) : null);

                $citiesRaw = $row[$headerMap['cities'] ?? -1] ?? '';
                $area->setCities(!empty(trim((string)$citiesRaw)) ? trim((string)$citiesRaw) : null);

                $postcodesRaw = $row[$headerMap['postcodes'] ?? -1] ?? '';
                $area->setPostcodes(!empty(trim((string)$postcodesRaw)) ? trim((string)$postcodesRaw) : null);

                $addressRaw = $row[$headerMap['address_lines'] ?? -1] ?? '';
                $area->setAddressLines(!empty(trim((string)$addressRaw)) ? trim((string)$addressRaw) : null);

                $area->setCountriesType(0);
                $area->setRegionsType(0);
                $area->setCitiesType(0);
                $area->setPostcodesType(0);
                $area->setAddressLinesType(($row[$headerMap['address_lines_type'] ?? -1] ?? '') === 'exclude' ? 2 : 1);
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
