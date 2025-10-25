import type { LogLevel } from "~/types/utils/logger";

function createLogger(emojiSet: string = "classic") {
  const emojis: Record<string, Record<LogLevel, string>> = {
    professional: {
      log: "📝",
      warn: "⚠️",
      error: "❌",
      info: "ℹ️",
    },
    colors: {
      log: "🔵",
      warn: "🟠",
      error: "🔴",
      info: "🟢",
    },
    minimalist: {
      log: "➖",
      warn: "⚡",
      error: "🔥",
      info: "💡",
    },
    nature: {
      log: "🌳",
      warn: "🌞",
      error: "🌧️",
      info: "🌟",
    },
    techie: {
      log: "💻",
      warn: "🛠️",
      error: "🖥️",
      info: "🧑‍💻",
    },
    cartoonish: {
      log: "🦸",
      warn: "🦹",
      error: "💥",
      info: "🎯",
    },
    classic: {
      log: "📜",
      warn: "⚠️",
      error: "🚨",
      info: "📣",
    },
    retro: {
      log: "📼",
      warn: "🕹️",
      error: "💣",
      info: "📟",
    },
    space: {
      log: "🚀",
      warn: "🪐",
      error: "🌑",
      info: "🪐",
    },
    food: {
      log: "🍞",
      warn: "🌶️",
      error: "🍔",
      info: "🍏",
    },
  };

  const emoSet = emojis[emojiSet] ?? emojis.retro;

  const log = (..._: any[]) => {};
  const error = (..._: any[]) => {};
  const warn = (..._: any[]) => {};
  const info = (..._: any[]) => {};

  if (process.env.NODE_ENV === "development") {
    return {
      log: console.log.bind(console, emoSet?.log),
      error: console.error.bind(console, emoSet?.error),
      warn: console.warn.bind(console, emoSet?.warn),
      info: console.info.bind(console, emoSet?.info),
    };
  }

  return { log, warn, error, info };
}

export const { log, warn, error, info } = createLogger();
