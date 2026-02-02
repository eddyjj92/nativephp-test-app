export * from './auth';
export * from './navigation';
export * from './ui';

import type { Auth } from './auth';

export type Settings = {
    site_name: string;
    site_active: boolean;
    email: string;
    phone: string | null;
    address: string;
    facebook: string | null;
    x: string | null;
    instagram: string | null;
    logo_light: string;
    logo_dark: string;
    terms_conditions: string;
    privacy_policies: string;
    cookies_policies: string;
    legal_notice: string;
    frequently_questions: any[];
};

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
    settings: Settings;
    [key: string]: unknown;
};
