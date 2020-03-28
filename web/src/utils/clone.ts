export function clone(value: any): any {
  return JSON.parse(JSON.stringify(value));
}
