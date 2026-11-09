<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Api\Data;

interface RuleInterface
{
    public const RULE_ID = 'rule_id';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const IS_ACTIVE = 'is_active';
    public const PRIORITY = 'priority';
    public const STOP_RULES_PROCESSING = 'stop_rules_processing';
    public const FROM_DATE = 'from_date';
    public const TO_DATE = 'to_date';
    public const DAYS_OF_WEEK = 'days_of_week';
    public const TIME_FROM = 'time_from';
    public const TIME_TO = 'time_to';
    public const CARRIERS = 'carriers';
    public const METHODS = 'methods';
    public const CUSTOM_CARRIER_LABEL = 'custom_carrier_label';
    public const CUSTOM_METHOD_LABEL = 'custom_method_label';
    public const APPLY_IN_ADMIN = 'apply_in_admin';
    public const COUPON_TYPE = 'coupon_type';
    public const COUPON_CODE = 'coupon_code';
    public const BACKORDER_MODE = 'backorder_mode';
    public const CALCULATION_TYPE = 'calculation_type';
    public const CALC_BASE_RATE = 'calc_base_rate';
    public const CALC_PER_PRODUCT = 'calc_per_product';
    public const CALC_PER_WEIGHT = 'calc_per_weight';
    public const CALC_PERCENT_PRODUCT = 'calc_percent_product';
    public const CALC_HANDLING_PERCENT = 'calc_handling_percent';
    public const MIN_RATE_CHANGE = 'min_rate_change';
    public const MAX_RATE_CHANGE = 'max_rate_change';
    public const MIN_TOTAL_RATE = 'min_total_rate';
    public const MAX_TOTAL_RATE = 'max_total_rate';
    public const CONDITIONS_SERIALIZED = 'conditions_serialized';
    public const ACTIONS_SERIALIZED = 'actions_serialized';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const STORE_IDS = 'store_ids';
    public const CUSTOMER_GROUP_IDS = 'customer_group_ids';

    // Calculation Types
    public const CALC_REPLACE = 'replace';
    public const CALC_SURCHARGE = 'surcharge';
    public const CALC_DISCOUNT = 'discount';
    public const CALC_PARTIAL_REPLACE = 'partial_replace';

    // Coupon Types
    public const COUPON_NONE = 0;
    public const COUPON_SPECIFIC = 1;
    public const COUPON_EXCLUDE = 2;

    // Backorder Modes
    public const BACKORDER_ALL = 0;
    public const BACKORDER_ONLY = 1;
    public const BACKORDER_EXCLUDE = 2;

    /**
     * @return int|null
     */
    public function getRuleId(): ?int;

