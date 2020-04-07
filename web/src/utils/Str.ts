export default class Str {
  constructor(private value: string) {}

  replace(search: string, replace: string): this {
    this.value.split(search).join(replace);
    return this;
  }

  get(): string {
    return this.value;
  }
}
