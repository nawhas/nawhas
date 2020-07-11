import { NavigationGuard, RawLocation } from 'vue-router';
import { Role } from '@/entities/user';
import { Getters as AuthGetters } from '@/store/modules/auth';
import store from '@/store';

export function enforceRole(role: Role, redirect: RawLocation = '/'): NavigationGuard {
  return ((to, from, next) => {
    if (store.getters[AuthGetters.Role] !== role) {
      next(redirect);
      return;
    }
    next();
  });
}
