<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Test\Unit\Model\Service;

use PHPUnit\Framework\TestCase;
use PriyankJivani\ShippingRules\Model\Service\ShippingAreaMatcher;
use PriyankJivani\ShippingRules\Api\ShippingAreaRepositoryInterface;
use PriyankJivani\ShippingRules\Api\Data\ShippingAreaInterface;
use Magento\Quote\Model\Quote\Address;

class ShippingAreaMatcherTest extends TestCase
{
    private $repositoryMock;
    private ShippingAreaMatcher $matcher;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ShippingAreaRepositoryInterface::class);
        $this->matcher = new ShippingAreaMatcher($this->repositoryMock);
    }

    private function createAreaMock(array $data): ShippingAreaInterface
    {
        $area = $this->createMock(ShippingAreaInterface::class);
        $area->method('getIsActive')->willReturn(true);
        $area->method('getCountriesType')->willReturn($data['countries_type'] ?? ShippingAreaInterface::TYPE_ALL);
        $area->method('getCountries')->willReturn($data['countries'] ?? '');
        $area->method('getRegionsType')->willReturn($data['regions_type'] ?? ShippingAreaInterface::TYPE_ALL);
        $area->method('getRegions')->willReturn($data['regions'] ?? '');
        $area->method('getCitiesType')->willReturn($data['cities_type'] ?? ShippingAreaInterface::TYPE_ALL);
        $area->method('getCities')->willReturn($data['cities'] ?? '');
        $area->method('getPostcodesType')->willReturn($data['postcodes_type'] ?? ShippingAreaInterface::TYPE_ALL);
        $area->method('getPostcodes')->willReturn($data['postcodes'] ?? '');
        $area->method('getAddressLinesType')->willReturn($data['address_lines_type'] ?? ShippingAreaInterface::ADDR_TYPE_ALL);
        $area->method('getAddressLines')->willReturn($data['address_lines'] ?? '');

        return $area;
    }

    private function createAddressMock(array $data): Address
    {
        $address = $this->createMock(Address::class);
        $address->method('getCountryId')->willReturn($data['country_id'] ?? 'US');
        $address->method('getRegionId')->willReturn($data['region_id'] ?? '2');
        $address->method('getRegionCode')->willReturn($data['region_code'] ?? 'AK');
        $address->method('getRegion')->willReturn($data['region'] ?? 'Alaska');
        $address->method('getCity')->willReturn($data['city'] ?? 'Anchorage');
        $address->method('getPostcode')->willReturn($data['postcode'] ?? '99501');
        $address->method('getStreet')->willReturn($data['street'] ?? ['100 Main St']);

        return $address;
    }

    public function testMatchCountryInclude(): void
    {
        $area = $this->createAreaMock([
            'countries_type' => ShippingAreaInterface::TYPE_INCLUDE,
            'countries' => 'US,CA,MX'
        ]);
        $this->repositoryMock->method('getById')->willReturn($area);

        $addressUS = $this->createAddressMock(['country_id' => 'US']);
        $this->assertTrue($this->matcher->matchAddress(1, $addressUS));

        $addressGB = $this->createAddressMock(['country_id' => 'GB']);
        $this->assertFalse($this->matcher->matchAddress(1, $addressGB));
    }

    public function testMatchCountryExclude(): void
    {
        $area = $this->createAreaMock([
            'countries_type' => ShippingAreaInterface::TYPE_EXCLUDE,
            'countries' => 'US'
        ]);
        $this->repositoryMock->method('getById')->willReturn($area);

        $addressUS = $this->createAddressMock(['country_id' => 'US']);
        $this->assertFalse($this->matcher->matchAddress(1, $addressUS));

        $addressCA = $this->createAddressMock(['country_id' => 'CA']);
        $this->assertTrue($this->matcher->matchAddress(1, $addressCA));
    }

    public function testMatchPostcodeRange(): void
    {
        $area = $this->createAreaMock([
            'postcodes_type' => ShippingAreaInterface::TYPE_INCLUDE,
            'postcodes' => '99501-99524'
        ]);
        $this->repositoryMock->method('getById')->willReturn($area);

        $addressInRange = $this->createAddressMock(['postcode' => '99510']);
        $this->assertTrue($this->matcher->matchAddress(1, $addressInRange));

        $addressOutOfRange = $this->createAddressMock(['postcode' => '99530']);
        $this->assertFalse($this->matcher->matchAddress(1, $addressOutOfRange));
    }

    public function testMatchPostcodeWildcard(): void
    {
        $area = $this->createAreaMock([
            'postcodes_type' => ShippingAreaInterface::TYPE_INCLUDE,
            'postcodes' => '995*'
        ]);
        $this->repositoryMock->method('getById')->willReturn($area);

        $addressWildcardMatch = $this->createAddressMock(['postcode' => '99599']);
        $this->assertTrue($this->matcher->matchAddress(1, $addressWildcardMatch));

        $addressNoMatch = $this->createAddressMock(['postcode' => '90210']);
        $this->assertFalse($this->matcher->matchAddress(1, $addressNoMatch));
    }

    public function testMatchAddressLinePOBox(): void
    {
        $area = $this->createAreaMock([
            'address_lines_type' => ShippingAreaInterface::ADDR_TYPE_CONTAINS,
            'address_lines' => 'PO Box, P.O. Box, Box'
        ]);
        $this->repositoryMock->method('getById')->willReturn($area);

        $addressPOBox = $this->createAddressMock(['street' => ['PO Box 1234', 'Apt 2']]);
        $this->assertTrue($this->matcher->matchAddress(1, $addressPOBox));

        $addressStreet = $this->createAddressMock(['street' => ['742 Evergreen Terrace']]);
        $this->assertFalse($this->matcher->matchAddress(1, $addressStreet));
    }
}
