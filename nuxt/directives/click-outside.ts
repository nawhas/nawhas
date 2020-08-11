/* eslint-disable no-param-reassign,no-underscore-dangle,dot-notation */
import { DirectiveOptions } from 'vue';

export interface Activator {
  (): boolean;
}
export interface ClickOutsideConfiguration {
  active: Activator;
  handler: Function;
}

const ClickOutside: DirectiveOptions = {
  bind(el, binding) {
    // Provided expression must evaluate to a function.
    if (typeof binding.value !== 'object') {
      const warn = `[ClickOutside] Provided expression '${binding.expression}' is not an object, but has to be`;
      // eslint-disable-next-line no-console
      console.warn(warn);
    }

    // Define Handler and cache it on the element
    const { bubble } = binding.modifiers;
    const handler = (e) => {
      if (!binding.value.active()) {
        return;
      }

      if (bubble || (!el.contains(e.target) && el !== e.target)) {
        binding.value.handler(e);
      }
    };
    el['__vueClickOutside__'] = handler;

    // add Event Listeners
    document.addEventListener('click', handler);
  },

  unbind(el) {
    // Remove Event Listeners
    document.removeEventListener('click', el['__vueClickOutside__']);
    el['__vueClickOutside__'] = null;
  },
};

export default ClickOutside;
