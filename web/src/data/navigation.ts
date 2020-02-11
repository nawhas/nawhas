export default [
  {
    group: 'main',
    children: [
      {
        icon: 'mdi-home',
        title: 'Home',
        exact: true,
        to: '/',
      },
      {
        icon: 'mdi-account-multiple',
        title: 'Reciters',
        exact: false,
        to: '/reciters',
      },
      {
        icon: 'label',
        title: 'Topics',
        exact: false,
        to: '/topics',
        role: 'admin',
      },
      {
        icon: 'library_books',
        title: 'My Library',
        exact: false,
        to: '/library',
        role: 'admin',
      },
    ],
  },
  {
    group: 'trending',
    children: [
      {
        icon: 'trending_up',
        title: 'Top Charts',
        exact: true,
        to: '/charts',
        role: 'admin',
      },
      {
        icon: 'whatshot',
        title: 'Trending',
        exact: false,
        to: '/trending',
        role: 'admin',
      },
      {
        icon: 'date_range',
        title: 'New Releases',
        exact: false,
        to: '/new-releases',
        role: 'admin',
      },
    ],
  },
  {
    group: 'manage',
    children: [
      {
        icon: 'file_upload',
        title: 'Upload',
        exact: true,
        to: '/upload',
        role: 'admin',
      },
      {
        icon: 'settings',
        title: 'Settings',
        exact: false,
        to: '/settings',
        role: 'admin',
      },
    ],
  },
];
