export const useColors = () => {
  const randomColor = (alpha: boolean = false): string => {
    const color = Math.random() * 16777215 * (alpha ? 255 : 1);
    const rgb = Math.floor(color).toString(16);
    return `#${rgb}`;
  };

  const hexAToRGBA = (h: string) => {
    let r = 0,
      g = 0,
      b = 0,
      a = 255;

    const _h = h?.replace("#", "") ?? "000";

    if (_h.length == 4 || _h.length == 3) {
      r = parseInt(_h[0] + _h[0], 16);
      g = parseInt(_h[1] + _h[1], 16);
      b = parseInt(_h[2] + _h[2], 16);
      if (_h.length == 4) {
        a = parseInt(_h[3] + _h[3], 16);
      }
    } else if (_h.length == 8 || _h.length == 6) {
      r = parseInt(_h[0] + _h[1], 16);
      g = parseInt(_h[2] + _h[3], 16);
      b = parseInt(_h[4] + _h[5], 16);
      if (_h.length == 8) {
        a = parseInt(_h[6] + _h[7], 16);
      }
    }
    a = +(a / 255).toFixed(2);

    return {
      r,
      g,
      b,
      a,
      get toString() {
        return `rgba(${this.r},${this.g},${this.b},${this.a})`;
      },
    };
  };

  const RGBAToHexA = (r: number, g: number, b: number, a: number) => {
    let _r = r.toString(16);
    let _g = g.toString(16);
    let _b = b.toString(16);
    let _a = Math.round(a * 255).toString(16);

    if (_r.length == 1) _r = "0" + _r;
    if (_g.length == 1) _g = "0" + _g;
    if (_b.length == 1) _b = "0" + _b;
    if (_a.length == 1) _a = "0" + _a;

    return "#" + _r + _g + _b + _a;
  };

  const stringToColour = (str: string) => {
    let hash = 0;
    str.split("").forEach((char) => {
      hash = char.charCodeAt(0) + ((hash << 5) - hash);
    });
    let colour = "#";
    for (let i = 0; i < 3; i++) {
      const value = (hash >> (i * 8)) & 0xff;
      colour += value.toString(16).padStart(2, "0");
    }
    // Add 60% (99 code) opacity
    return colour + "99";
  };

  const brightness = (hex: string) => {
    const rgb = hexAToRGBA(hex);
    return (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
  };

  const isDark = (hex: string) => {
    const rgba = hexAToRGBA(hex);
    return rgba.a >= 0.5 ? brightness(hex) < 128 : false;
  };

  return {
    randomColor,
    hexAToRGBA,
    RGBAToHexA,
    stringToColour,
    brightness,
    isDark,
  };
};
