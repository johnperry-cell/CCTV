import type { HasKey, Prettify } from "../utils";
import type { Profile, User } from "./users";

export type LogLevel = 0 | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8;

export type LogSummaryItem = Prettify<
  HasKey & {
    level: LogLevel;
    prct: number;
    total: number;
    name: string;
  }
>;

export type LogSummary = Record<string, Record<string, number>>;

export type LogUser = Prettify<
  Pick<User, "username" | "email"> & Pick<Profile, "full_name">
>;

export type LogData = Prettify<
  HasKey & {
    action: string;
    actor?: string;
    timestamp: string;
    id: string;
    level: LogLevel;
    module: string;
    type?: number;
    user?: LogUser;
    new_data?: unknown;
    old_data?: unknown;
  }
>;

export type LogFileItem = {
  name: string;
  size: number;
  modified: string;
};
