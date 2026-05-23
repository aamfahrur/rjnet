import React from "react";
import { Head, Link } from "@inertiajs/react";

const Welcome: React.FC = () => (
    <>
        <Head title="Welcome" />
        <div className="min-h-screen bg-gradient-to-br from-primary-600 to-indigo-800 flex items-center justify-center px-4">
            <div className="text-center text-white">
                <img src="/images/logo-icon.svg" alt="RJNet" className="w-20 h-20 mx-auto mb-6" />
                <h1 className="text-4xl font-bold mb-3">RJNet</h1>
                <p className="text-lg text-white/80 mb-8">RT/RW Net Management System</p>
                <div className="flex gap-3 justify-center">
                    <Link href="/auth/login" className="px-6 py-2.5 bg-white text-primary-600 rounded-lg font-medium text-sm hover:bg-gray-100">Login Admin</Link>
                    <Link href="/panel/dashboard" className="px-6 py-2.5 border border-white/30 text-white rounded-lg font-medium text-sm hover:bg-white/10">Panel Pelanggan</Link>
                </div>
            </div>
        </div>
    </>
);

export default Welcome;
