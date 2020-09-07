<template>
  <v-card :class="classes" outlined>
    <div :to="revision.meta.link" class="revision-card__content">
      <div class="revision-card__avatar">
        <v-avatar size="40" class="avatar">
          <v-icon>{{ icon }}</v-icon>
        </v-avatar>
      </div>
      <div class="revision-card__text">
        <div class="revision-card__title body-1">
          <div class="mr-1">
            {{ title }}
          </div>
          <div v-if="subtitle" class="body-2 text--disabled" v-text="subtitle" />
          <v-btn
            small
            icon
            :to="revision.meta.link"
            target="_blank"
          >
            <v-icon small>
              open_in_new
            </v-icon>
          </v-btn>
        </div>
        <div class="revision-card__name change-type-container">
          <v-avatar :color="indicator" size="12" class="change-type-icon" />
          <span class="change-type overline">{{ revision.changeType }}</span>
        </div>
      </div>
      <div class="revision-card__text revision-card__text-right">
        <div class="revision-card__name caption" :title="revision.createdAt | localDate">
          {{ revision.createdAt | relativeDate }}
        </div>
        <div class="revision-card__name caption">
          by {{ getUserDisplay(revision) }}
        </div>
      </div>
    </div>
    <div v-if="revision.changeType === ChangeType.Modified" class="revision-card__diff">
      <diff-table
        :old="revision.previous || {}"
        :snapshot="revision.snapshot"
      />
    </div>
  </v-card>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import { relative as relativeDate, local as localDate } from '@/filters/date';
import { ChangeType, EntityType, getUserDisplay, Revision } from '@/entities/revision';
import DiffTable from '@/components/moderator/revisions/DiffTable.vue';

const colors = {
  [ChangeType.Created]: 'green',
  [ChangeType.Modified]: 'orange',
  [ChangeType.Deleted]: 'red',
};

const icons = {
  [EntityType.Reciter]: 'record_voice_over',
  [EntityType.Album]: 'album',
  [EntityType.Track]: 'music_note',
};

@Component({
  filters: {
    relativeDate,
    localDate,
  },
  components: {
    DiffTable,
  },
})
export default class RevisionHistoryCard extends Vue {
  @Prop({ type: Object, required: true }) private readonly revision!: Revision;
  private readonly ChangeType = ChangeType;
  private readonly getUserDisplay = getUserDisplay;

  get classes() {
    return {
      'revision-card': true,
      'revision-card--dark': this.$vuetify.theme.dark,
    };
  }

  get indicator() {
    return colors[this.revision.changeType];
  }

  get title() {
    const data = this.revision.snapshot;

    switch (this.revision.entityType) {
      case EntityType.Reciter:
        return data.name;
      case EntityType.Album:
      case EntityType.Track:
        return data.title;
      default:
        throw new Error(`Unknown revision type ${this.revision.entityType}`);
    }
  }

  get subtitle() {
    const { meta } = this.revision;

    switch (this.revision.entityType) {
      case EntityType.Album:
        return `(${meta.reciter})`;
      case EntityType.Track:
        return `(${meta.reciter} • ${meta.year})`;
      default:
        return null;
    }
  }

  get icon(): string {
    return icons[this.revision.entityType];
  }
}
</script>

<style lang="scss">
@import '~assets/theme';

.revision-card {
  margin-bottom: 10px;
  background-color: transparent;

  .revision-card__content {
    padding: 16px;
    display: flex;
    align-items: center;
    cursor: pointer;
  }

  .revision-card__text {
    margin-left: 16px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    width: auto;
  }

  .revision-card__title {
    text-decoration: none;
    color: inherit;
    display: flex;
    align-items: center;
    justify-content: flex-start;
  }

  .revision-card__text-right {
    margin-left: auto;
    text-align: right;
  }
}

.change-type-container {
  display: flex;
  align-items: center;
}

.change-type {
  text-transform: uppercase;
}

.change-type-icon {
  margin-right: 6px;
}

.changes {
  display: flex;

  .old-values {
    margin-right: 6px;
  }
}

.revision-card__diff {
  width: 100%;
  border-top: 1px solid rgba(0,0,0,0.07);
}

.revision-card--dark {
  .revision-card__diff {
    border-top: 1px solid rgba(255,255,255,0.07);
  }
}
</style>
