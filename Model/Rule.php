<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model;

use Magento\Rule\Model\AbstractModel;
use PriyankJivani\ShippingRules\Api\Data\RuleInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use PriyankJivani\ShippingRules\Model\Rule\Condition\CombineFactory;
use PriyankJivani\ShippingRules\Model\Rule\Condition\ProductCombineFactory;

class Rule extends AbstractModel implements RuleInterface
{
    private CombineFactory $combineFactory;
    private ProductCombineFactory $productCombineFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        CombineFactory $combineFactory,
        ProductCombineFactory $productCombineFactory,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->combineFactory = $combineFactory;
        $this->productCombineFactory = $productCombineFactory;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Rule::class);
        $this->setIdFieldName(self::RULE_ID);
    }

    public function getConditionsInstance()
    {
        return $this->combineFactory->create();
    }

    public function getActionsInstance()
    {
        return $this->productCombineFactory->create();
    }

    public function getRuleId(): ?int
    {
        $id = $this->getData(self::RULE_ID);
        return $id !== null ? (int)$id : null;
    }

    public function setRuleId(?int $id): RuleInterface
    {
        return $this->setData(self::RULE_ID, $id);
    }

    public function getName(): string
    {
        return (string)$this->getData(self::NAME);
    }

    public function setName(string $name): RuleInterface
    {
        return $this->setData(self::NAME, $name);
    }

    public function getDescription(): ?string
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription(?string $desc): RuleInterface
    {
        return $this->setData(self::DESCRIPTION, $desc);
    }

    public function getIsActive(): bool
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    public function setIsActive(bool $active): RuleInterface
    {
        return $this->setData(self::IS_ACTIVE, $active ? 1 : 0);
    }

    public function getPriority(): int
    {
        return (int)$this->getData(self::PRIORITY);
    }

    public function setPriority(int $priority): RuleInterface
    {
        return $this->setData(self::PRIORITY, $priority);
    }

    public function getStopRulesProcessing(): bool
    {
        return (bool)$this->getData(self::STOP_RULES_PROCESSING) || (bool)$this->getData('discard_subsequent_rules');
    }

    public function setStopRulesProcessing(bool $stop): RuleInterface
    {
        $this->setData('discard_subsequent_rules', $stop ? 1 : 0);
        return $this->setData(self::STOP_RULES_PROCESSING, $stop ? 1 : 0);
    }

    public function getDiscardSubsequentRules(): bool
    {
        return $this->getStopRulesProcessing();
    }

    public function setDiscardSubsequentRules(bool $discard): RuleInterface
    {
        return $this->setStopRulesProcessing($discard);
    }

    public function getFromDate(): ?string
    {
        return $this->getData(self::FROM_DATE);
    }

    public function setFromDate(?string $fromDate): RuleInterface
    {
        return $this->setData(self::FROM_DATE, $fromDate);
    }

    public function getToDate(): ?string
    {
        return $this->getData(self::TO_DATE);
    }

    public function setToDate(?string $toDate): RuleInterface
    {
        return $this->setData(self::TO_DATE, $toDate);
    }

    public function getDaysOfWeek(): ?string
    {
        return $this->getData(self::DAYS_OF_WEEK);
    }

    public function setDaysOfWeek(?string $days): RuleInterface
    {
        return $this->setData(self::DAYS_OF_WEEK, $days);
    }

    public function getTimeFrom(): ?string
    {
        return $this->getData(self::TIME_FROM);
    }

    public function setTimeFrom(?string $timeFrom): RuleInterface
    {
        return $this->setData(self::TIME_FROM, $timeFrom);
    }

    public function getTimeTo(): ?string
    {
        return $this->getData(self::TIME_TO);
    }

    public function setTimeTo(?string $timeTo): RuleInterface
    {
        return $this->setData(self::TIME_TO, $timeTo);
    }

    public function getCarriers(): ?string
    {
        return $this->getData(self::CARRIERS);
    }

    public function setCarriers(?string $carriers): RuleInterface
    {
        return $this->setData(self::CARRIERS, $carriers);
    }

    public function getMethods(): ?string
    {
        return $this->getData(self::METHODS);
    }

    public function setMethods(?string $methods): RuleInterface
    {
        return $this->setData(self::METHODS, $methods);
    }

    public function getCustomCarrierLabel(): ?string
    {
        return $this->getData(self::CUSTOM_CARRIER_LABEL);
    }

    public function setCustomCarrierLabel(?string $label): RuleInterface
    {
        return $this->setData(self::CUSTOM_CARRIER_LABEL, $label);
    }

    public function getCustomMethodLabel(): ?string
    {
        return $this->getData(self::CUSTOM_METHOD_LABEL);
    }

    public function setCustomMethodLabel(?string $label): RuleInterface
    {
        return $this->setData(self::CUSTOM_METHOD_LABEL, $label);
    }

    public function getApplyInAdmin(): bool
    {
        return (bool)$this->getData(self::APPLY_IN_ADMIN);
    }

    public function setApplyInAdmin(bool $apply): RuleInterface
    {
        return $this->setData(self::APPLY_IN_ADMIN, $apply ? 1 : 0);
    }

    public function getCouponType(): int
    {
        return (int)$this->getData(self::COUPON_TYPE);
    }

    public function setCouponType(int $type): RuleInterface
    {
        return $this->setData(self::COUPON_TYPE, $type);
    }

    public function getCouponCode(): ?string
    {
        return $this->getData(self::COUPON_CODE);
    }

    public function setCouponCode(?string $coupon): RuleInterface
    {
        return $this->setData(self::COUPON_CODE, $coupon);
    }

    public function getBackorderMode(): int
    {
        return (int)$this->getData(self::BACKORDER_MODE);
    }

    public function setBackorderMode(int $mode): RuleInterface
    {
        return $this->setData(self::BACKORDER_MODE, $mode);
    }

    public function getCalculationType(): string
    {
        return (string)($this->getData(self::CALCULATION_TYPE) ?: self::CALC_REPLACE);
    }

    public function setCalculationType(string $calcType): RuleInterface
    {
        return $this->setData(self::CALCULATION_TYPE, $calcType);
    }

    public function getCalcBaseRate(): float
    {
        return (float)$this->getData(self::CALC_BASE_RATE);
    }

    public function setCalcBaseRate(float $rate): RuleInterface
    {
        return $this->setData(self::CALC_BASE_RATE, $rate);
    }

    public function getCalcPerProduct(): float
    {
        return (float)$this->getData(self::CALC_PER_PRODUCT);
    }

    public function setCalcPerProduct(float $rate): RuleInterface
    {
        return $this->setData(self::CALC_PER_PRODUCT, $rate);
    }

    public function getCalcPerWeight(): float
    {
        return (float)$this->getData(self::CALC_PER_WEIGHT);
    }

    public function setCalcPerWeight(float $rate): RuleInterface
    {
        return $this->setData(self::CALC_PER_WEIGHT, $rate);
    }

    public function getCalcPercentProduct(): float
    {
        return (float)$this->getData(self::CALC_PERCENT_PRODUCT);
    }

    public function setCalcPercentProduct(float $rate): RuleInterface
    {
        return $this->setData(self::CALC_PERCENT_PRODUCT, $rate);
    }

    public function getCalcHandlingPercent(): float
    {
        return (float)$this->getData(self::CALC_HANDLING_PERCENT);
    }

    public function setCalcHandlingPercent(float $rate): RuleInterface
    {
        return $this->setData(self::CALC_HANDLING_PERCENT, $rate);
    }

    public function getMinRateChange(): ?float
    {
        $v = $this->getData(self::MIN_RATE_CHANGE);
        return $v !== null && $v !== '' ? (float)$v : null;
    }

    public function setMinRateChange(?float $val): RuleInterface
    {
        return $this->setData(self::MIN_RATE_CHANGE, $val);
    }

    public function getMaxRateChange(): ?float
    {
        $v = $this->getData(self::MAX_RATE_CHANGE);
        return $v !== null && $v !== '' ? (float)$v : null;
    }

    public function setMaxRateChange(?float $val): RuleInterface
    {
        return $this->setData(self::MAX_RATE_CHANGE, $val);
    }

    public function getMinTotalRate(): ?float
    {
        $v = $this->getData(self::MIN_TOTAL_RATE);
        return $v !== null && $v !== '' ? (float)$v : null;
    }

    public function setMinTotalRate(?float $val): RuleInterface
    {
        return $this->setData(self::MIN_TOTAL_RATE, $val);
    }

    public function getMaxTotalRate(): ?float
    {
        $v = $this->getData(self::MAX_TOTAL_RATE);
        return $v !== null && $v !== '' ? (float)$v : null;
    }

    public function setMaxTotalRate(?float $val): RuleInterface
    {
        return $this->setData(self::MAX_TOTAL_RATE, $val);
    }

    public function getConditionsSerialized(): ?string
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    public function setConditionsSerialized(?string $conditions): RuleInterface
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $conditions);
    }

    public function getStoreIds(): array
    {
        $stores = $this->getData(self::STORE_IDS);
        return is_array($stores) ? $stores : [];
    }

    public function setStoreIds(array $storeIds): RuleInterface
    {
        return $this->setData(self::STORE_IDS, $storeIds);
    }

    public function getCustomerGroupIds(): array
    {
        $groups = $this->getData(self::CUSTOMER_GROUP_IDS);
        return is_array($groups) ? $groups : [];
    }

    public function setCustomerGroupIds(array $groupIds): RuleInterface
    {
        return $this->setData(self::CUSTOMER_GROUP_IDS, $groupIds);
    }
}
