export type FeatureMap = {
  [name in Feature]?: boolean;
};

export enum Feature {
  PublicUserRegistration = 'registration.public',
  SocialAuthentication = 'auth.social',
}
