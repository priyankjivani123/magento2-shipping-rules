<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Api\Data;

interface ShippingAreaInterface
{
    public const AREA_ID = 'area_id';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const IS_ACTIVE = 'is_active';
    public const COUNTRIES_TYPE = 'countries_type';
    public const COUNTRIES = 'countries';
    public const REGIONS_TYPE = 'regions_type';
    public const REGIONS = 'regions';
    public const CITIES_TYPE = 'cities_type';
    public const CITIES = 'cities';
    public const POSTCODES_TYPE = 'postcodes_type';
    public const POSTCODES = 'postcodes';
    public const ADDRESS_LINES_TYPE = 'address_lines_type';
    public const ADDRESS_LINES = 'address_lines';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const TYPE_ALL = 0;
    public const TYPE_INCLUDE = 1;
    public const TYPE_EXCLUDE = 2;

    public const ADDR_TYPE_ALL = 0;
    public const ADDR_TYPE_CONTAINS = 1;
    public const ADDR_TYPE_NOT_CONTAINS = 2;

    /**
     * @return int|null
     */
    public function getAreaId(): ?int;

    /**
     * @param int|null $id
     * @return $this
     */
    public function setAreaId(?int $id);

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
    public function getCountriesType(): int;

    /**
     * @param int $type
     * @return $this
     */
    public function setCountriesType(int $type);

    /**
     * @return string|null
     */
    public function getCountries(): ?string;

    /**
     * @param string|null $countries
     * @return $this
     */
    public function setCountries(?string $countries);

    /**
     * @return int
     */
    public function getRegionsType(): int;

    /**
     * @param int $type
     * @return $this
     */
    public function setRegionsType(int $type);

    /**
     * @return string|null
     */
    public function getRegions(): ?string;

    /**
     * @param string|null $regions
     * @return $this
     */
    public function setRegions(?string $regions);

    /**
     * @return int
     */
    public function getCitiesType(): int;

    /**
     * @param int $type
     * @return $this
     */
    public function setCitiesType(int $type);

    /**
     * @return string|null
     */
    public function getCities(): ?string;

    /**
     * @param string|null $cities
     * @return $this
     */
    public function setCities(?string $cities);

    /**
     * @return int
     */
    public function getPostcodesType(): int;

    /**
     * @param int $type
     * @return $this
     */
    public function setPostcodesType(int $type);

    /**
     * @return string|null
     */
    public function getPostcodes(): ?string;

    /**
     * @param string|null $postcodes
     * @return $this
     */
    public function setPostcodes(?string $postcodes);

    /**
     * @return int
     */
    public function getAddressLinesType(): int;

    /**
     * @param int $type
     * @return $this
     */
    public function setAddressLinesType(int $type);

    /**
     * @return string|null
     */
    public function getAddressLines(): ?string;

    /**
     * @param string|null $lines
     * @return $this
     */
    public function setAddressLines(?string $lines);
}
