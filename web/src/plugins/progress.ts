import Vue from 'vue';
import VueProgressBar from 'vue-progressbar';

Vue.use(VueProgressBar, {
  color: '#ff5a00',
  failedColor: '#c90800',
  thickness: '2px',
  transition: {
    speed: '0.3s',
    opacity: '0.6s',
    termination: 0,
  },
  autoRevert: false,
  location: 'top',
  inverse: false,
});
