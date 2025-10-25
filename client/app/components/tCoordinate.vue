<script setup lang="ts">
import type { Coordinate } from "~/types";
import type { Ref } from "vue";
import type { FormError } from "#ui/types";
const props = withDefaults(
  defineProps<{
    modelValue?: Coordinate;
    disabled?: boolean;
    loading?: boolean;
    required?: boolean;
  }>(),
  {
    required: false,
    disabled: false,
    loading: false,
  },
);

const emit = defineEmits(["update:modelValue"]);

const formErrors = inject<Ref<FormError[]> | null>("form-errors", null);

const errorPaths = {
  main: ["longitude", "latitude"],
  custom: ["longitude.latitude", "latitude.longitude"],
  get all() {
    return [...this.main, ...this.custom];
  },
};
const errors = computed(() =>
  formErrors?.value.filter((fe) => errorPaths.all.indexOf(fe.path) > -1),
);

const longitude = computed({
  get: () => props.modelValue?.longitude,
  set: (val: number | null | undefined) =>
    emit(
      "update:modelValue",
      Object.assign({}, props.modelValue, { longitude: val }),
    ),
});

const latitude = computed({
  get: () => props.modelValue?.latitude,
  set: (val: number | null | undefined) =>
    emit(
      "update:modelValue",
      Object.assign({}, props.modelValue, { latitude: val }),
    ),
});

const error = computed(
  () => !(!formErrors || !formErrors.value || !formErrors.value.length),
);

const invalidCoord = computed(() => {
  return (
    error.value &&
    formErrors!.value.filter(
      (fe: FormError) => errorPaths.custom.indexOf(fe.path) > -1,
    ).length > 0
  );
});

const hasError = computed(() => {
  const _error =
    formErrors!.value.filter(
      (fe: FormError) => errorPaths.main.indexOf(fe.path) > -1,
    ).length > 0;
  return error.value && (invalidCoord.value || _error);
});

const formValid = (path: string): boolean | null => {
  return (hasError.value &&
    !!formErrors?.value!.find((fe) => fe.path == path)) ||
    invalidCoord.value
    ? true
    : null;
};
</script>

<template>
  <div class="relative flex items-end gap-2">
    <TFormGroup
      size="md"
      label="Longitude"
      name="longitude"
      class="flex-auto"
      :required
      :error="formValid('longitude')"
      :ui="{ error: 'absolute top-full mt-0 px-2 text-xs transition-all' }"
    >
      <TInput
        v-model="longitude"
        :disabled="loading || disabled"
        type="number"
        :ui="{ base: 'appearance-none' }"
      />
    </TFormGroup>
    <TFormGroup
      size="md"
      label="Latitude"
      name="latitude"
      class="flex-auto"
      :required
      :error="formValid('latitude')"
      :ui="{ error: 'absolute top-full mt-0 px-2 text-xs transition-all' }"
    >
      <TInput
        v-model="latitude"
        :disabled="loading || disabled"
        type="number"
        :ui="{ base: 'appearance-none' }"
      />
    </TFormGroup>
    <TFormGroup
      size="md"
      label=" "
      :ui="{ error: 'absolute top-full mt-0 px-2 text-xs transition-all' }"
    >
      <TTooltip text="Not yet implemented!">
        <TButton
          icon="tabler:map-pin"
          size="lg"
          color="neutral"
          :disabled="loading || disabled"
        />
      </TTooltip>
      <template v-if="hasError" #hint>
        <TPopover
          mode="hover"
          :popper="{ placement: 'right' }"
          class="flex items-center"
        >
          <TButton
            variant="ghost"
            color="red"
            icon="tabler:info-circle"
            :padded="false"
          />
          <template #panel>
            <div class="px-3 py-2">
              <div v-for="err in errors">
                <ul class="list-disc *:ml-5">
                  <li>
                    <TTypography variant="sm">
                      {{ err.message }}
                    </TTypography>
                  </li>
                </ul>
              </div>
            </div>
          </template>
        </TPopover>
      </template>
    </TFormGroup>

    <p
      v-if="hasError"
      class="absolute top-full mt-0 px-2 text-xs text-red-500 transition-all dark:text-red-400"
    >
      Invalid Coordinates!
    </p>
  </div>
</template>
