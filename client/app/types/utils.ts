export type HasKey = {
  [key: string]: any;
};

export type StringList = Array<string>;

export type Prettify<T> = { [K in keyof T]: T[K] };
