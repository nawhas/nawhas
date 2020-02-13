<template>
  <v-skeleton-loader
    ref="skeleton"
    :boilerplate="false"
    type="list-item-avatar-three-line"
    :tile="false"
    class="mx-auto"
  ></v-skeleton-loader>
</template>

<script>
export default {
  name: 'reciter.card.skeleton',
};
</script>
