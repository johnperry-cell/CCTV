export type PasswordRules = {
  min: number;
  max: number | null;
  numbers: boolean;
  symbols: boolean;
  letters: boolean;
  mixedCase: boolean;
  messages: Record<string, string>;
};
