<?php
declare(strict_types=1);

/**
 * Live Client Demonstration Fixture Seeder
 * Populates realistic, client-ready shipping rules and shipping areas.
 */

use Magento\Framework\App\Bootstrap;
use PriyankJivani\ShippingRules\Api\Data\RuleInterface;
use PriyankJivani\ShippingRules\Model\RuleFactory;
use PriyankJivani\ShippingRules\Model\ShippingAreaFactory;
use PriyankJivani\ShippingRules\Api\RuleRepositoryInterface;
use PriyankJivani\ShippingRules\Api\ShippingAreaRepositoryInterface;

require '/var/www/magento2/app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$ruleFactory = $objectManager->get(RuleFactory::class);
$areaFactory = $objectManager->get(ShippingAreaFactory::class);
$ruleRepository = $objectManager->get(RuleRepositoryInterface::class);
$areaRepository = $objectManager->get(ShippingAreaRepositoryInterface::class);

echo "\n--- Seeding Live Demonstration Shipping Rules & Areas ---\n";

// 1. Create Shipping Area: Alaska & Hawaii
$area = $areaFactory->create();
$area->load(1);
$area->setName('US Non-Contiguous: Alaska & Hawaii');
$area->setDescription('Remote non-contiguous US states requiring specialized air transport.');
$area->setIsActive(true);
$area->setCountries('US');
$area->setRegions('AK,HI');
$area->setPostcodes("99501-99695\n96701-96898");
$area = $areaRepository->save($area);
echo "Configured Shipping Area: {$area->getName()} (ID: {$area->getId()})\n";

// 2. Rule 1: Alaska & Hawaii Air Surcharge (+$35.00)
$rule1 = $ruleFactory->create();
$rule1->load(1);
$rule1->setName('Alaska & Hawaii Air Surcharge (+$35.00)');
$rule1->setDescription('Applies a $35.00 air transport surcharge for delivery to Alaska and Hawaii.');
$rule1->setIsActive(true);
$rule1->setPriority(10);
$rule1->setCarriers('flatrate');
$rule1->setStoreIds([0]);
$rule1->setCustomerGroupIds([0, 1, 2, 3]);
$rule1->setCalculationType(RuleInterface::CALC_SURCHARGE);
$rule1->setCalcBaseRate(35.00);
$rule1->setCustomCarrierLabel('Expedited Air Freight');
$rule1->setCustomMethodLabel('Standard Air Delivery (Non-Contiguous)');
$rule1->setConditionsSerialized(json_encode([
    'type' => 'PriyankJivani\ShippingRules\Model\Rule\Condition\Combine',
    'attribute' => null,
    'operator' => null,
    'value' => '1',
    'is_value_processed' => null,
    'aggregator' => 'all',
    'conditions' => [
        [
            'type' => 'PriyankJivani\ShippingRules\Model\Rule\Condition\Area',
            'attribute' => 'shipping_area_id',
            'operator' => '==',
            'value' => (string)$area->getId(),
            'is_value_processed' => false
        ]
    ]
]));
$rule1 = $ruleRepository->save($rule1);
echo "Configured Rule 1: {$rule1->getName()} (ID: {$rule1->getId()})\n";

// 3. Rule 2: Free Shipping Tier on Orders Over $75.00
$rule2 = $ruleFactory->create();
$rule2->load(2);
$rule2->setName('Free Ground Shipping Tier (Orders Over $75)');
$rule2->setDescription('Promotional free shipping automatically applied to qualifying cart totals.');
$rule2->setIsActive(true);
$rule2->setPriority(20);
$rule2->setCarriers('flatrate');
$rule2->setStoreIds([0]);
$rule2->setCustomerGroupIds([0, 1, 2, 3]);
$rule2->setCalculationType(RuleInterface::CALC_REPLACE);
$rule2->setCalcBaseRate(0.00);
$rule2->setStopRulesProcessing(true);
$rule2->setCustomCarrierLabel('Complimentary Shipping');
$rule2->setCustomMethodLabel('Free Ground Delivery (Tier Qualified)');
$rule2->setConditionsSerialized(json_encode([
    'type' => 'PriyankJivani\ShippingRules\Model\Rule\Condition\Combine',
    'attribute' => null,
    'operator' => null,
    'value' => '1',
    'is_value_processed' => null,
    'aggregator' => 'all',
    'conditions' => [
        [
            'type' => 'PriyankJivani\ShippingRules\Model\Rule\Condition\Address',
            'attribute' => 'base_subtotal',
            'operator' => '>=',
            'value' => '75',
            'is_value_processed' => false
        ]
    ]
]));
$rule2 = $ruleRepository->save($rule2);
echo "Configured Rule 2: {$rule2->getName()} (ID: {$rule2->getId()})\n";

