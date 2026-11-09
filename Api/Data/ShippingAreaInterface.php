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

    public function getAreaId(): ?int;
    public function setAreaId(?int $id): self;

    public function getName(): string;
    public function setName(string $name): self;

    public function getDescription(): ?string;
    public function setDescription(?string $desc): self;

    public function getIsActive(): bool;
    public function setIsActive(bool $active): self;

    public function getCountriesType(): int;
    public function setCountriesType(int $type): self;

    public function getCountries(): ?string;
    public function setCountries(?string $countries): self;

    public function getRegionsType(): int;
    public function setRegionsType(int $type): self;

    public function getRegions(): ?string;
    public function setRegions(?string $regions): self;

    public function getCitiesType(): int;
    public function setCitiesType(int $type): self;

    public function getCities(): ?string;
    public function setCities(?string $cities): self;

    public function getPostcodesType(): int;
    public function setPostcodesType(int $type): self;

    public function getPostcodes(): ?string;
    public function setPostcodes(?string $postcodes): self;

    public function getAddressLinesType(): int;
    public function setAddressLinesType(int $type): self;

    public function getAddressLines(): ?string;
    public function setAddressLines(?string $addressLines): self;
}
