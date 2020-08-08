import { VueRouter } from 'vue-router/types/router';
import { getCurrentInstance } from '@vue/composition-api';

interface UseRouterReturn { router: VueRouter }

export default function useRouter(): UseRouterReturn {
  const vm = getCurrentInstance();

  if (!vm) {
    throw new Error('This must be called within a setup function.');
  }

  return {
    router: vm.$router,
  };
};
