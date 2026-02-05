export * from './auth';
export * from './navigation';
export * from './ui';

import type { Auth } from './auth';

export interface FrequentlyAskedQuestion {
    question: string;
    answer: string;
}

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
    frequently_questions: FrequentlyAskedQuestion[];
};

export interface Banner {
    id: number
    image: string
    mobileImage: string
    status: string
    type: 'discount' | 'informative'
    bannerableId: number
    bannerableType: string
    bannerable: Discount | any | null
}

export interface Department {
    id: number;
    name: string;
    slug: string;
    description: string;
    image: string;
}

export interface Category {
    id: number;
    name: string;
    slug: string;
    description: string;
    image: string;
    products_count: number;
    department?: Department;
}

export interface Discount {
    id: number;
    name: string;
    slug: string;
    description: string;
    type: 'percentage' | 'fixed';
    value: number;
    applicableTo: string;
}

export interface Product {
    id: number;
    name: string;
    slug: string;
    code: string;
    description: string | null;
    type: string | null;
    salePrice: number;
    weight: number;
    freeShipping: boolean;
    image: string;
    status: 'ENABLED' | 'DISABLED';
    recommended: boolean;
    stock: number | null;
    activeDiscounts: Discount[];
    category: Category | null;
}

export interface Currency {
    id: number;
    name: string;
    isoCode: string;
    isDefault: boolean;
    conversionValue: number;
}

export interface PaginatedResponse<T> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
    settings: Settings;
    currencies: Currency[];
    selectedCurrency: Currency | null;
    [key: string]: unknown;
};
