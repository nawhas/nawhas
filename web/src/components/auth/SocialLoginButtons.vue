<template>
  <div class="container">
    <button @click="loginWithSocial('facebook')" class="loginBtn loginBtn--facebook">Login with Facebook</button>
    <button @click="loginWithSocial('google')" class="loginBtn loginBtn--google">Login with Google</button>
    <button @click="loginWithSocial('apple')" class="loginBtn loginBtn--apple">Login with Apple</button>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';

@Component
export default class SocialLoginButtons extends Vue {
  async loginWithSocial(provider: string) {
    window.location.href = `https://api.nawhas.test/v1/auth/login/${provider}`;
  }
}
</script>

<style lang="scss" scoped>
.container {
  display: block;
}

.loginBtn {
  box-sizing: border-box;
  position: relative;
  width: 13em;
  margin: 0.2em;
  padding: 0 15px 0 46px;
  border: none;
  text-align: left;
  line-height: 34px;
  white-space: nowrap;
  border-radius: 0.2em;
  font-size: 16px;
  color: #fff;
}
.loginBtn:before {
  content: '';
  box-sizing: border-box;
  position: absolute;
  top: 0;
  left: 0;
  width: 34px;
  height: 100%;
}
.loginBtn:focus {
  outline: none;
}
.loginBtn:active {
  box-shadow: inset 0 0 0 32px rgba(0, 0, 0, 0.1);
}

/* Facebook */
.loginBtn--facebook {
  background-color: #4c69ba;
  background-image: linear-gradient(#4c69ba, #3b55a0);
  /*font-family: "Helvetica neue", Helvetica Neue, Helvetica, Arial, sans-serif;*/
  text-shadow: 0 -1px 0 #354c8c;
}
.loginBtn--facebook:before {
  border-right: #364e92 1px solid;
  background: url('https://s3-us-west-2.amazonaws.com/s.cdpn.io/14082/icon_facebook.png')
    6px 6px no-repeat;
}
.loginBtn--facebook:hover,
.loginBtn--facebook:focus {
  background-color: #5b7bd5;
  background-image: linear-gradient(#5b7bd5, #4864b1);
}

/* Google */
.loginBtn--google {
  /*font-family: "Roboto", Roboto, arial, sans-serif;*/
  background: #dd4b39;
}
.loginBtn--google:before {
  border-right: #bb3f30 1px solid;
  background: url('https://s3-us-west-2.amazonaws.com/s.cdpn.io/14082/icon_google.png')
    6px 6px no-repeat;
}
.loginBtn--google:hover,
.loginBtn--google:focus {
  background: #e74b37;
}

/* Apple */
.loginBtn--apple {
  /*font-family: "Roboto", Roboto, arial, sans-serif;*/
  background: black;
}
.loginBtn--apple:before {
  border-right: black 1px solid;
}
.loginBtn--apple:hover,
.loginBtn--apple:focus {
  background: black;
}
</style>
