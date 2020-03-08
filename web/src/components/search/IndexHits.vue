<template>
  <ais-instant-search :search-client="client" :index-name="index">
    <slot name="configure">
      <ais-configure :query="search" :hits-per-page.camel="4" :distinct="true" />
    </slot>
    <ais-state-results>
      <div class="search__hits" slot-scope="{ nbHits }" v-if="nbHits > 0 && search">
        <div class="search__hits__heading caption">{{ caption }}</div>
        <ais-hits :escapeHTML="false">
          <template slot-scope="{ items }">
            <div class="search__hit search__hit--reciter"
                 v-for="(item, index) in items" :key="index">
              <slot v-bind="{ item, index }"></slot>
            </div>
          </template>
        </ais-hits>
      </div>
      <div class="search__hits--empty" v-else></div>
    </ais-state-results>
  </ais-instant-search>
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'vue-property-decorator';

@Component
export default class IndexHits extends Vue {
  @Prop({ type: Object }) private client;
  @Prop({ type: String }) private search;
  @Prop({ type: String }) private index;
  @Prop({ type: String }) private caption;
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
