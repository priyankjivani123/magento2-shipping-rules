<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Service;

use PriyankJivani\ShippingRules\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use PriyankJivani\ShippingRules\Api\Data\RuleInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method as RateMethod;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class RuleProcessor
{
    private RuleCollectionFactory $ruleCollectionFactory;
    private RuleEvaluator $evaluator;
    private RateCalculator $calculator;
    private ScopeConfigInterface $scopeConfig;
    private LoggerInterface $logger;

    public function __construct(
        RuleCollectionFactory $ruleCollectionFactory,
        RuleEvaluator $evaluator,
        RateCalculator $calculator,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->evaluator = $evaluator;
        $this->calculator = $calculator;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    public function isModuleEnabled(int $storeId): bool
    {
        return $this->scopeConfig->isSetFlag(
            'priyankjivani_shippingrules/general/enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) || $this->scopeConfig->isSetFlag(
            'sales/priyankjivani_shippingrules/enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function processRates(array $rates, RateRequest $request): array
    {
        $storeId = (int)$request->getStoreId();
        if (!$this->isModuleEnabled($storeId)) {
            return $rates;
        }

        // Load active rules sorted by priority ASC
        $rules = $this->ruleCollectionFactory->create()
            ->addFieldToFilter(RuleInterface::IS_ACTIVE, 1)
            ->setOrder(RuleInterface::PRIORITY, 'ASC');

        if ($rules->getSize() === 0) {
            return $rates;
        }

        $debug = $this->scopeConfig->isSetFlag(
            'priyankjivani_shippingrules/general/debug_logging',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) || $this->scopeConfig->isSetFlag(
            'sales/priyankjivani_shippingrules/debug_logging',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        foreach ($rates as $rate) {
            if (!$rate instanceof RateMethod) {
                continue;
            }

            $carrierCode = (string)$rate->getCarrier();
            $methodCode = (string)$rate->getMethod();
            $currentPrice = (float)$rate->getPrice();
            $originalPrice = $currentPrice;
            $appliedRules = [];

            /** @var RuleInterface $rule */
            foreach ($rules as $rule) {
                if (!$this->evaluator->isRuleApplicable($rule, $request, $carrierCode, $methodCode)) {
                    continue;
                }

                $calcResult = $this->calculator->calculate($rule, $currentPrice, $request);
                $newPrice = (float)$calcResult['final_price'];

                if ($debug) {
                    $this->logger->info(sprintf(
                        '[PriyankJivani_ShippingRules] Rule #%d ("%s") applied to %s_%s. Price adjusted from %.2f to %.2f',
                        $rule->getRuleId(),
                        $rule->getName(),
                        $carrierCode,
                        $methodCode,
                        $currentPrice,
                        $newPrice
                    ));
                }

                $currentPrice = $newPrice;
                $appliedRules[] = $rule->getRuleId();

                // Custom labels
                if (!empty($calcResult['custom_carrier_label'])) {
                    $rate->setCarrierTitle($calcResult['custom_carrier_label']);
                }
                if (!empty($calcResult['custom_method_label'])) {
                    $rate->setMethodTitle($calcResult['custom_method_label']);
                }

                // Discard subsequent rules
                if ($rule->getStopRulesProcessing()) {
                    if ($debug) {
                        $this->logger->info(sprintf(
                            '[PriyankJivani_ShippingRules] Rule #%d has Discard Subsequent Rules active. Halting execution for %s_%s.',
                            $rule->getRuleId(),
                            $carrierCode,
                            $methodCode
                        ));
                    }
                    break;
                }
            }

            if (!empty($appliedRules)) {
                $rate->setPrice($currentPrice);
                $rate->setCost($currentPrice);
            }
        }

        return $rates;
    }
}
