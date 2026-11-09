<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model;

use Magento\Framework\Model\AbstractModel;
use PriyankJivani\ShippingRules\Api\Data\ShippingAreaInterface;

class ShippingArea extends AbstractModel implements ShippingAreaInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel\ShippingArea::class);
        $this->setIdFieldName(self::AREA_ID);
    }

    public function getAreaId(): ?int
    {
        $id = $this->getData(self::AREA_ID);
        return $id !== null ? (int)$id : null;
    }

    public function setAreaId(?int $id): ShippingAreaInterface
    {
        return $this->setData(self::AREA_ID, $id);
    }

    public function getName(): string
    {
        return (string)$this->getData(self::NAME);
    }

    public function setName(string $name): ShippingAreaInterface
    {
        return $this->setData(self::NAME, $name);
    }

    public function getDescription(): ?string
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription(?string $desc): ShippingAreaInterface
    {
        return $this->setData(self::DESCRIPTION, $desc);
    }

    public function getIsActive(): bool
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    public function setIsActive(bool $active): ShippingAreaInterface
    {
        return $this->setData(self::IS_ACTIVE, $active ? 1 : 0);
    }

    public function getCountriesType(): int
    {
        return (int)$this->getData(self::COUNTRIES_TYPE);
    }

    public function setCountriesType(int $type): ShippingAreaInterface
    {
        return $this->setData(self::COUNTRIES_TYPE, $type);
    }

    public function getCountries(): ?string
    {
        return $this->getData(self::COUNTRIES);
    }

    public function setCountries(?string $countries): ShippingAreaInterface
    {
        return $this->setData(self::COUNTRIES, $countries);
    }

    public function getRegionsType(): int
    {
        return (int)$this->getData(self::REGIONS_TYPE);
    }

    public function setRegionsType(int $type): ShippingAreaInterface
    {
        return $this->setData(self::REGIONS_TYPE, $type);
    }

    public function getRegions(): ?string
    {
        return $this->getData(self::REGIONS);
    }

    public function setRegions(?string $regions): ShippingAreaInterface
    {
        return $this->setData(self::REGIONS, $regions);
    }

    public function getCitiesType(): int
    {
        return (int)$this->getData(self::CITIES_TYPE);
    }

    public function setCitiesType(int $type): ShippingAreaInterface
    {
        return $this->setData(self::CITIES_TYPE, $type);
    }

    public function getCities(): ?string
    {
        return $this->getData(self::CITIES);
    }

    public function setCities(?string $cities): ShippingAreaInterface
    {
        return $this->setData(self::CITIES, $cities);
    }

    public function getPostcodesType(): int
    {
        return (int)$this->getData(self::POSTCODES_TYPE);
    }

    public function setPostcodesType(int $type): ShippingAreaInterface
    {
        return $this->setData(self::POSTCODES_TYPE, $type);
    }

    public function getPostcodes(): ?string
    {
        return $this->getData(self::POSTCODES);
    }

    public function setPostcodes(?string $postcodes): ShippingAreaInterface
    {
        return $this->setData(self::POSTCODES, $postcodes);
    }

    public function getAddressLinesType(): int
    {
        return (int)$this->getData(self::ADDRESS_LINES_TYPE);
    }

    public function setAddressLinesType(int $type): ShippingAreaInterface
    {
        return $this->setData(self::ADDRESS_LINES_TYPE, $type);
    }

    public function getAddressLines(): ?string
    {
        return $this->getData(self::ADDRESS_LINES);
    }

    public function setAddressLines(?string $addressLines): ShippingAreaInterface
    {
        return $this->setData(self::ADDRESS_LINES, $addressLines);
    }
}
