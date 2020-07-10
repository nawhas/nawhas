<template>
  <v-card
      :class="{ 'audit-card': true, 'audit-card--dark': $vuetify.theme.dark }"
      outlined
  >
    <div class="audit-card__content">
      <div class="audit-card__avatar">
        <v-avatar size="40" class="avatar">
          <v-icon color="white">{{ icon }}</v-icon>
        </v-avatar>
      </div>
      <div class="audit-card__text">
        <div class="audit-card__name body-1">
          {{ name }}
          <span class="subtitle" v-if="subtitle">{{ subtitle }}</span>
        </div>
        <div class="audit-card__name change-type-container">
          <v-avatar :color="indicatorColor" size="12" class="change-type-icon" />
          <span class="change-type overline">{{ audit.type }}</span>
        </div>
      </div>
      <div class="audit-card__text audit-card__text-right">
        <div class="audit-card__name caption">2 hours ago</div>
        <div class="audit-card__name caption">{{ audit.user.email }}</div>
      </div>
    </div>
    <div class="audit-card__diff" v-if="isModified">
      <diff-viewer
          :original="audit.old"
          :modified="audit.new"
      />
    </div>
  </v-card>
</template>

<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator';
import { Data as AuditData, ChangeType, EntityType } from '@/entities/audit';
import DiffViewer from '@/components/moderator/DiffViewer.vue';

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
  components: {
    DiffViewer,
  },
})
export default class RevisionHistoryCard extends Vue {
  @Prop() private audit!: AuditData;

  get test() {
    return JSON.stringify(this.audit.old, null, 2);
  }

  get indicatorColor() {
    return colors[this.audit.type];
  }

  get name() {
    // TODO - this needs to be specific to each type of audit record.
    if (!this.audit.new && !this.audit.old) {
      return false;
    }
    if (this.audit.new) {
      return this.audit.new.name;
    }
    return this.audit.old ? this.audit.old.name : null;
  }

  get subtitle(): string | null {
    // TODO - Need to return the correct value
    if (this.audit.entity === EntityType.Reciter) {
      return null;
    }

    if (this.audit.new) {
      return this.audit.new.name;
    }

    if (this.audit.old) {
      return this.audit.old.name;
    }

    return null;
  }

  get icon(): string {
    return icons[this.audit.entity];
  }

  get isCreated() {
    return this.audit.type === ChangeType.Created;
  }

  get isModified() {
    return this.audit.type === ChangeType.Modified;
  }

  get isDeleted() {
    return this.audit.type === ChangeType.Deleted;
  }
}
</script>

<style lang="scss">
@import '../../styles/theme';

.audit-card {
  margin-bottom: 10px;
  background-color: transparent;

  .audit-card__content {
    padding: 16px;
    display: flex;
    align-items: center;
  }

  .audit-card__text {
    margin-left: 16px;
    overflow: hidden;
    @include transition(color);

    .audit-card__name {
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
      width: auto;
    }
  }

  .audit-card__text-right {
    margin-left: auto;
    text-align: right;
  }

  .audit-card__avatar .avatar {
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

.audit-card__diff {
  width: 100%;
  border-top: 1px solid rgba(0,0,0,0.07);
}

.audit-card--dark {
  .audit-card__diff {
    border-top: 1px solid rgba(255,255,255,0.07);
  }
}
</style>
