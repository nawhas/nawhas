export enum Feature {
  PublicUserRegistration = 'registration.public',
  SocialAuthentication = 'auth.social',
}

export type FeatureMap = {
  [name in Feature]?: boolean;
};
