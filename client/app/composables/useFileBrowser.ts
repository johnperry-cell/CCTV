export type FileBrowserOptions = {
  multiple?: boolean;
  accept?: string;
  onChange: (e: Event) => void;
};

export const useFileBrowser = (
  options: FileBrowserOptions | ComputedRef<FileBrowserOptions>,
) => {
  const _options = computed<FileBrowserOptions>(() =>
    Object.assign(
      {
        accept: "*",
        multiple: false,
      },
      toValue(options),
    ),
  );

  const openFileBrowser = () => {
    const input = document.createElement("input");
    input.type = "file";
    input.multiple = _options.value.multiple!;
    input.accept = _options.value.accept!;
    input.addEventListener("change", _options.value.onChange);
    input.click();
  };

  return {
    openFileBrowser,
  };
};
