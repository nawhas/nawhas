<template>
  <div v-click-outside="clickOutsideConfig">
    <v-btn v-if="mobile" icon @click="activate"><v-icon>search</v-icon></v-btn>
    <div v-show="!mobile || activated"
         :class="{
           search: true,
           'search--focused': activated,
           'search--focused--dark': isDark && activated
          }">
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
                      :class="{'search__input': true, 'search__input--dark': isDark}"
        ></v-text-field>
      </div>
      <v-expand-transition>
        <div class="search__container"
            :style="{
              background: $vuetify.theme.currentTheme.background,
            }"
            v-show="activated">
          <index-hits :client="client" :search="search" :index="indices.reciters" caption="Reciters">
            <reciter-result slot-scope="{ item }" :reciter="item" />
          </index-hits>
          <index-hits :client="client" :search="search" :index="indices.tracks" caption="Tracks">
            <track-result slot-scope="{ item }" :track="item" />
            <template v-slot:configure>
              <ais-configure :query="search"
                             :hits-per-page.camel="4"
                             :attributesToSnippet="['lyrics']"
                             :distinct="true"
              />
            </template>
          </index-hits>
          <div class="search__footer" :class="{ 'white--text': isDark }">
            <div class="search__footer-hint body-2" v-if="!search">
              Start typing to see results...
            </div>
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
import { ALGOLIA_APP_ID, ALGOLIA_SEARCH_KEY, ALGOLIA_INDEX_PREFIX } from '@/config';
import IndexHits from '@/components/search/IndexHits.vue';
import ReciterResult from '@/components/search/ReciterResult.vue';
import AlbumResult from '@/components/search/AlbumResult.vue';
import TrackResult from '@/components/search/TrackResult.vue';
import ClickOutside, { ClickOutsideConfiguration } from '@/directives/click-outside';
import { EventBus, Search } from '@/events';

@Component({
  components: {
    ReciterResult,
    AlbumResult,
    TrackResult,
    IndexHits,
  },
  directives: {
    ClickOutside,
  },
})
export default class GlobalSearch extends Vue {
  private client = algolia(ALGOLIA_APP_ID, ALGOLIA_SEARCH_KEY)
  private activated = false;
  private focused = false;
  private search = '';
  private listener: Function|null = null;
  @Ref('search') readonly input!: HTMLElement;

  get indices() {
    return {
      reciters: `${ALGOLIA_INDEX_PREFIX}reciters`,
      tracks: `${ALGOLIA_INDEX_PREFIX}tracks`,
    };
  }

  get mobile() {
    return this.$vuetify.breakpoint.smAndDown;
  }

  get isDark() {
    return this.$vuetify.theme.dark;
  }

  mounted() {
    this.listener = () => {
      this.$nextTick(() => this.activate());
    };

    EventBus.$on(Search.TRIGGER, this.listener);
  }

  @Watch('$route')
  onRouteChange() {
    this.resetSearch();
  }

  @Watch('focused')
  onFocusChange(focused) {
    if (focused) {
      this.activated = true;
    }
    if (!focused && !this.search) {
      this.activated = false;
    }
  }

  onBlur() {
    this.focused = false;
  }

  onEsc() {
    this.input.blur();
  }

  onFocus() {
    this.focused = true;
  }

  get clickOutsideConfig(): ClickOutsideConfiguration {
    return {
      active: () => !!this.search && this.activated,
      handler: () => this.activated = false,
    };
  }

  resetSearch() {
    this.search = '';
    this.focused = false;
    this.activated = false;
  }

  activate() {
    this.focused = true;
    this.$nextTick(() => {
      this.input.focus();
    });
  }

  beforeDestroy() {
    if (this.listener) {
      EventBus.$off(Search.TRIGGER, this.listener);
    }
  }
}
</script>

<style lang="scss" scoped>
@import '~vuetify/src/styles/styles';

$width: 400px;
.search {
  $easing: cubic-bezier(0.4, 0, 0.2, 1);
  $duration: 280ms;
  will-change: background-color, box-shadow;
  transition: background-color $duration $easing, box-shadow $duration $easing;
  display: flex;
  flex-direction: column;
  background: rgba(0, 0, 0, 0.1);
  width: $width;
  border-radius: 4px;
  margin-top: 4px;

  &--focused {
    background-color: white;
    @include elevation(4);
  }
  &--focused--dark {
    background-color: map-get($material-dark-elevation-colors, '8');
    @include elevation(4);
  }

  .search__bar {
    display: flex;
    flex-direction: row;
    align-items: center;

    .search__input {
      // background-color: transparent;
    }

    .search__input--dark {
      background-color: map-get($material-dark-elevation-colors, '8');
    }
  }

  .search__container {
    width: $width;
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

@media #{map-get($display-breakpoints, 'sm-and-down')} {
  .search {
    width: 100%;
    margin-top: 0;

    &--focused {
      position: fixed;
      top: 0;
      left: 0;
      z-index: 1;
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
