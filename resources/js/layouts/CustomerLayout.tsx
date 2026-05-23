import React, { useEffect, type ReactNode } from "react";
import { Link, usePage } from "@inertiajs/react";
import { ToastProvider, useToast } from "../components/Toast";

interface CustomerLayoutProps { children: ReactNode; }

const CustomerLayoutContent: React.FC<CustomerLayoutProps> = ({ children }) => {
    const { auth, flash } = usePage().props as any;
    const user = auth?.user;
    const { setFlash } = useToast();

    useEffect(() => {
        if (flash && Object.keys(flash).length > 0) {
            setFlash(flash);
        }
    }, [flash]);

    return (
        <div className="min-h-screen bg-gray-50 dark:bg-[#0a0e17] flex flex-col">
            <header className="sticky top-0 z-30 bg-white/80 dark:bg-[#0c1427]/80 backdrop-blur-lg border-b border-gray-100 dark:border-[#172036]">
                <div className="max-w-7xl mx-auto flex items-center justify-between h-16 px-4 md:px-6">
                    <Link href="/panel/dashboard" className="flex items-center gap-2.5">
                        <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-indigo-600 flex items-center justify-center shadow-sm">
                            <span className="text-white font-bold text-sm">R</span>
                        </div>
                        <span className="font-bold text-lg text-gray-800 dark:text-white">RJNet Panel</span>
                    </Link>
                    <nav className="flex items-center gap-1">
                        {[
                            { href: "/panel/invoices", label: "Tagihan", icon: "receipt_long" },
                            { href: "/panel/tickets", label: "Tiket", icon: "support_agent" },
                            { href: "/panel/status", label: "Status", icon: "monitor_heart" },
                        ].map((item) => (
                            <Link key={item.href} href={item.href}
                                className="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-[#15203c] hover:text-primary-500 transition-colors">
                                <i className="material-symbols-outlined !text-[18px]">{item.icon}</i>
                                <span className="hidden sm:inline">{item.label}</span>
                            </Link>
                        ))}
                        <div className="ml-3 pl-3 border-l border-gray-200 dark:border-gray-700 flex items-center gap-2.5">
                            <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-indigo-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                {user?.name?.charAt(0) || "U"}
                            </div>
                            <div className="hidden sm:block text-sm leading-tight">
                                <p className="font-semibold text-gray-800 dark:text-white">{user?.name}</p>
                                <Link href="/auth/logout" method="post" as="button" className="text-xs text-gray-500 hover:text-red-500 transition-colors">Logout</Link>
                            </div>
                        </div>
                    </nav>
                </div>
            </header>
            <main className="flex-1 max-w-7xl w-full mx-auto px-4 md:px-6 py-8">{children}</main>
            <footer className="bg-white dark:bg-[#0c1427] border-t border-gray-100 dark:border-[#172036] py-4 text-center">
                <p className="text-xs text-gray-400 dark:text-gray-500">&copy; {new Date().getFullYear()} <span className="font-medium text-gray-500 dark:text-gray-400">RJNet</span>. All rights reserved.</p>
            </footer>
        </div>
    );
};

const CustomerLayout: React.FC<CustomerLayoutProps> = ({ children }) => (
    <ToastProvider>
        <CustomerLayoutContent>{children}</CustomerLayoutContent>
    </ToastProvider>
);

export default CustomerLayout;
