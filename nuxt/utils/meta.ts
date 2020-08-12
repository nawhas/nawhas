import { MetaInfo, MetaPropertyProperty, MetaPropertyName } from 'vue-meta';

interface MetaOptions {
  title: string;
  description?: string;
}

export function generateMeta({ title, description } : MetaOptions): MetaInfo {
  const meta: (MetaPropertyProperty|MetaPropertyName)[] = [
    {
      hid: 'og:title',
      property: 'og:title',
      content: title,
    },
  ];

  if (description) {
    meta.push(
      {
        hid: 'description',
        name: 'description',
        content: description,
      },
      {
        hid: 'og:description',
        property: 'og:description',
        content: description,
      },
    );
  }

  return {
    title,
    meta,
  };
}
