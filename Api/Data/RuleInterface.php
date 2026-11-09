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

    public function getRuleId(): ?int;
    public function setRuleId(?int $id): self;

    public function getName(): string;
    public function setName(string $name): self;

    public function getDescription(): ?string;
    public function setDescription(?string $desc): self;

    public function getIsActive(): bool;
    public function setIsActive(bool $active): self;

    public function getPriority(): int;
    public function setPriority(int $priority): self;

    public function getStopRulesProcessing(): bool;
    public function setStopRulesProcessing(bool $stop): self;

    public function getFromDate(): ?string;
    public function setFromDate(?string $fromDate): self;

    public function getToDate(): ?string;
    public function setToDate(?string $toDate): self;

    public function getDaysOfWeek(): ?string;
    public function setDaysOfWeek(?string $days): self;

    public function getTimeFrom(): ?string;
    public function setTimeFrom(?string $timeFrom): self;

    public function getTimeTo(): ?string;
    public function setTimeTo(?string $timeTo): self;

    public function getCarriers(): ?string;
    public function setCarriers(?string $carriers): self;

    public function getMethods(): ?string;
    public function setMethods(?string $methods): self;

    public function getCustomCarrierLabel(): ?string;
    public function setCustomCarrierLabel(?string $label): self;

    public function getCustomMethodLabel(): ?string;
    public function setCustomMethodLabel(?string $label): self;

    public function getApplyInAdmin(): bool;
    public function setApplyInAdmin(bool $apply): self;

    public function getCouponType(): int;
    public function setCouponType(int $type): self;

    public function getCouponCode(): ?string;
    public function setCouponCode(?string $coupon): self;

    public function getBackorderMode(): int;
    public function setBackorderMode(int $mode): self;

    public function getCalculationType(): string;
    public function setCalculationType(string $calcType): self;

    public function getCalcBaseRate(): float;
    public function setCalcBaseRate(float $rate): self;

    public function getCalcPerProduct(): float;
    public function setCalcPerProduct(float $rate): self;

    public function getCalcPerWeight(): float;
    public function setCalcPerWeight(float $rate): self;

    public function getCalcPercentProduct(): float;
    public function setCalcPercentProduct(float $rate): self;

    public function getCalcHandlingPercent(): float;
    public function setCalcHandlingPercent(float $rate): self;

    public function getMinRateChange(): ?float;
    public function setMinRateChange(?float $val): self;

    public function getMaxRateChange(): ?float;
    public function setMaxRateChange(?float $val): self;

    public function getMinTotalRate(): ?float;
    public function setMinTotalRate(?float $val): self;

    public function getMaxTotalRate(): ?float;
    public function setMaxTotalRate(?float $val): self;

    public function getConditionsSerialized(): ?string;
    public function setConditionsSerialized(?string $conditions): self;

    public function getStoreIds(): array;
    public function setStoreIds(array $storeIds): self;

    public function getCustomerGroupIds(): array;
    public function setCustomerGroupIds(array $groupIds): self;
}
