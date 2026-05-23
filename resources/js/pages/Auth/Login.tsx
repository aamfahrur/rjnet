import React from "react";
import GuestLayout from "../../layouts/GuestLayout";
import { Head, useForm } from "@inertiajs/react";

const Login: React.FC = () => {
    const { data, setData, post, processing, errors } = useForm({ email: "", password: "", remember: false });

    return (
        <GuestLayout>
            <Head title="Login" />
            <div className="w-full max-w-sm mx-auto">
                {/* Logo */}
                <div className="flex justify-center mb-6">
                    <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-primary-500/25">
                        <span className="text-white font-bold text-2xl">R</span>
                    </div>
                </div>
                <h2 className="text-2xl font-bold text-gray-800 dark:text-white text-center mb-1">Selamat Datang</h2>
                <p className="text-sm text-gray-500 dark:text-gray-400 text-center mb-8">Login ke Panel RJNet</p>

                <form onSubmit={(e) => { e.preventDefault(); post("/auth/login"); }} className="space-y-4">
                    <div>
                        <label className="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Email</label>
                        <input type="email" value={data.email} onChange={(e) => setData("email", e.target.value)}
                            className="w-full px-4 py-3 border border-gray-200 dark:border-[#172036] rounded-xl text-sm bg-white dark:bg-[#0c1427] text-gray-800 dark:text-white placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 outline-none transition-all"
                            placeholder="admin@rjnet.id" autoFocus />
                        {errors.email && <p className="text-xs text-red-500 mt-1.5 ml-1">{errors.email}</p>}
                    </div>
                    <div>
                        <label className="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Password</label>
                        <input type="password" value={data.password} onChange={(e) => setData("password", e.target.value)}
                            className="w-full px-4 py-3 border border-gray-200 dark:border-[#172036] rounded-xl text-sm bg-white dark:bg-[#0c1427] text-gray-800 dark:text-white placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 outline-none transition-all"
                            placeholder="••••••••" />
                        {errors.password && <p className="text-xs text-red-500 mt-1.5 ml-1">{errors.password}</p>}
                    </div>
                    <div className="flex items-center justify-between">
                        <label className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                            <input type="checkbox" checked={data.remember} onChange={(e) => setData("remember", e.target.checked)}
                                className="rounded border-gray-300 dark:border-gray-600 text-primary-500 focus:ring-primary-500" />
                            Ingat saya
                        </label>
                        <a href="/auth/forgot-password" className="text-sm text-primary-500 hover:text-primary-600 font-medium transition-colors">Lupa password?</a>
                    </div>
                    <button type="submit" disabled={processing}
                        className="w-full py-3 bg-gradient-to-r from-primary-500 to-indigo-600 text-white rounded-xl text-sm font-semibold hover:from-primary-600 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-primary-500/20">
                        {processing ? "Memproses..." : "Masuk"}
                    </button>
                </form>
                <p className="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
                    Belum punya akun? <a href="/auth/register" className="text-primary-500 hover:text-primary-600 font-medium transition-colors">Daftar</a>
                </p>
            </div>
        </GuestLayout>
    );
};

export default Login;
