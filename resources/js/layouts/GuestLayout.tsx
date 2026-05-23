import React, { type ReactNode } from "react";

interface GuestLayoutProps {
    children: ReactNode;
}

const GuestLayout: React.FC<GuestLayoutProps> = ({ children }) => {
    return (
        <div className="min-h-screen bg-gray-50 dark:bg-[#0a0e17] flex items-center justify-center px-4 py-12">
            <div className="w-full max-w-md">
                <div className="bg-white dark:bg-[#0c1427] rounded-2xl shadow-sm border border-gray-100 dark:border-[#172036] p-8">
                    {children}
                </div>
                <p className="text-center text-xs text-gray-400 dark:text-gray-500 mt-6">
                    &copy; {new Date().getFullYear()} <span className="font-medium text-gray-500 dark:text-gray-400">RJNet</span>. All rights reserved.
                </p>
            </div>
        </div>
    );
};

export default GuestLayout;
