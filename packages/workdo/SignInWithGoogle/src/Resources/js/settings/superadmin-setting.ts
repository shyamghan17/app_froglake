import { LogIn } from 'lucide-react';

export interface SettingMenuItem {
  order: number;
  title: string;
  href: string;
  icon: any;
  permission: string;
  component: string;
}

export const getSignInWithGoogleSuperAdminSettings = (t: (key: string) => string): SettingMenuItem[] => [
  {
    order: 660,
    title: t('Sign-In With Google Settings'),
    href: '#google-signin-settings',
    icon: LogIn,
    permission: 'manage-google-signin-settings',
    component: 'google-signin-settings'
  }
];