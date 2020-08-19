export default () => {
  if ('loading' in HTMLImageElement.prototype) {
    return;
  }

  import('lazysizes');
};
