/* eslint-disable no-param-reassign,no-underscore-dangle,dot-notation */
import { DirectiveOptions } from 'vue';

const ClickOutside: DirectiveOptions = {
  bind(el, binding) {
    // Provided expression must evaluate to a function.
    if (typeof binding.value !== 'function') {
      const warn = `[ClickOutside] Provided expression '${binding.expression}' is not a function, but has to be`;
      console.warn(warn);
    }

    // Define Handler and cache it on the element
    const { bubble } = binding.modifiers;
    const handler = (e) => {
      if (bubble || (!el.contains(e.target) && el !== e.target)) {
        binding.value(e);
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
