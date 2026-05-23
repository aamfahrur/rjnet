import React, { type ReactNode } from "react";
import { Link } from "@inertiajs/react";

interface GuestLayoutProps {
    children: ReactNode;
}

const GuestLayout: React.FC<GuestLayoutProps> = ({ children }) => {
    return (
        <div className="min-h-screen bg-gray-50 dark:bg-[#0a0e17] flex items-center justify-center px-4 py-12">
            <div className="w-full max-w-md">
                <div className="text-center mb-8">
                    <Link href="/" className="inline-flex items-center justify-center">
                        <img src="/images/logo-icon.svg" alt="RJNet" className="w-10 h-10" />
                        <span className="font-bold text-2xl text-black dark:text-white ml-2">RJNet</span>
                    </Link>
                    <p className="text-gray-500 dark:text-gray-400 mt-2 text-sm">RT/RW Net Management System</p>
                </div>
                <div className="bg-white dark:bg-[#0c1427] rounded-lg shadow-sm border border-gray-100 dark:border-[#172036] p-8">
                    {children}
                </div>
                <p className="text-center text-sm text-gray-500 mt-6">&copy; {new Date().getFullYear()} RJNet.</p>
            </div>
        </div>
    );
};

export default GuestLayout;
