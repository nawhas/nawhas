<template>
  <div :class="{ search: true, 'search--focused': focused }">
    <v-text-field solo flat single-line hide-details
                  background-color="transparent"
                  prepend-inner-icon="search"
                  clearable
                  @focus="focused = true"
                  @blur="focused = false"
                  placeholder="Search for nawhas, reciters, albums, or lyrics..."
                  class="search__input">
    </v-text-field>
    <v-expand-transition>
      <div class="search__container" v-if="focused">
        <div class="search__branding">
          <img :src="require('../assets/search-by-algolia.svg')" alt="Search by Algolia"/>
        </div>
      </div>
    </v-expand-transition>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import algolia from 'algoliasearch/lite';
import { ALGOLIA_APP_ID, ALGOLIA_SEARCH_KEY } from '@/config';

@Component({
  components: {
    // AisInstantSearch,
  },
})
export default class GlobalSearch extends Vue {
  private client = algolia(ALGOLIA_APP_ID, ALGOLIA_SEARCH_KEY)

  private focused = false;
}
</script>

<style lang="scss" scoped>
@import '~vuetify/src/styles/styles';

.search {
  $easing: cubic-bezier(0.4, 0, 0.2, 1);
  $duration: 280ms;
  will-change: background-color, box-shadow;
  transition: background-color $duration $easing, box-shadow $duration $easing;
  display: flex;
  flex-direction: column;
  background: rgba(0, 0, 0, 0.1);
  width: 450px;
  border-radius: 4px;
  margin-top: 4px;

  &--focused {
    background-color: white;
    @include elevation(4);
  }

  .search__container {
    width: 450px;
    border-top: 1px solid rgba(0, 0, 0, 0.06);
    position: relative;

    .search__branding {
      display: flex;
      justify-content: flex-end;
      padding: 8px;
      img {
        height: 16px;
        width: auto;
      }
    }
  }

}
</style>
