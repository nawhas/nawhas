<router>
  path: /moderator/stories/new
  name: "moderator.stories.new"
</router>

<template>
  <v-container class="app__section mt-4 story-editor">
    <h2 class="story-editor__title" dusk="moderator-stories-new__title">
      New story
    </h2>
    <v-form @submit.prevent="onSubmit">
      <v-text-field v-model="form.title" label="Title" required outlined />
      <v-text-field
        v-model="form.slug"
        label="Slug (optional)"
        hint="Leave blank to generate from title"
        persistent-hint
        outlined
      />
      <v-text-field
        v-model="form.display_date"
        label="Display date"
        type="date"
        outlined
        dusk="moderator-stories__display-date"
      />
      <v-file-input
        v-model="form.heroImageFile"
        label="Hero image (upload)"
        placeholder="Upload a hero image"
        prepend-icon="mdi-image"
        outlined
        accept="image/*"
        :show-size="1000"
        clearable
      />
      <v-text-field
        v-model="form.hero_image_url"
        label="Or hero image URL (external)"
        hint="Optional if you upload a file instead"
        persistent-hint
        outlined
      />
      <v-textarea v-model="form.excerpt" label="Excerpt (plain text)" outlined rows="3" />
      <v-textarea v-model="form.body" label="Body" outlined rows="12" />
      <v-switch v-model="form.published" label="Published" color="primary" />
      <div class="story-editor__actions">
        <v-btn text :to="{ name: 'moderator.stories.index' }">
          Cancel
        </v-btn>
        <v-btn color="primary" type="submit" :loading="saving">
          Create
        </v-btn>
      </div>
    </v-form>
  </v-container>
</template>

<script lang="ts">
import Vue from 'vue';
import type { StoreStoryPayload } from '@/api/stories';
import { showToast } from '@/events/toaster';
import { localIsoDate } from '@/utils/date';

interface Form {
  title: string;
  slug: string;
  excerpt: string;
  body: string;
  hero_image_url: string;
  heroImageFile: File | null;
  display_date: string;
  published: boolean;
}

interface Data {
  form: Form;
  saving: boolean;
}

function emptyForm(): Form {
  return {
    title: '',
    slug: '',
    excerpt: '',
    body: '',
    hero_image_url: '',
    heroImageFile: null,
    display_date: localIsoDate(),
    published: false,
  };
}

export default Vue.extend({
  layout: 'moderator',
  data(): Data {
    return {
      form: emptyForm(),
      saving: false,
    };
  },
  head: {
    title: 'New story',
  },
  methods: {
    payload(): StoreStoryPayload {
      const p: StoreStoryPayload = {
        title: this.form.title.trim(),
        excerpt: this.form.excerpt || null,
        body: this.form.body || null,
        hero_image_url: this.form.heroImageFile ? null : (this.form.hero_image_url || null),
        display_date: this.form.display_date || null,
        published: this.form.published,
      };
      const slug = this.form.slug.trim();
      if (slug) {
        p.slug = slug;
      }
      return p;
    },
    async onSubmit() {
      if (!this.form.title.trim()) {
        showToast({ text: 'Title is required', type: 'error' });
        return;
      }
      this.saving = true;
      try {
        const story = await this.$api.stories.store(this.payload());
        if (this.form.heroImageFile) {
          await this.$api.stories.uploadHeroImage(story.id, this.form.heroImageFile);
        }
        showToast({ text: 'Story created', type: 'success' });
        await this.$router.replace({
          name: 'moderator.stories.edit',
          params: { id: story.id },
        });
      } catch (e) {
        showToast({ text: 'Could not create story', type: 'error' });
      } finally {
        this.saving = false;
      }
    },
  },
});
</script>

<style scoped lang="scss">
.story-editor__title {
  font-weight: 300;
  font-size: 34px;
  margin-bottom: 24px;
}
.story-editor__actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  margin-top: 16px;
}
</style>
