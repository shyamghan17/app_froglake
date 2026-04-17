import React from 'react';
import { Button } from '@/components/ui/button';
import { LogIn } from 'lucide-react';
import { router, usePage } from '@inertiajs/react';
import { getCompanySetting, getAdminSetting, getImagePath } from '@/utils/helpers';

interface LoginButtonData {
  t: (key: string) => string;
  isLoading?: boolean;
}

export const getLoginButtons = (data: LoginButtonData) => {
  const { t, isLoading } = data;
  const { auth } = usePage().props as any;
  
  const googleLogo = auth?.user?.roles 
    ? getCompanySetting('google_signin_logo') 
    : getAdminSetting('google_signin_logo');

  const isEnabledGoogle = auth?.user?.roles 
    ? getCompanySetting('google_signin_enabled') 
    : getAdminSetting('google_signin_enabled');

  const handleGoogleSignIn = () => {
    window.location.href = route('google.redirect');
  };
  if (isEnabledGoogle !== 'on') {
    return [];
  } 
  return [
    {
      id: 'google-signin',
      order: 1,
      component: (
        <Button
          type="button"
          variant="outline"
          className="w-full"
          onClick={handleGoogleSignIn}
          disabled={isLoading}
        >
          {googleLogo ? (
            <img src={getImagePath(googleLogo)} alt="Google" className="mr-2 h-4 w-4" />
          ) : ''}
          {t('Sign in with Google')}
          <LogIn className="mr-2 h-4 w-4" />
        </Button>
      ),
    },
  ];
};