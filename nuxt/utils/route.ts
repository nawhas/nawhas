import { Route } from 'vue-router';

export function getPage(route: Route): number {
  const page = route.query.page;

  if (Array.isArray(page)) {
    return Number(page.pop() ?? 1);
  }

  return Number(page ?? 1);
}