// 4. Rule 3: Heavy Freight Handling Fee ($2.00 / unit weight for packages >= 50 lbs)
$rule3 = $ruleFactory->create();
$rule3->load(3);
$rule3->setName('Heavy Item Handling Fee ($2.00 per lb)');
$rule3->setDescription('Adds handling surcharge based on total package weight for heavy freight.');
$rule3->setIsActive(true);
$rule3->setPriority(30);
$rule3->setCarriers('flatrate');
$rule3->setStoreIds([0]);
$rule3->setCustomerGroupIds([0, 1, 2, 3]);
$rule3->setCalculationType(RuleInterface::CALC_SURCHARGE);
$rule3->setCalcBaseRate(5.00);
$rule3->setCalcPerWeight(2.00);
$rule3->setMinRateChange(5.00);
$rule3->setMaxRateChange(50.00);
$rule3->setCustomCarrierLabel('Freight Logistics');
$rule3->setCustomMethodLabel('Heavy Freight Delivery');
$rule3->setConditionsSerialized(json_encode([
    'type' => 'PriyankJivani\ShippingRules\Model\Rule\Condition\Combine',
    'attribute' => null,
    'operator' => null,
    'value' => '1',
    'is_value_processed' => null,
    'aggregator' => 'all',
    'conditions' => [
        [
            'type' => 'PriyankJivani\ShippingRules\Model\Rule\Condition\Address',
            'attribute' => 'weight',
            'operator' => '>=',
            'value' => '50',
            'is_value_processed' => false
        ]
    ]
]));
$rule3 = $ruleRepository->save($rule3);
echo "Configured Rule 3: {$rule3->getName()} (ID: {$rule3->getId()})\n";

// 5. Rule 4: Wholesale Partner 15% Shipping Discount
$rule4 = $ruleFactory->create();
$rule4->load(4);
$rule4->setName('Wholesale Tier 15% Shipping Discount');
$rule4->setDescription('Preferred B2B logistics rate discount for registered Wholesale customer accounts.');
$rule4->setIsActive(true);
$rule4->setPriority(15);
$rule4->setCarriers('flatrate');
$rule4->setStoreIds([0]);
$rule4->setCustomerGroupIds([2]); // Wholesale
$rule4->setCalculationType(RuleInterface::CALC_DISCOUNT);
$rule4->setCalcHandlingPercent(15.0);
$rule4->setCustomCarrierLabel('B2B Logistics Partner');
$rule4->setCustomMethodLabel('Wholesale Priority Freight');
$rule4 = $ruleRepository->save($rule4);
echo "Configured Rule 4: {$rule4->getName()} (ID: {$rule4->getId()})\n";

// 6. Rule 5: Weekend Express Delivery Special ($3.99 Flat)
$rule5 = $ruleFactory->create();
$rule5->load(5);
$rule5->setName('Weekend Express Delivery Special ($3.99 Flat)');
$rule5->setDescription('Special flat rate discount exclusively available on Saturdays and Sundays.');
$rule5->setIsActive(true);
$rule5->setPriority(5);
$rule5->setCarriers('flatrate');
$rule5->setStoreIds([0]);
$rule5->setCustomerGroupIds([0, 1, 2, 3]);
$rule5->setDaysOfWeek('0,6'); // Saturday & Sunday
$rule5->setCalculationType(RuleInterface::CALC_REPLACE);
$rule5->setCalcBaseRate(3.99);
$rule5->setCustomCarrierLabel('Weekend Express');
$rule5->setCustomMethodLabel('Fast Dispatch Weekend Rate');
$rule5 = $ruleRepository->save($rule5);
echo "Configured Rule 5: {$rule5->getName()} (ID: {$rule5->getId()})\n";

echo "--- Demonstration Data Seeded Successfully ---\n\n";
