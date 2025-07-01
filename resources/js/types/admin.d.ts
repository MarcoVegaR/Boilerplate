export interface Admin {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export type AdminPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: Admin;
    };
};
