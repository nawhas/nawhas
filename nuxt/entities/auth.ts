export interface AuthPrompt {
  type: 'login'|'register';
  reason?: AuthReason;
}

export enum AuthReason {
  General,
  TrackSaved,
}
