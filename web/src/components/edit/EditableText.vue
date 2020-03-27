<template>
  <div contenteditable="true" ref="editable" v-on="listeners"></div>
</template>

<script lang="ts">
import {
  Component, Model, Ref, Vue, Watch,
} from 'vue-property-decorator';
import Str from '@/components/utils/Str';

@Component
export default class EditableText extends Vue {
  @Ref('editable') private field!: HTMLDivElement;
  @Model('input', { type: String }) private readonly content!: string;

  get listeners() {
    return {
      ...this.$listeners,
      input: this.update,
      paste: this.onPaste,
      keypress: this.onKeyPress,
    };
  }

  mounted() {
    this.field.innerText = this.content;
  }

  @Watch('content')
  renderChange(value) {
    if (value !== this.field.innerText) {
      this.field.innerText = value;
    }
  }

  onPaste(e: ClipboardEvent) {
    e.preventDefault();
    if (e.clipboardData) {
      const text = new Str(e.clipboardData.getData('text/plain'))
        .replace('\r\n', ' ')
        .replace('\n', ' ')
        .replace('\r', ' ')
        .get();

      window.document.execCommand('insertText', false, text);
    }

    this.forward(e);
  }

  onKeyPress(e: KeyboardEvent) {
    if (e.key === 'Enter') {
      e.preventDefault();
    }

    this.forward(e);
  }

  update() {
    this.$emit('input', this.field.innerText);
  }

  forward(e: Event) {
    this.$emit(e.type, e);
  }
}
</script>

<style lang="scss" scoped>

</style>
