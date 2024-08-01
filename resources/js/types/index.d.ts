export interface User {
    id: string;
    name: string;
    email: string;
    username: string;
    phone_code: string;
    phone_number: string;
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
