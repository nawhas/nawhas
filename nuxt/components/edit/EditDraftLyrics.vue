<template>
  <v-dialog
    persistent
    fullscreen
    no-click-animation
    hide-overlay
    transition="dialog-bottom-transition"
    :value="dialog"
  >
    <template #activator="{}">
      <v-btn
        v-if="cta"
        large
        color="primary"
        rounded
        dark
        @click="handleDialog"
      >
        {{ cta }}
      </v-btn>
      <v-btn
        v-else
        icon
        dark
        @click="handleDialog"
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
          @click="closeDialog"
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
import { Documents, DraftLyrics, Format } from '@/entities/lyrics';
import JsonV1Document = Documents.JsonV1.Document;
import LyricsData = Documents.JsonV1.LyricsData;
import GroupType = Documents.JsonV1.LineGroupType;
import { clone } from '@/utils/clone';
import { AuthReason } from '@/entities/auth';
import { User } from '@/entities/user';

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
  @Prop({ type: String, required: false }) private cta!: string|undefined;
  private draftLyrics: DraftLyrics | null = null;
  private form: Form = { ...defaults };
  private dialog = false;
  private loading = false;
  private buttonClicked: 'save' | 'publish' | 'close' | 'delete' | null = null;

  @Watch('dialog')
  async onDialogStateChanged(opened: boolean) {
    if (opened) {
      this.resetForm();
      return;
    }
    if (this.buttonClicked != null && this.buttonClicked !== 'publish' && this.buttonClicked !== 'delete') {
      await this.unlock();
    }
  }

  async handleDialog(): Promise<void> {
    if (!this.isLoggedIn) {
      this.$store.commit('auth/PROMPT_USER', { reason: AuthReason.General }, { root: true });
      return;
    }
    await this.getDraftLyrics();
    const canUserEdit = await this.canUserEdit();
    if (!canUserEdit) {
      this.$errors.handle423('write-up');
      return;
    }
    this.dialog = true;
  }

  async canUserEdit(): Promise<boolean> {
    if (!this.user) {
      return false;
    }
    if (this.isModerator) {
      await this.lock();
      return true;
    }

    return await this.lock();
  }

  get user(): User | null {
    return this.$store.getters['auth/user'];
  }

  get isLoggedIn() {
    return this.user;
  }

  get isModerator(): boolean {
    return this.$store.getters['auth/isModerator'];
  }

  async getDraftLyrics(): Promise<void> {
    try {
      this.draftLyrics = await this.$api.draftLyrics.index(this.track.id);
    } catch (e) {
      this.draftLyrics = null;
    }
  }

  async lock(): Promise<boolean> {
    if (!this.draftLyrics) {
      return true;
    }
    try {
      await this.$api.draftLyrics.lock(this.draftLyrics.id);
    } catch (e) {
      return false;
    }
    return true;
  }

  async unlock(): Promise<void> {
    if (!this.draftLyrics) {
      return;
    }
    try {
      await this.$api.draftLyrics.unlock(this.draftLyrics.id);
    } catch (e) {
      this.$errors.handle423('write-up');
    }
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

  closeDialog() {
    this.buttonClicked = 'close';
    this.close();
  }

  close() {
    this.dialog = false;
    this.loading = false;
  }

  async confirmDelete() {
    this.buttonClicked = 'delete';
    if (!this.draftLyrics) {
      return;
    }

    if (window.confirm(`Are you sure you want to delete draft lyrics for '${this.track.title}'?`)) {
      try {
        await this.$api.draftLyrics.delete(this.draftLyrics.id);
      } catch (e) {
        this.$errors.handle423('write-up');
      }
      this.close();
    }
  }

  async submit() {
    this.buttonClicked = 'save';
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
      try {
        await this.$api.draftLyrics.update(this.draftLyrics.id, {
          document: {
            content: this.prepareLyrics(),
            format: this.form.document.format,
          },
        });
      } catch (e) {
        this.$errors.handle423('write-up');
      }
    }

    this.close();
  }

  async publish() {
    this.buttonClicked = 'publish';
    this.loading = true;

    if (!this.form.document.content || !this.draftLyrics) {
      this.close();
      return;
    }

    try {
      await this.$api.draftLyrics.publish(this.draftLyrics.id);
    } catch (e) {
      this.$errors.handle423('write-up');
    }

    this.close();
  }

  prepareLyrics() {
    const lyrics = clone(this.form.document.content);

    return JSON.stringify(lyrics);
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
