<template>
  <div class="divider">
    <div class="divider__line" :style="lineStyles"></div>
    <div class="divider__label overline">{{ label }}</div>
    <div class="divider__line" :style="lineStyles"></div>
  </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';

@Component
export default class LabeledDivider extends Vue {
  @Prop({ type: String, default: 'or' }) private readonly label !: string;

  get color() {
    return this.$vuetify.theme.dark ? 'rgba(255,255,255,0.3)' : 'rgba(0, 0, 0, 0.2)';
  }

  get lineStyles() {
    return { background: this.color };
  }
}
</script>

<style lang="scss" scoped>
.divider {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 24px 0;

  .divider__line {
    height: 1px;
    flex: 1;
  }
  .divider__label {
    padding: 4px 12px;
  }
}
</style>
