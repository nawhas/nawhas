import { AppState } from '@/store/app';
import { AuthState } from '@/store/auth';
import { FeaturesState } from '@/store/features';
import { LibraryState } from '@/store/library';
import { PlayerState } from '@/store/player';
import { PreferencesState } from '@/store/preferences';

export type RootState = {
  app: AppState,
  auth: AuthState,
  features: FeaturesState,
  library: LibraryState,
  player: PlayerState,
  preferences: PreferencesState,
};
