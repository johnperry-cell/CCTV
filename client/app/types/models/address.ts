import type { HasKey, Prettify } from "../utils";

type Common = Prettify<
  HasKey & {
    name: string;
    code: string;
    psgc10DigitCode?: string;
  }
>;

export type IslandGroupType = Omit<Common, "psgc10DigitCode">;

export type RegionType = Prettify<
  Common & {
    regionName: string;
    islandGroupCode?: string;
  }
>;

export type ProvinceType = Prettify<
  Common & {
    isDistrict: boolean;
    regionCode: string;
    islandGroupCode: string;
  }
>;

export type CityType = Prettify<
  Common & {
    oldName: string;
    isMunicipality: Boolean;
    isCapital: Boolean;
    provinceCode: string;
    regionCode: string;
    islandGroupCode: string;
  }
>;

export type BarangayType = Prettify<
  Common & {
    oldName?: string;
    cityCode: string;
    provinceCode: string;
    regionCode: string;
    islandGroupCode: string;

    district?: number;
    telephone_number?: string;
    contact_number?: string;
    lng?: number;
    lat?: number;
    dru?: number;
  }
>;
