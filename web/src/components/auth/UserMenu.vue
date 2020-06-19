<template>
  <div>
    <v-menu :close-on-content-click="false" v-model="open" left>
      <template #activator="{ on }">
        <v-avatar
            icon
            class="user-menu__avatar"
            v-on="on" v-ripple size="36"
            :color="$vuetify.theme.dark ? 'grey darken-3' : 'grey lighten-2'"
        >
          <img v-if="authenticated" crossorigin :src="user.avatar" :alt="user.name">
          <v-icon v-else>person</v-icon>
        </v-avatar>
      </template>
      <v-card width="400px">
        <template v-if="initialized">
          <v-list>
            <v-list-item>
              <v-list-item-avatar color="grey lighten-2">
                <img v-if="authenticated" crossorigin :src="user.avatar" :alt="user.name">
                <v-icon v-else light>person</v-icon>
              </v-list-item-avatar>

              <v-list-item-content v-if="authenticated">
                <v-list-item-title class="d-flex align-center">
                  <div>{{ user.name }}</div>
                  <v-icon title="Moderator" class="ml-2"
                          v-if="user.role === Role.Moderator" color="primary" small>
                    security
                  </v-icon>
                </v-list-item-title>
                <v-list-item-subtitle>{{ user.email }}</v-list-item-subtitle>
              </v-list-item-content>
              <v-list-item-content v-else>
                <v-list-item-title>Guest</v-list-item-title>
                <v-list-item-subtitle>Not logged in</v-list-item-subtitle>
              </v-list-item-content>
              <v-list-item-action>
                <v-btn v-if="authenticated" color="primary" text @click="logout">Log Out</v-btn>
                <template v-else>
                  <div>
                    <v-btn v-if="canRegisterFeature" color="primary" text @click="register">Sign Up</v-btn>
                    <v-btn color="primary" text @click="login">Log In</v-btn>
                  </div>
                </template>
              </v-list-item-action>
            </v-list-item>
          </v-list>

          <v-divider></v-divider>

          <div class="user-menu__section">
            <v-list>
              <v-subheader class="section__heading">Preferences</v-subheader>
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

          <div class="user-menu__section">
            <v-list>
              <v-list-item @click="showWhatsNew">
                <v-list-item-content>
                  <v-list-item-title class="user-menu__action">
                    <v-icon>fiber_new</v-icon> <div class="user-menu__action__text">What's new?</div>
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <v-list-item @click="showBugReport">
                <v-list-item-content>
                  <v-list-item-title class="user-menu__action">
                    <v-icon>report</v-icon> <div class="user-menu__action__text">Report an issue</div>
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </div>
        </template>

        <div class="loader" v-else>
          <v-progress-circular indeterminate color="primary" size="36" />
        </div>
      </v-card>
    </v-menu>
    <v-dialog v-model="showLoginDialog" width="500">
      <login-form @close="showLoginDialog = false" />
    </v-dialog>
    <v-dialog v-model="showRegisterDialog" width="500">
      <register-form @close="showRegisterDialog = false" />
    </v-dialog>
    <v-dialog v-model="showWhatsNewDialog" width="500">
      <app-changelog @close="showWhatsNewDialog = false" />
    </v-dialog>
    <v-dialog persistent v-model="showBugReportDialog" width="500">
      <bug-report-form @close="showBugReportDialog = false" />
    </v-dialog>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import LoginForm from '@/components/auth/LoginForm.vue';
import RegisterForm from '@/components/auth/RegisterForm.vue';
import AppChangelog from '@/components/notifications/AppChangelog.vue';
import BugReportForm from '@/components/BugReportForm.vue';
import { User, Role } from '@/entities/user';
import { Getters as AuthGetters, Actions as AuthActions } from '@/store/modules/auth';

@Component({
  components: {
    BugReportForm,
    AppChangelog,
    LoginForm,
    RegisterForm,
  },
})
export default class UserMenu extends Vue {
  private readonly Role = Role;
  private open = false;
  private showLoginDialog = false;
  private showRegisterDialog = false;
  private showWhatsNewDialog = false;
  private showBugReportDialog = false;

  get user(): User|null {
    return this.$store.getters[AuthGetters.User];
  }

  get authenticated(): boolean {
    return this.$store.getters[AuthGetters.Authenticated];
  }

  get initialized(): boolean {
    return this.$store.state.auth.initialized;
  }

  get theme() {
    return this.$store.state.preferences.theme;
  }

  set theme(value) {
    this.$store.commit('preferences/SET_THEME', value);
  }

  // This deterimes if the feature is marked as true or false for public registration
  get canRegisterFeature() {
    const { features } = this.$store.state;

    if (features.features['registration.public'] === undefined) {
      return true;
    }

    return features.features['registration.public'];
  }

  login() {
    this.showLoginDialog = true;
    this.open = false;
  }

  async logout() {
    await this.$store.dispatch(AuthActions.Logout);
    this.open = false;
  }

  showWhatsNew() {
    this.open = false;
    this.showWhatsNewDialog = true;
  }

  showBugReport() {
    this.open = false;
    this.showBugReportDialog = true;
  }

  register() {
    this.showRegisterDialog = true;
    this.open = false;
  }
}
</script>

<style lang="scss" scoped>
@import "../../styles/theme";

.loader {
  padding: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.user-menu__avatar {
  cursor: pointer;
  margin-top: 6px;
  margin-left: 12px;
}
.user-menu__section {
  .section__heading {
    height: 36px;
    text-transform: uppercase;
  }
}
.user-menu__action {
  display: flex;
  align-items: center;
  .user-menu__action__text {
    margin-left: 16px;
  }
}

@media #{map-get($display-breakpoints, 'sm-and-down')} {
  .user-menu__avatar {
    margin-top: 0;
    margin-left: 12px;
  }
}
</style>
