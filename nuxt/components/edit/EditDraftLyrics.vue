<template>
  <v-dialog
    v-model="dialog"
    persistent
    fullscreen
    no-click-animation
    hide-overlay
    transition="dialog-bottom-transition"
  >
    <template #activator="{ on }">
      <v-btn
        icon
        dark
        v-on="on"
      >
        <v-icon>lyrics</v-icon>
      </v-btn>
    </template>
    <v-card :loading="loading">
      <v-app-bar
        fixed
        elevate-on-scroll
      >
        <v-btn
          icon
          @click="close"
        >
          <v-icon>close</v-icon>
        </v-btn>
        <v-toolbar-title>Edit Write-Up</v-toolbar-title>
        <v-spacer />
        <div class="toolbar__actions">
          <v-btn
            v-if="isModerator && draftLyrics"
            text
            @click="publish"
          >
            Publish
          </v-btn>
          <v-btn
            v-if="isModerator && draftLyrics"
            text
            @click="confirmDelete"
          >
            <v-icon>delete_forever</v-icon>
          </v-btn>
          <v-btn
            color="primary"
            @click="submit"
          >
            Save
          </v-btn>
        </div>
      </v-app-bar>
      <v-card-text class="dialog__content">
        <timestamped-editor v-model="form.document.content" :track="track" />
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'nuxt-property-decorator';
import { Track } from '@/entities/track';
import TimestampedEditor from '@/components/edit/lyrics/TimestampedEditor.vue';
import { Documents, Format, Lyrics } from '@/entities/lyrics';
import JsonV1Document = Documents.JsonV1.Document;
import LyricsData = Documents.JsonV1.LyricsData;
import GroupType = Documents.JsonV1.LineGroupType;
import { clone } from '@/utils/clone';

interface Form {
  document: {
    content: JsonV1Document | null;
    format: Format;
  };
}

const defaults: Form = {
  document: {
    content: null,
    format: Format.JsonV1,
  },
};

@Component({
  components: { TimestampedEditor },
})
export default class EditDraftLyrics extends Vue {
  @Prop({ type: Object, required: true }) private track!: Track;
  private draftLyrics: Lyrics | null = null;
  private form: Form = { ...defaults };
  private dialog = false;
  private loading = false;

  @Watch('dialog')
  async onDialogStateChanged(opened: boolean) {
    if (opened) {
      await this.getDraftLyrics();
      this.resetForm();
      await this.lock();
    } else {
      await this.unlock();
    }
  }

  async getDraftLyrics(): Promise<void> {
    try {
      this.draftLyrics = await this.$api.draftLyrics.index(this.track.id);
    } catch (e) {
      this.draftLyrics = null;
    }
  }

  async lock(): Promise<void> {
    if (!this.draftLyrics) {
      return;
    }
    try {
      await this.$api.draftLyrics.lock(this.draftLyrics.id);
    } catch (e) {}
  }

  async unlock(): Promise<void> {
    if (!this.draftLyrics) {
      return;
    }
    try {
      await this.$api.draftLyrics.unlock(this.draftLyrics.id);
    } catch (e) {}
  }

  resetForm() {
    this.form = { ...defaults };
    if (this.track.lyrics === undefined) {
      return;
    }
    this.form.document.content = this.lyrics;
  }

  get lyrics(): JsonV1Document|null {
    if (this.draftLyrics) {
      return JSON.parse(this.draftLyrics.document.content);
    }

    if (!this.track.lyrics) {
      return null;
    }

    const { content, format } = this.track.lyrics;
    if (format === Format.JsonV1) {
      return JSON.parse(content);
    }

    const data: LyricsData = content.trim().split(/\n/gi).map((text, index) => {
      if (text.trim().length === 0) {
        return {
          timestamp: null,
          lines: [{ text: '', repeat: 0 }],
          type: GroupType.Spacer,
        };
      }

      return {
        timestamp: index === 0 ? 0 : null,
        lines: [{ text: text.trim(), repeat: 0 }],
      };
    });

    return {
      meta: {
        timestamps: false,
      },
      data,
    };
  }

  close() {
    this.dialog = false;
    this.loading = false;
  }

  async confirmDelete() {
    if (!this.draftLyrics) {
      return;
    }

    if (window.confirm(`Are you sure you want to delete draft lyrics for '${this.track.title}'?`)) {
      await this.$api.draftLyrics.delete(this.draftLyrics.id);
      this.close();
    }
  }

  async submit() {
    this.loading = true;

    if (!this.form.document.content) {
      this.close();
      return;
    }

    if (!this.draftLyrics) {
      await this.$api.draftLyrics.store({
        track_id: this.track.id,
        document: {
          content: this.prepareLyrics(),
          format: this.form.document.format,
        },
      });
    } else {
      await this.$api.draftLyrics.update(this.draftLyrics.id, {
        document: {
          content: this.prepareLyrics(),
          format: this.form.document.format,
        },
      });
    }

    this.close();
  }

  async publish() {
    this.loading = true;

    if (!this.form.document.content || !this.draftLyrics) {
      this.close();
      return;
    }

    await this.$api.draftLyrics.publish(this.draftLyrics.id);

    this.close();
  }

  prepareLyrics() {
    const lyrics = clone(this.form.document.content);

    return JSON.stringify(lyrics);
  }

  get isModerator(): boolean {
    return this.$store.getters['auth/isModerator'];
  }
}
</script>

<style lang="scss" scoped>
.dialog__content {
  max-width: 800px;
  margin: 0 auto;
  padding: 96px 12px !important;
}
</style>
