import React, { type ReactNode } from "react";
import { Link, usePage } from "@inertiajs/react";

interface CustomerLayoutProps {
    children: ReactNode;
}

const CustomerLayout: React.FC<CustomerLayoutProps> = ({ children }) => {
    const { auth } = usePage().props as any;
    const user = auth?.user;

    return (
        <div className="min-h-screen bg-gray-50 dark:bg-[#0a0e17]">
            <header className="bg-white dark:bg-[#0c1427] border-b border-gray-100 dark:border-[#172036] py-4 px-6">
                <div className="max-w-7xl mx-auto flex items-center justify-between">
                    <Link href="/panel/dashboard" className="flex items-center">
                        <img src="/images/logo-icon.svg" alt="RJNet" className="w-7 h-7" />
                        <span className="font-bold text-lg text-black dark:text-white ml-2">RJNet Panel</span>
                    </Link>
                    <div className="flex items-center gap-4">
                        <Link href="/panel/invoices" className="text-sm text-gray-600 dark:text-gray-300 hover:text-primary-500">Tagihan</Link>
                        <Link href="/panel/tickets" className="text-sm text-gray-600 dark:text-gray-300 hover:text-primary-500">Tiket</Link>
                        <Link href="/panel/status" className="text-sm text-gray-600 dark:text-gray-300 hover:text-primary-500">Status</Link>
                        <div className="flex items-center gap-2 ml-4 pl-4 border-l border-gray-200 dark:border-gray-700">
                            <div className="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center text-white text-sm font-medium">
                                {user?.name?.charAt(0) || "U"}
                            </div>
                            <div className="hidden sm:block">
                                <p className="text-sm font-medium text-black dark:text-white">{user?.name}</p>
                                <Link href="/auth/logout" method="post" as="button" className="text-xs text-gray-500 hover:text-red-500">Logout</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <main className="max-w-7xl mx-auto px-6 py-8">{children}</main>
            <footer className="border-t border-gray-100 dark:border-[#172036] py-4 text-center text-sm text-gray-500">
                &copy; {new Date().getFullYear()} RJNet. All rights reserved.
            </footer>
        </div>
    );
};

export default CustomerLayout;
