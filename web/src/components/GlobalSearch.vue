<template>
  <div :class="{ search: true, 'search--focused': focused }">
    <v-text-field solo flat single-line hide-details
                  background-color="transparent"
                  prepend-inner-icon="search"
                  ref="search"
                  clearable
                  @focus="onFocus"
                  @blur="onBlur"
                  @keydown.esc="onEsc"
                  placeholder="Search for nawhas, reciters, or lyrics..."
                  v-model="search"
                  class="search__input"
    ></v-text-field>
    <v-expand-transition>
      <div class="search__container" v-show="focused">
        <ais-instant-search :search-client="client" index-name="reciters">
          <ais-configure :query="search" :hits-per-page.camel="4" :distinct="true" />
          <ais-state-results>
            <div class="search__hits" slot-scope="{ nbHits }" v-if="nbHits > 0 && search">
              <div class="search__hits__heading caption">Reciters</div>
              <ais-hits :escapeHTML="false">
                <template slot-scope="{ items }">
                  <div class="search__hit search__hit--reciter"
                       v-for="(item, index) in items" :key="index">
                    <reciter-result @selected="onSelect" :reciter="item" />
                  </div>
                </template>
              </ais-hits>
            </div>
            <div class="search__hits--empty" v-else></div>
          </ais-state-results>
        </ais-instant-search>
<!--        <ais-instant-search :search-client="client" index-name="albums">-->
<!--          <ais-configure :query="search" :hits-per-page.camel="4" :distinct="true" />-->
<!--          <ais-state-results>-->
<!--            <div class="search__hits" slot-scope="{ nbHits }" v-if="nbHits > 0 && search">-->
<!--              <div class="search__hits__heading caption">Albums</div>-->
<!--              <ais-hits :escapeHTML="false">-->
<!--                <template slot-scope="{ items }">-->
<!--                  <div class="search__hit search__hit&#45;&#45;album"-->
<!--                       v-for="(item, index) in items" :key="index">-->
<!--                    <album-result @selected="onSelect" :album="item" />-->
<!--                  </div>-->
<!--                </template>-->
<!--              </ais-hits>-->
<!--            </div>-->
<!--            <div class="search__hits&#45;&#45;empty" v-else></div>-->
<!--          </ais-state-results>-->
<!--        </ais-instant-search>-->
        <ais-instant-search :search-client="client" index-name="tracks">
          <ais-configure :query="search" :hits-per-page.camel="4" :distinct="true" />
          <ais-state-results>
            <div class="search__hits" slot-scope="{ nbHits }" v-if="nbHits > 0 && search">
              <div class="search__hits__heading caption">Tracks</div>
              <ais-hits :escapeHTML="false">
                <template slot-scope="{ items }">
                  <div class="search__hit search__hit--track"
                       v-for="(item, index) in items" :key="index">
                    <track-result @selected="onSelect" :track="item" />
                  </div>
                </template>
              </ais-hits>
            </div>
            <div class="search__hits--empty" v-else></div>
          </ais-state-results>
        </ais-instant-search>
        <div class="search__footer">
          <div class="body-2" v-if="!search">Start typing to see results...</div>
          <div class="body-2" v-else></div>
          <img :src="require('../assets/search-by-algolia.svg')" alt="Search by Algolia"/>
        </div>
      </div>
    </v-expand-transition>
  </div>
</template>

<script lang="ts">
import { Component, Ref, Vue } from 'vue-property-decorator';
import algolia from 'algoliasearch/lite';
import { RawLocation } from 'vue-router';
import { ALGOLIA_APP_ID, ALGOLIA_SEARCH_KEY } from '@/config';
import ReciterResult from '@/components/search/ReciterResult.vue';
import AlbumResult from '@/components/search/AlbumResult.vue';
import TrackResult from '@/components/search/TrackResult.vue';

@Component({
  components: {
    ReciterResult,
    AlbumResult,
    TrackResult,
  },
})
export default class GlobalSearch extends Vue {
  private client = algolia(ALGOLIA_APP_ID, ALGOLIA_SEARCH_KEY)
  private focused = false;
  private search = '';
  private searching = false;
  private timeout: number|undefined = undefined;
  @Ref('search') readonly input!: HTMLElement;

  onBlur() {
    this.focused = false;
  }
  onEsc() {
    this.input.blur();
  }
  onFocus() {
    window.clearTimeout(this.timeout);

    this.focused = true;
  }
  resetSearch(timeout = 0) {
    window.clearTimeout(this.timeout);

    this.$nextTick(() => {
      this.timeout = window.setTimeout(() => {
        this.focused = false;
        this.searching = false;
        this.search = '';
      }, timeout);
    });
  }
  onSelect(route: RawLocation) {
    this.resetSearch();
    this.$router.push(route);
  }
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

    .search__footer {
      color: rgba(0, 0, 0, 0.3);
      display: flex;
      justify-content: space-between;
      padding: 8px;
      img {
        height: 16px;
        width: auto;
      }
    }
  }
}

.search__hits {
  margin-bottom: 8px;
  .search__hits__heading {
    padding: 8px 16px;
    background-color: rgba(0,0,0,0.03);
    font-weight: 500;
  }
}
</style>
