export enum AuthReason {
  General,
  TrackSaved,
}

export interface AuthPrompt {
  type: 'login'|'register'|'reset.request'|'reset.change';
  reason?: AuthReason;
}
