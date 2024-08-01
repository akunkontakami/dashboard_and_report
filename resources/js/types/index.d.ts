export interface User {
    id: string;
    name: string;
    company_id: string;
    role: string;
    avatar: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
    flash: {
        error?: string;
        success?: string;
    },
};
