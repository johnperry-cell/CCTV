import type { HasKey, Prettify } from "../utils";
import type { BaseModel, Common } from "./base";
import type { PermissionItem } from "./permission";
import type { RoleItem } from "./roles";
import type { Team } from "./team";

export type Gender = Prettify<
  HasKey &
    Common & {
      description?: string;
      active?: boolean;
    }
>;

export type AddressType = Prettify<Common>;

export type UserRole = Pick<RoleItem, "id" | "name" | "color">;
export type UserPermission = Pick<PermissionItem, "id" | "name">;

export type LocationItem = Prettify<
  HasKey & {
    code: string;
    name: string;
  }
>;

export type Address = Prettify<
  HasKey &
    Required<BaseModel> & {
      type?: AddressType;
      readonly full: string;
      location: string;
      zipCode: string;
      isMain: boolean;
      barangay: LocationItem;
      city: LocationItem;
      province: LocationItem;
      region: LocationItem;
      islandGroup: LocationItem;
    }
>;

export type Profile = Prettify<
  HasKey & {
    first_name?: string;
    last_name?: string;
    middle_name?: string;
    suffix?: string;
    nickname?: string;
    readonly full_name: string;
    birthdate?: string;
    gender?: Gender;
    addresses?: Array<Address>;
    images?: Array<ProfileImage>;
  }
>;

export type User = Prettify<
  HasKey &
    Required<BaseModel> & {
      active: boolean;
      username: string;
      email?: string;
      verified?: string;
      roles: Array<UserRole | string>;
      permissions: Array<UserPermission | string>;
      profile?: Profile;
      team?: Team;
    }
>;

export type ProfileImage = Prettify<
  HasKey & {
    alt: string;
    ext: string;
    mime: string;
    primary: boolean;
    size: number;
    url: Record<string, string>;
  }
>;
