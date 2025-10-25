import type { HasKey, Prettify } from "../utils";

export type PasswordGenerateOptions = Prettify<
  HasKey & {
    length: number;
    letters: boolean;
    mixedCase: boolean;
    numbers: boolean;
    symbols: boolean;
    excludeSimilarCharacters: boolean;
    exclude: string;
    excludeSimilarCharactersThreshold: number;
    excludeSimilarCharactersExclude: string;
  }
>;