    /**
     * @param int|null $id
     * @return $this
     */
    public function setRuleId(?int $id);

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name);

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string|null $desc
     * @return $this
     */
    public function setDescription(?string $desc);

    /**
     * @return bool
     */
    public function getIsActive(): bool;

    /**
     * @param bool $active
     * @return $this
     */
    public function setIsActive(bool $active);

    /**
     * @return int
     */
    public function getPriority(): int;

    /**
     * @param int $priority
     * @return $this
     */
    public function setPriority(int $priority);

    /**
     * @return bool
     */
    public function getStopRulesProcessing(): bool;

    /**
     * @param bool $stop
     * @return $this
     */
    public function setStopRulesProcessing(bool $stop);

    /**
     * @return string|null
     */
    public function getFromDate(): ?string;

    /**
     * @param string|null $fromDate
     * @return $this
     */
    public function setFromDate(?string $fromDate);

    /**
     * @return string|null
     */
    public function getToDate(): ?string;

    /**
     * @param string|null $toDate
     * @return $this
     */
    public function setToDate(?string $toDate);

    /**
     * @return string|null
     */
    public function getDaysOfWeek(): ?string;

    /**
     * @param string|null $days
     * @return $this
     */
    public function setDaysOfWeek(?string $days);

    /**
     * @return string|null
     */
    public function getTimeFrom(): ?string;

    /**
     * @param string|null $timeFrom
     * @return $this
     */
    public function setTimeFrom(?string $timeFrom);

    /**
     * @return string|null
     */
    public function getTimeTo(): ?string;

    /**
     * @param string|null $timeTo
     * @return $this
     */
    public function setTimeTo(?string $timeTo);

    /**
     * @return string|null
     */
    public function getCarriers(): ?string;

    /**
     * @param string|null $carriers
     * @return $this
     */
    public function setCarriers(?string $carriers);

    /**
     * @return string|null
     */
    public function getMethods(): ?string;

    /**
     * @param string|null $methods
     * @return $this
     */
    public function setMethods(?string $methods);

    /**
     * @return string|null
     */
    public function getCustomCarrierLabel(): ?string;

    /**
     * @param string|null $label
     * @return $this
     */
    public function setCustomCarrierLabel(?string $label);

    /**
     * @return string|null
     */
    public function getCustomMethodLabel(): ?string;

    /**
     * @param string|null $label
     * @return $this
     */
    public function setCustomMethodLabel(?string $label);

    /**
     * @return bool
     */
    public function getApplyInAdmin(): bool;

    /**
     * @param bool $apply
     * @return $this
     */
    public function setApplyInAdmin(bool $apply);

    /**
     * @return int
     */
    public function getCouponType(): int;

    /**
     * @param int $type
     * @return $this
     */
    public function setCouponType(int $type);

    /**
     * @return string|null
     */
    public function getCouponCode(): ?string;

    /**
     * @param string|null $coupon
     * @return $this
     */
    public function setCouponCode(?string $coupon);

    /**
     * @return int
     */
    public function getBackorderMode(): int;

    /**
     * @param int $mode
     * @return $this
     */
    public function setBackorderMode(int $mode);

    /**
     * @return string
     */
    public function getCalculationType(): string;

    /**
     * @param string $calcType
     * @return $this
     */
    public function setCalculationType(string $calcType);

    /**
     * @return float
     */
    public function getCalcBaseRate(): float;

    /**
     * @param float $rate
     * @return $this
     */
    public function setCalcBaseRate(float $rate);

    /**
     * @return float
     */
    public function getCalcPerProduct(): float;

    /**
     * @param float $rate
     * @return $this
     */
    public function setCalcPerProduct(float $rate);

    /**
     * @return float
     */
    public function getCalcPerWeight(): float;

    /**
     * @param float $rate
     * @return $this
     */
    public function setCalcPerWeight(float $rate);

    /**
     * @return float
     */
    public function getCalcPercentProduct(): float;

    /**
     * @param float $rate
     * @return $this
     */
    public function setCalcPercentProduct(float $rate);

    /**
     * @return float
     */
    public function getCalcHandlingPercent(): float;

    /**
     * @param float $rate
     * @return $this
     */
    public function setCalcHandlingPercent(float $rate);

    /**
     * @return float|null
     */
    public function getMinRateChange(): ?float;

    /**
     * @param float|null $val
     * @return $this
     */
    public function setMinRateChange(?float $val);

    /**
     * @return float|null
     */
    public function getMaxRateChange(): ?float;

    /**
     * @param float|null $val
     * @return $this
     */
    public function setMaxRateChange(?float $val);

    /**
     * @return float|null
     */
    public function getMinTotalRate(): ?float;

    /**
     * @param float|null $val
     * @return $this
     */
    public function setMinTotalRate(?float $val);

    /**
     * @return float|null
     */
    public function getMaxTotalRate(): ?float;

    /**
     * @param float|null $val
     * @return $this
     */
    public function setMaxTotalRate(?float $val);

    /**
     * @return string|null
     */
    public function getConditionsSerialized(): ?string;

    /**
     * @param string|null $conditions
     * @return $this
     */
    public function setConditionsSerialized(?string $conditions);

    /**
     * @return int[]
     */
    public function getStoreIds(): array;

    /**
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds(array $storeIds);

    /**
     * @return int[]
     */
    public function getCustomerGroupIds(): array;

    /**
     * @param int[] $groupIds
     * @return $this
     */
    public function setCustomerGroupIds(array $groupIds);

    /**
     * Validate model against rule conditions.
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model);
}
