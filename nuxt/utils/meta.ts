import { MetaInfo, MetaPropertyProperty, MetaPropertyName } from 'vue-meta';

interface MetaOptions {
  title: string;
  description?: string;
  image?: string;
}

export function generateMeta({ title, description, image } : MetaOptions): MetaInfo {
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

  if (image) {
    meta.push(
      {
        hid: 'og:image',
        property: 'og:image',
        content: image,
      },
    );
  }

  return {
    title,
    meta,
  };
}
