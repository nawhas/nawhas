<template>
  <div v-if="search && results.length > 0" class="search__hits">
    <div class="search__hits__heading caption">
      {{ heading }}
    </div>
    <div
      v-for="(item, index) in results"
      :key="index"
      class="search__hit search__hit--reciter"
    >
      <slot v-bind="{ item, index }" />
    </div>
  </div>
  <div v-else class="search__hits--empty" />
</template>

<script lang="ts">
import { Vue, Component, Prop, Watch } from 'nuxt-property-decorator';

import { MeiliSearch, Index } from 'meilisearch';

@Component
export default class IndexResults extends Vue {
  @Prop({ type: Object, required: true }) private client!: MeiliSearch;
  @Prop({ type: String, required: false }) private search?: string;
  @Prop({ type: String, required: true }) private collection!: string;
  @Prop({ type: String, required: true }) private heading!: string;
  @Prop({ type: Number, required: false, default: 4 }) private limit!: number;
  @Prop({ type: Array, required: false, default: () => [] }) private highlight!: Array<string>;
  @Prop({ type: Array, required: false, default: () => [] }) private crop!: Array<string>;
  private index!: Index<any>;
  private results: Array<any> = [];

  created() {
    this.index = this.client.index(this.collection);
  }

  @Watch('search')
  async onSearch() {
    if (!this.search) {
      this.results = [];
      return;
    }

    const response = await this.index.search(this.search, {
      limit: this.limit,
      attributesToHighlight: this.highlight,
      attributesToCrop: this.crop,
    });
    this.results = response.hits;
  }
}
</script>

<style lang="scss" scoped>
.search__hits {
  margin-bottom: 8px;

  .search__hits__heading {
    padding: 8px 16px;
    background-color: rgba(0,0,0,0.03);
    font-weight: 500;
  }
}
</style>
