import Vue, { ComponentOptions } from 'vue';
import { MetaInfo } from 'vue-meta';
import { DefaultComputed, DefaultData, DefaultMethods, DefaultProps, PropsDefinition } from 'vue/types/options';

declare module 'vue/types/options' {
  interface ComponentOptions<
    V extends Vue,
    Data=DefaultData<V>,
    Methods=DefaultMethods<V>,
    Computed=DefaultComputed,
    PropsDef=PropsDefinition<DefaultProps>,
    Props=DefaultProps> {
    head?: MetaInfo | (() => MetaInfo)
  }
}
