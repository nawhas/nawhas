export interface AuthPrompt {
  type: 'login'|'register'|'reset.request'|'reset.change';
  reason?: AuthReason;
}

export enum AuthReason {
  General,
  TrackSaved,
}
