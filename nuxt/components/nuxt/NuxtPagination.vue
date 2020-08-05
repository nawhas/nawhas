<script>
/**
 * @see https://github.com/vuetifyjs/vuetify/issues/4855
 */
import Vue from 'vue';
const VPagination = Vue.component('VPagination');
export default {
  name: 'NuxtPagination',
  mixins: [VPagination],
  props: {
    toGenerator: {
      type: Function,
      required: true,
    },
  },
  methods: {
    next(e) {
      e.preventDefault();
      this.$router.push(this.toGenerator(this.value + 1));
    },
    previous(e) {
      e.preventDefault();
      this.$router.push(this.toGenerator(this.value - 1));
    },
    genItem(h, i) {
      return h('nuxt-link', {
        attrs: {
          to: this.toGenerator(i),
        },
      }, [VPagination.options.methods.genItem.call(this, h, i)]);
    },
  },
};
</script>
