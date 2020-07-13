<template>
  <v-card :class="classes" outlined>
    <div class="revision-card__content">
      <div class="revision-card__avatar">
        <v-avatar size="40" class="avatar">
          <v-icon color="white">{{ icon }}</v-icon>
        </v-avatar>
      </div>
      <div class="revision-card__text">
        <div class="revision-card__name body-1">
          {{ name }}
          <span class="subtitle" v-if="false"></span>
        </div>
        <div class="revision-card__name change-type-container">
          <v-avatar :color="indicator" size="12" class="change-type-icon" />
          <span class="change-type overline">{{ revision.type }}</span>
        </div>
      </div>
      <div class="revision-card__text revision-card__text-right">
        <div class="revision-card__name caption">{{ revision.createdAt | relative }}</div>
        <div class="revision-card__name caption">by {{ revision.user.email }}</div>
      </div>
    </div>
    <div class="revision-card__diff" v-if="revision.type === ChangeType.Updated">
      <diff-viewer
          v-if="view === DiffView.Code"
          :original="revision.old"
          :modified="revision.new"
      />
      <diff-table
          v-if="view === DiffView.Table"
          :original="revision.old"
          :modified="revision.new"
      />
    </div>
  </v-card>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import { relative } from '@/filters/date';
import { ChangeType, EntityType, Revision } from '@/entities/revision';
import DiffViewer from '@/components/moderator/DiffViewer.vue';
import DiffTable from '@/components/moderator/DiffTable.vue';

const colors = {
  [ChangeType.Created]: 'green',
  [ChangeType.Updated]: 'orange',
  [ChangeType.Deleted]: 'red',
};

const icons = {
  [EntityType.Reciter]: 'record_voice_over',
  [EntityType.Album]: 'album',
  [EntityType.Track]: 'music_note',
};

enum DiffView {
  Table, Code
}

@Component({
  filters: {
    relative,
  },
  components: {
    DiffViewer,
    DiffTable,
  },
})
export default class RevisionHistoryCard extends Vue {
  @Prop({ type: Object, required: true }) private readonly revision!: Revision;
  private readonly DiffView = DiffView;
  private readonly ChangeType = ChangeType;

  get classes() {
    return {
      'revision-card': true,
      'revision-card--dark': this.$vuetify.theme.dark,
    };
  }

  get view(): DiffView {
    return DiffView.Table;
  }

  get indicator() {
    return colors[this.revision.type];
  }

  get name() {
    const data = this.revision.new || this.revision.old || {};

    switch (this.revision.entity) {
      case EntityType.Reciter:
        return data.name;
      case EntityType.Album:
        return `${data.title} – ${data.year}`;
      case EntityType.Track:
        return `${data.title}`;
      case EntityType.Lyrics:
        return 'Lyrics';
      default:
        return 'Unknown';
    }
  }

  get icon(): string {
    return icons[this.revision.entity];
  }
}
</script>

<style lang="scss">
@import '../../styles/theme';

.revision-card {
  margin-bottom: 10px;
  background-color: transparent;

  .revision-card__content {
    padding: 16px;
    display: flex;
    align-items: center;
  }

  .revision-card__text {
    margin-left: 16px;
    overflow: hidden;
    @include transition(color);

    .revision-card__name {
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
      width: auto;
    }
  }

  .revision-card__text-right {
    margin-left: auto;
    text-align: right;
  }

  .revision-card__avatar .avatar {
    background-color: grey;
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
