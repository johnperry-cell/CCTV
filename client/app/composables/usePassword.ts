import { z, type ZodString } from "zod";
import type { PasswordGenerateOptions } from "~/types/composables/usePassword";

export const usePassword = () => {
  const passwordRules = (messages: Record<string, string> = {}): ZodString => {
    const config = useAppConfig().mitd.password.rules;
    const _messages = Object.assign({}, config.messages, messages);
    let _z = z
      .string()
      .min(config.min, _messages.min.replace("$1", config.min.toString()));

    if (config.max !== null) {
      _z = _z.max(
        config.max,
        _messages.max.replace("$1", config.max.toString()),
      );
    }

    if (config.letters) {
      _z = _z.regex(/^(?=.*[a-zA-Z])/, _messages.letters);
      if (config.mixedCase) {
        _z = _z.regex(/^(?=.*[a-z])(?=.*[A-Z])/, _messages.mixedCase);
      }
    }

    if (config.numbers) {
      _z = _z.regex(/^(?=.*[0-9])/, _messages.numbers);
    }

    if (config.symbols) {
      _z = _z.regex(/^(?=.*[!@#$%^&*])/, _messages.symbols);
    }

    return _z;
  };

  const generate = (option?: Partial<PasswordGenerateOptions>) => {
    const config = useAppConfig().mitd.password.rules;
    const options = Object.assign(
      {
        length: config.max
          ? Math.floor(
              Math.random() * (config.max - config.min + 1) + config.min,
            )
          : config.min,
        letters: config.letters,
        mixedCase: config.mixedCase,
        numbers: config.numbers,
        symbols: config.symbols,
        exclude: "",
        excludeSimilarCharacters: true,
        excludeSimilarCharactersThreshold: 3,
        excludeSimilarCharactersExclude: "oO0iIl1",
      },
      option,
    ) as PasswordGenerateOptions;

    const lowerCaseLetters = "abcdefghijklmnopqrstuvwxyz";
    const upperCaseLetters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const numbers = "0123456789";
    const specialCharacters = "!@#$%^&*()-_+=[]{}:;\"'<>,.?/";

    let characterSet = "";
    let password = "";

    if (options.letters) {
      characterSet += lowerCaseLetters;
      password += getRandomChar(lowerCaseLetters);
      if (options.mixedCase) {
        characterSet += upperCaseLetters;
        password += getRandomChar(upperCaseLetters);
      }
    }
    if (options.numbers) {
      characterSet += numbers;
      password += getRandomChar(numbers);
    }
    if (options.symbols) {
      characterSet += specialCharacters;
      password += getRandomChar(specialCharacters);
    }

    // Exclude specified characters
    if (options.exclude) {
      password = excludeChars(password, options.exclude!);
      characterSet = excludeChars(characterSet, options.exclude!);
    }

    // Exclude similar characters if specified
    if (options.excludeSimilarCharacters) {
      password = excludeSimilarCharacters(
        password,
        options.excludeSimilarCharactersExclude,
        options.excludeSimilarCharactersThreshold,
      );
      characterSet = excludeSimilarCharacters(
        characterSet,
        options.excludeSimilarCharactersExclude,
        options.excludeSimilarCharactersThreshold,
      );
    }

    if (characterSet.length === 0) {
      throw new Error("At least one character type must be selected");
    }

    while (password.length < options.length) {
      password += getRandomChar(characterSet);
    }

    return shuffleString(password);
  };

  const generator = (options?: {
    onGenerate?: (password: string) => void;
    iterations?: number;
    allowedScore?: number;
    options?:
      | Partial<PasswordGenerateOptions>
      | ComputedRef<Partial<PasswordGenerateOptions>>;
  }) => {
    const generating = ref(false);
    const password = ref("");
    return {
      generating,
      password,
      generate: async () => {
        let str = null;
        let theChosenOne = "";
        let _iterations = options?.iterations || 10;
        const _score = Math.min(99, Math.max(options?.allowedScore || 0));
        generating.value = true;

        while (!str || _iterations > 0 || str.score < _score) {
          password.value = generate(toValue(options?.options));
          options?.onGenerate?.(password.value);
          str = strength(password.value.trim());
          if (str.score >= 60) {
            theChosenOne = password.value;
          }
          _iterations--;
          await new Promise((resolve) => setTimeout(resolve, 50));
        }
        if (theChosenOne) {
          password.value = theChosenOne;
          options?.onGenerate?.(password.value);
        }
        generating.value = false;
      },
    };
  };

  // Extracted from: https://uic.edu/apps/strong-password/
  // TODO: Breakdown into smaller functions
  const strength = (pwd: string) => {
    let nScore = 0,
      nLength = 0,
      nAlphaUC = 0,
      nAlphaLC = 0,
      nNumber = 0,
      nSymbol = 0,
      nMidChar = 0,
      nRequirements = 0,
      nUnqChar = 0,
      nRepChar = 0,
      nRepInc = 0,
      nConsecAlphaUC = 0,
      nConsecAlphaLC = 0,
      nConsecNumber = 0,
      nConsecSymbol = 0,
      nConsecCharType = 0,
      nSeqAlpha = 0,
      nSeqNumber = 0,
      nSeqSymbol = 0,
      nSeqChar = 0,
      nReqChar = 0;
    let nMultMidChar = 2,
      nMultConsecAlphaUC = 2,
      nMultConsecAlphaLC = 2,
      nMultConsecNumber = 2;
    let nMultSeqAlpha = 3,
      nMultSeqNumber = 3,
      nMultSeqSymbol = 3;
    let nMultLength = 4,
      nMultNumber = 4;
    let nMultSymbol = 6;
    let nTmpAlphaUC = "",
      nTmpAlphaLC = "",
      nTmpNumber = "",
      nTmpSymbol = "";
    let sAlphas = "abcdefghijklmnopqrstuvwxyz";
    let sNumerics = "01234567890";
    let sSymbols = ")!@#$%^&*()";
    let sComplexity = "Too Short";
    let sColor = "#FF4C4C";
    let nMinPwdLen = 8;
    let bonuses = {
      addition: {
        nLengthBonus: 0,
        nAlphaUCBonus: 0,
        nAlphaLCBonus: 0,
        nNumberBonus: 0,
        nSymbolBonus: 0,
        nMidCharBonus: 0,
        nRequirementsBonus: 0,
      },
      deduction: {
        nAlphasOnlyBonus: 0,
        nNumbersOnlyBonus: 0,
        nRepCharBonus: 0,
        nConsecAlphaUCBonus: 0,
        nConsecAlphaLCBonus: 0,
        nConsecNumberBonus: 0,
        nSeqAlphaBonus: 0,
        nSeqNumberBonus: 0,
        nSeqSymbolBonus: 0,
      },
    };
    if (pwd) {
      nScore = pwd.length * nMultLength;
      nLength = pwd.length;
      let arrPwd = pwd.replace(/\s+/g, "").split(/\s*/);
      let arrPwdLen = arrPwd.length;

      /* Loop through password to check for Symbol, Numeric, Lowercase and Uppercase pattern matches */
      for (let a = 0; a < arrPwdLen; a++) {
        if (arrPwd[a].match(/[A-Z]/g)) {
          if (nTmpAlphaUC !== "") {
            if (parseInt(nTmpAlphaUC) + 1 == a) {
              nConsecAlphaUC++;
              nConsecCharType++;
            }
          }
          nTmpAlphaUC = `${a}`;
          nAlphaUC++;
        } else if (arrPwd[a].match(/[a-z]/g)) {
          if (nTmpAlphaLC !== "") {
            if (parseInt(nTmpAlphaLC) + 1 == a) {
              nConsecAlphaLC++;
              nConsecCharType++;
            }
          }
          nTmpAlphaLC = `${a}`;
          nAlphaLC++;
        } else if (arrPwd[a].match(/[0-9]/g)) {
          if (a > 0 && a < arrPwdLen - 1) {
            nMidChar++;
          }
          if (nTmpNumber !== "") {
            if (parseInt(nTmpNumber) + 1 == a) {
              nConsecNumber++;
              nConsecCharType++;
            }
          }
          nTmpNumber = `${a}`;
          nNumber++;
        } else if (arrPwd[a].match(/[^a-zA-Z0-9_]/g)) {
          if (a > 0 && a < arrPwdLen - 1) {
            nMidChar++;
          }
          if (nTmpSymbol !== "") {
            if (parseInt(nTmpSymbol) + 1 == a) {
              nConsecSymbol++;
              nConsecCharType++;
            }
          }
          nTmpSymbol = `${a}`;
          nSymbol++;
        }
        /* Internal loop through password to check for repeat characters */
        let bCharExists = false;
        for (let b = 0; b < arrPwdLen; b++) {
          if (arrPwd[a] == arrPwd[b] && a != b) {
            /* repeat character exists */
            bCharExists = true;
            /*
              Calculate icrement deduction based on proximity to identical characters
              Deduction is incremented each time a new match is discovered
              Deduction amount is based on total password length divided by the
              difference of distance between currently selected match
            */
            nRepInc += Math.abs(arrPwdLen / (b - a));
          }
        }
        if (bCharExists) {
          nRepChar++;
          nUnqChar = arrPwdLen - nRepChar;
          nRepInc = nUnqChar
            ? Math.ceil(nRepInc / nUnqChar)
            : Math.ceil(nRepInc);
        }
      }

      /* Check for sequential alpha string patterns (forward and reverse) */
      for (let s = 0; s < 23; s++) {
        let sFwd = sAlphas.substring(s, s + 3);
        let sRev = strReverse(sFwd);
        if (
          pwd.toLowerCase().indexOf(sFwd) != -1 ||
          pwd.toLowerCase().indexOf(sRev) != -1
        ) {
          nSeqAlpha++;
          nSeqChar++;
        }
      }

      /* Check for sequential numeric string patterns (forward and reverse) */
      for (let s = 0; s < 8; s++) {
        let sFwd = sNumerics.substring(s, s + 3);
        let sRev = strReverse(sFwd);
        if (
          pwd.toLowerCase().indexOf(sFwd) != -1 ||
          pwd.toLowerCase().indexOf(sRev) != -1
        ) {
          nSeqNumber++;
          nSeqChar++;
        }
      }

      /* Check for sequential symbol string patterns (forward and reverse) */
      for (let s = 0; s < 8; s++) {
        let sFwd = sSymbols.substring(s, s + 3);
        let sRev = strReverse(sFwd);
        if (
          pwd.toLowerCase().indexOf(sFwd) != -1 ||
          pwd.toLowerCase().indexOf(sRev) != -1
        ) {
          nSeqSymbol++;
          nSeqChar++;
        }
      }

      /* Modify overall score value based on usage vs requirements */

      /* General point assignment */
      bonuses.addition.nLengthBonus = nScore;
      if (nAlphaUC > 0 && nAlphaUC < nLength) {
        bonuses.addition.nAlphaUCBonus = (nLength - nAlphaUC) * 2;
        nScore += bonuses.addition.nAlphaUCBonus;
      }
      if (nAlphaLC > 0 && nAlphaLC < nLength) {
        bonuses.addition.nAlphaLCBonus = (nLength - nAlphaLC) * 2;
        nScore += bonuses.addition.nAlphaLCBonus;
      }
      if (nNumber > 0 && nNumber < nLength) {
        bonuses.addition.nNumberBonus = nNumber * nMultNumber;
        nScore += bonuses.addition.nNumberBonus;
      }
      if (nSymbol > 0) {
        bonuses.addition.nSymbolBonus = nSymbol * nMultSymbol;
        nScore += bonuses.addition.nSymbolBonus;
      }
      if (nMidChar > 0) {
        bonuses.addition.nMidCharBonus = nMidChar * nMultMidChar;
        nScore += bonuses.addition.nMidCharBonus;
      }

      /* Point deductions for poor practices */
      if ((nAlphaLC > 0 || nAlphaUC > 0) && nSymbol === 0 && nNumber === 0) {
        // Only Letters
        bonuses.deduction.nAlphasOnlyBonus = nLength;
        nScore -= bonuses.deduction.nAlphasOnlyBonus;
      }
      if (nAlphaLC === 0 && nAlphaUC === 0 && nSymbol === 0 && nNumber > 0) {
        // Only Numbers
        bonuses.deduction.nNumbersOnlyBonus = nLength;
        nScore -= bonuses.deduction.nNumbersOnlyBonus;
      }
      if (nRepChar > 0) {
        // Same character exists more than once
        bonuses.deduction.nRepCharBonus = nRepInc;
        nScore -= bonuses.deduction.nRepCharBonus;
      }
      if (nConsecAlphaUC > 0) {
        // Consecutive Uppercase Letters exist
        bonuses.deduction.nConsecAlphaUCBonus =
          nConsecAlphaUC * nMultConsecAlphaUC;
        nScore -= bonuses.deduction.nConsecAlphaUCBonus;
      }
      if (nConsecAlphaLC > 0) {
        // Consecutive Lowercase Letters exist
        bonuses.deduction.nConsecAlphaLCBonus =
          nConsecAlphaLC * nMultConsecAlphaLC;
        nScore -= bonuses.deduction.nConsecAlphaLCBonus;
      }
      if (nConsecNumber > 0) {
        // Consecutive Numbers exist
        bonuses.deduction.nConsecNumberBonus =
          nConsecNumber * nMultConsecNumber;
        nScore -= bonuses.deduction.nConsecNumberBonus;
      }
      if (nSeqAlpha > 0) {
        // Sequential alpha strings exist (3 characters or more)
        bonuses.deduction.nSeqAlphaBonus = nSeqAlpha * nMultSeqAlpha;
        nScore -= bonuses.deduction.nSeqAlphaBonus;
      }
      if (nSeqNumber > 0) {
        // Sequential numeric strings exist (3 characters or more)
        bonuses.deduction.nSeqNumberBonus = nSeqNumber * nMultSeqNumber;
        nScore -= bonuses.deduction.nSeqNumberBonus;
      }
      if (nSeqSymbol > 0) {
        // Sequential symbol strings exist (3 characters or more)
        bonuses.deduction.nSeqSymbolBonus = nSeqSymbol * nMultSeqSymbol;
        nScore -= bonuses.deduction.nSeqSymbolBonus;
      }

      /* Determine if mandatory requirements have been met and set image indicators accordingly */
      let arrChars = [nLength, nAlphaUC, nAlphaLC, nNumber, nSymbol];
      let arrCharsIds = [
        "nLength",
        "nAlphaUC",
        "nAlphaLC",
        "nNumber",
        "nSymbol",
      ];
      for (let c = 0; c < arrChars.length; c++) {
        let minVal = 0;
        if (arrCharsIds[c] == "nLength") {
          minVal = nMinPwdLen - 1;
        }
        if (arrChars[c] == minVal + 1) {
          nReqChar++;
        } else if (arrChars[c] > minVal + 1) {
          nReqChar++;
        }
      }
      nRequirements = nReqChar;
      let nMinReqChars = 4;
      if (pwd.length >= nMinPwdLen) {
        nMinReqChars = 3;
      }
      if (nRequirements > nMinReqChars) {
        // One or more required characters exist
        bonuses.addition.nRequirementsBonus = nRequirements * 2;
        nScore += bonuses.addition.nRequirementsBonus;
      }

      /* Determine complexity based on overall score */
      if (nScore > 100) {
        nScore = 100;
      } else if (nScore < 0) {
        nScore = 0;
      }
      if (nScore >= 0 && nScore < 20) {
        sComplexity = "Very Weak";
        sColor = "#FF4C4C";
      } else if (nScore >= 20 && nScore < 40) {
        sComplexity = "Weak";
        sColor = "#FFA500";
      } else if (nScore >= 40 && nScore < 60) {
        sComplexity = "Good";
        sColor = "#FFD700";
      } else if (nScore >= 60 && nScore < 80) {
        sComplexity = "Strong";
        sColor = "#4CAF50";
      } else if (nScore >= 80 && nScore <= 100) {
        sComplexity = "Very Strong";
        sColor = "#007BFF";
      }
    }
    return {
      score: nScore,
      complexity: sComplexity,
      color: sColor,
      bonuses,
    };
  };

  const strReverse = (str: string): string => {
    return str.split("").reverse().join("");
  };

  const excludeSimilarCharacters = (
    s: string,
    exclude: string,
    threshold: number,
  ): string => {
    const similarCharacters = new Set(exclude.split(""));
    let excludedCount = 0;

    // Convert input to an array of characters
    const result = s.split("").filter((char) => {
      if (similarCharacters.has(char)) {
        excludedCount++;
        return excludedCount <= threshold; // Exclude if below the threshold
      }
      return true; // Keep other characters
    });

    return result.join("");
  };

  const getRandomChar = (chars: string): string => {
    return chars[Math.floor(Math.random() * chars.length)];
  };

  const shuffleString = (str: string): string => {
    return str
      .split("")
      .sort(() => Math.random() - 0.5)
      .join("");
  };

  const excludeChars = (s: string, chars: string): string => {
    return s
      .split("")
      .filter((char) => !chars.includes(char))
      .join("");
  };

  return {
    passwordRules,
    generate,
    strength,
    generator,
  };
};
