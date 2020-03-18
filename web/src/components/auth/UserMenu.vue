<template>
  <div>
    <v-menu :close-on-content-click="false" v-model="open">
      <template v-slot:activator="{ on }">
        <v-btn icon class="user-menu" v-on="on">
          <v-icon>person</v-icon>
        </v-btn>
      </template>
      <v-card width="350px">
        <template v-if="initialized">
          <v-list>
            <v-list-item>
              <v-list-item-avatar color="grey lighten-2">
                <img v-if="authenticated" crossorigin :src="user.avatar" :alt="user.name">
                <v-icon v-else>person</v-icon>
              </v-list-item-avatar>

              <v-list-item-content v-if="authenticated">
                <v-list-item-title class="d-flex align-center">
                  <div>{{ user.name }}</div>
                  <v-icon title="Moderator" class="ml-2"
                          v-if="user.role === 'moderator'" color="primary" small>
                    security
                  </v-icon>
                </v-list-item-title>
                <v-list-item-subtitle>{{ user.email }}</v-list-item-subtitle>
              </v-list-item-content>
              <v-list-item-content v-else>
                <v-list-item-title>Guest</v-list-item-title>
                <v-list-item-subtitle>Not logged in</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-list>

          <v-divider></v-divider>

          <div class="preferences">
            <v-list>
              <v-subheader style="height: 36px">PREFERENCES</v-subheader>
              <v-list-item-group>
                <v-list-item :ripple="false" inactive>
                  <v-list-item-content>
                    <v-list-item-title>Theme</v-list-item-title>
                  </v-list-item-content>
                  <v-list-item-action>
                    <v-btn-toggle mandatory dense v-model="theme">
                      <v-btn value="light">
                        <v-icon>wb_sunny</v-icon>
                      </v-btn>
                      <v-btn value="auto">
                        <v-icon>brightness_auto</v-icon>
                      </v-btn>
                      <v-btn value="dark">
                        <v-icon>nights_stay</v-icon>
                      </v-btn>
                    </v-btn-toggle>
                  </v-list-item-action>
                </v-list-item>
              </v-list-item-group>
            </v-list>
          </div>

          <v-divider></v-divider>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn v-if="authenticated" color="primary" text @click="logout">Log Out</v-btn>
            <template v-else>
              <v-btn color="primary" text @click="login">Log In</v-btn>
            </template>
          </v-card-actions>
        </template>
        <div class="loader" v-else>
          <v-progress-circular indeterminate color="primary" size="36" />
        </div>
      </v-card>
    </v-menu>
    <v-dialog v-model="showLoginDialog" width="500">
      <login-form @close="showLoginDialog = false" />
    </v-dialog>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import LoginForm from '@/views/public/auth/LoginForm.vue';
@Component({
  components: { LoginForm },
})
export default class UserMenu extends Vue {
  private open = false;
  private showLoginDialog = false;

  get user() {
    return this.$store.state.auth.user;
  }

  get authenticated() {
    return this.$store.getters['auth/authenticated'];
  }

  get initialized() {
    return this.$store.state.auth.initialized;
  }

  get theme() {
    return this.$store.state.preferences.theme;
  }

  set theme(value) {
    this.$store.commit('preferences/SET_THEME', value);
  }

  login() {
    this.showLoginDialog = true;
    this.open = false;
  }

  logout() {
    this.$store.dispatch('auth/logout');
    this.open = false;
  }

  setTheme(value) {
    console.log(value);
  }

  register() {
    // todo
  }
}
</script>

<style lang="scss" scoped>
.loader {
  padding: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
