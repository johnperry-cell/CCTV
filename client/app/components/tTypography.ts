import { h, type PropType } from "vue";
import { twMerge, type ClassNameValue } from "tailwind-merge";

const variantClassName = {
  h1: "text-[1.75rem] leading-10",
  h2: "text-[1.625rem] leading-9",
  h3: "text-[1.5rem] leading-8",
  h4: "text-[1.375rem] leading-8",
  h5: "text-[1.25rem] leading-7",
  h6: "text-[1.125rem] leading-7",
  xs: "text-xs leading-6 ",
  sm: "text-sm leading-6 ",
  md: "text-base leading-6 ",
  lg: "text-lg leading-8 ",
  xl: "text-2xl leading-10 ",
  overline: "text-sm font-bold tracking-[3.2px] uppercase",
  span: "text-sm leading-5",
};

type Variant = keyof typeof variantClassName;

export default defineComponent({
  name: "t-typography",
  props: {
    variant: {
      type: String as PropType<Variant>,
      default: "md",
    },
    class: {
      type: String as PropType<ClassNameValue>,
      default: "",
    },
  },
  setup: (props, { slots }) => {
    const getElementType = (variant: Variant) => {
      const v = variant.toLowerCase();
      if (["h1", "h2", "h3", "h4", "h5", "h6"].indexOf(v) > -1) {
        return v;
      }
      if (["xs", "sm", "md", "lg", "xl"].indexOf(v) > -1) {
        return "p";
      }
      return "span";
    };

    return () =>
      h(
        getElementType(props.variant),
        {
          class: twMerge(variantClassName[props.variant], props.class),
        },
        slots.default?.(),
      );
  },
});
