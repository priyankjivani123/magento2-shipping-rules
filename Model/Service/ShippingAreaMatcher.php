<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Service;

use PriyankJivani\ShippingRules\Api\ShippingAreaRepositoryInterface;
use PriyankJivani\ShippingRules\Api\Data\ShippingAreaInterface;
use Magento\Quote\Model\Quote\Address;

class ShippingAreaMatcher
{
    private ShippingAreaRepositoryInterface $areaRepository;

    public function __construct(ShippingAreaRepositoryInterface $areaRepository)
    {
        $this->areaRepository = $areaRepository;
    }

    public function matchAddress(int $areaId, Address $address): bool
    {
        try {
            $area = $this->areaRepository->getById($areaId);
        } catch (\Throwable $e) {
            return false;
        }

        if (!$area->getIsActive()) {
            return false;
        }

        // 1. Country Check
        if (!$this->matchCountries($area, (string)$address->getCountryId())) {
            return false;
        }

        // 2. State/Region Check
        if (!$this->matchRegions($area, $address)) {
            return false;
        }

        // 3. City Check
        if (!$this->matchCities($area, (string)$address->getCity())) {
            return false;
        }

        // 4. Postcode Check
        if (!$this->matchPostcodes($area, (string)$address->getPostcode())) {
            return false;
        }

        // 5. Street / Address Lines Check
        if (!$this->matchAddressLines($area, $address->getStreet())) {
            return false;
        }

        return true;
    }

    private function matchCountries(ShippingAreaInterface $area, string $destCountry): bool
    {
        $countries = (string)$area->getCountries();
        if ($countries === '') {
            return true;
        }

        $type = $area->getCountriesType();
        if ($type === ShippingAreaInterface::TYPE_ALL) {
            $type = ShippingAreaInterface::TYPE_INCLUDE;
        }

        $list = array_filter(array_map('trim', explode(',', $countries)));
        $inList = in_array(strtoupper($destCountry), array_map('strtoupper', $list), true);

        return $type === ShippingAreaInterface::TYPE_INCLUDE ? $inList : !$inList;
    }

    private function matchRegions(ShippingAreaInterface $area, Address $address): bool
    {
        $regions = (string)$area->getRegions();
        if ($regions === '') {
            return true;
        }

        $type = $area->getRegionsType();
        if ($type === ShippingAreaInterface::TYPE_ALL) {
            $type = ShippingAreaInterface::TYPE_INCLUDE;
        }

        $list = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $regions)));
        $regionId = (string)$address->getRegionId();
        $regionCode = (string)$address->getRegionCode();
        $regionName = (string)$address->getRegion();

        $inList = false;
        foreach ($list as $item) {
            if (strcasecmp($item, $regionId) === 0
                || strcasecmp($item, $regionCode) === 0
                || strcasecmp($item, $regionName) === 0
            ) {
                $inList = true;
                break;
            }
        }

        return $type === ShippingAreaInterface::TYPE_INCLUDE ? $inList : !$inList;
    }

    private function matchCities(ShippingAreaInterface $area, string $destCity): bool
    {
        $cities = (string)$area->getCities();
        if ($cities === '') {
            return true;
        }

        $type = $area->getCitiesType();
        if ($type === ShippingAreaInterface::TYPE_ALL) {
            $type = ShippingAreaInterface::TYPE_INCLUDE;
        }

        $list = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $cities)));
        $inList = false;
        foreach ($list as $item) {
            if (strcasecmp($item, $destCity) === 0) {
                $inList = true;
                break;
            }
        }

        return $type === ShippingAreaInterface::TYPE_INCLUDE ? $inList : !$inList;
    }

    private function matchPostcodes(ShippingAreaInterface $area, string $destPostcode): bool
    {
        $postcodes = (string)$area->getPostcodes();
        if ($postcodes === '') {
            return true;
        }

        $type = $area->getPostcodesType();
        if ($type === ShippingAreaInterface::TYPE_ALL) {
            $type = ShippingAreaInterface::TYPE_INCLUDE;
        }

        $rawPostcodes = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $postcodes)));
        $inList = false;
        $destClean = strtoupper(trim($destPostcode));

        foreach ($rawPostcodes as $pattern) {
            $patternClean = strtoupper(trim($pattern));

            // Check Range: e.g. 99501-99524
            if (str_contains($patternClean, '-')) {
                [$start, $end] = explode('-', $patternClean, 2);
                $start = trim($start);
                $end = trim($end);

                if (is_numeric($start) && is_numeric($end) && is_numeric($destClean)) {
                    $numDest = (int)$destClean;
                    if ($numDest >= (int)$start && $numDest <= (int)$end) {
                        $inList = true;
                        break;
                    }
                }
            }

            // Check Wildcard: e.g. 995*
            if (str_ends_with($patternClean, '*')) {
                $prefix = rtrim($patternClean, '*');
                if (str_starts_with($destClean, $prefix)) {
                    $inList = true;
                    break;
                }
            }

            // Exact match
            if ($patternClean === $destClean) {
                $inList = true;
                break;
            }
        }

        return $type === ShippingAreaInterface::TYPE_INCLUDE ? $inList : !$inList;
    }

    private function matchAddressLines(ShippingAreaInterface $area, $street): bool
    {
        $type = $area->getAddressLinesType();
        if ($type === ShippingAreaInterface::ADDR_TYPE_ALL) {
            return true;
        }

        $streetStr = is_array($street) ? implode(' ', $street) : (string)$street;
        $patterns = array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string)$area->getAddressLines())));

        $contains = false;
        foreach ($patterns as $p) {
            if (stripos($streetStr, $p) !== false) {
                $contains = true;
                break;
            }
        }

        return $type === ShippingAreaInterface::ADDR_TYPE_CONTAINS ? $contains : !$contains;
    }
}
