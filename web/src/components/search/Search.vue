<template>
  <div>
    <v-btn v-if="mobile" icon @click="activate"><v-icon>search</v-icon></v-btn>
    <div v-show="!mobile || focused" :class="{ search: true, 'search--focused': focused }">
      <div class="search__bar">
        <v-btn v-if="mobile" icon @click="resetSearch"><v-icon>arrow_back</v-icon></v-btn>
        <v-text-field solo flat single-line hide-details
                      background-color="transparent"
                      :prepend-inner-icon="mobile ? undefined : 'search'"
                      ref="search"
                      :dense="false"
                      :full-width="mobile"
                      :clearable="!!search"
                      @focus="onFocus"
                      @blur="onBlur"
                      @keydown.esc="onEsc"
                      placeholder="Search for nawhas, reciters, or lyrics..."
                      v-model="search"
                      class="search__input"
        ></v-text-field>
      </div>
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
                      <reciter-result :reciter="item" />
                    </div>
                  </template>
                </ais-hits>
              </div>
              <div class="search__hits--empty" v-else></div>
            </ais-state-results>
          </ais-instant-search>
          <ais-instant-search :search-client="client" index-name="tracks">
            <ais-configure :query="search"
                           :hits-per-page.camel="4"
                           :attributesToSnippet="['lyrics']"
                           :distinct="true"
            />
            <ais-state-results>
              <div class="search__hits" slot-scope="{ nbHits }" v-if="nbHits > 0 && search">
                <div class="search__hits__heading caption">Tracks</div>
                <ais-hits :escapeHTML="false">
                  <template slot-scope="{ items }">
                    <div class="search__hit search__hit--track"
                         v-for="(item, index) in items" :key="index">
                      <track-result :track="item" />
                    </div>
                  </template>
                </ais-hits>
              </div>
              <div class="search__hits--empty" v-else></div>
            </ais-state-results>
          </ais-instant-search>
          <div class="search__footer">
            <div class="search__footer-hint body-2" v-if="!search">Start typing to see results...</div>
            <div class="body-2" v-else></div>
            <img src="../../assets/search-by-algolia.svg" alt="Search by Algolia"/>
          </div>
        </div>
      </v-expand-transition>
    </div>
  </div>
</template>

<script lang="ts">
import {
  Component, Ref, Vue, Watch,
} from 'vue-property-decorator';
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

  get mobile() {
    return this.$vuetify.breakpoint.smAndDown;
  }

  @Watch('$route')
  onRouteChange() {
    this.resetSearch();
  }

  onBlur() {
    window.clearTimeout(this.timeout);
    this.$nextTick(() => {
      this.timeout = window.setTimeout(() => {
        this.focused = false;
      }, 280);
    });
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
    this.$router.push(route);
    this.$nextTick(() => this.resetSearch());
  }

  activate() {
    this.focused = true;
    this.$nextTick(() => this.input.focus());
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

  .search__bar {
    display: flex;
    flex-direction: row;
    align-items: center;
  }

  .search__container {
    width: 450px;
    background-color: white;
    max-height: calc(100vh - 100px);
    overflow-y: auto;
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

@media #{map-get($display-breakpoints, 'sm-and-down')} {
  .search {
    width: 100%;
    margin-top: 0;

    &--focused {
      position: fixed;
      top: 0;
      left: 0;
    }
    .search__container {
      width: 100%;

      .search__footer-hint {
        display: none;
      }
    }
  }
}
</style>
