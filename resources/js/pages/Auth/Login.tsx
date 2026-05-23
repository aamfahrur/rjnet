import React from "react";
import GuestLayout from "../../layouts/GuestLayout";
import { Head, useForm } from "@inertiajs/react";

const Login: React.FC = () => {
    const { data, setData, post, processing, errors } = useForm({ email: "", password: "", remember: false });
    const ic = "w-full px-3 py-2.5 border border-gray-200 dark:border-[#172036] rounded-lg text-sm bg-white dark:bg-[#0c1427] text-black dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none";

    return (
        <GuestLayout>
            <Head title="Login" />
            <h2 className="text-xl font-bold text-black dark:text-white text-center mb-1">Selamat Datang</h2>
            <p className="text-sm text-gray-500 text-center mb-6">Login ke Panel RJNet</p>
            <form onSubmit={(e) => { e.preventDefault(); post("/auth/login"); }} className="space-y-4">
                <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" value={data.email} onChange={(e) => setData("email", e.target.value)} className={ic} placeholder="admin@rjnet.id" autoFocus />
                    {errors.email && <p className="text-xs text-red-500 mt-1">{errors.email}</p>}
                </div>
                <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                    <input type="password" value={data.password} onChange={(e) => setData("password", e.target.value)} className={ic} placeholder="••••••••" />
                    {errors.password && <p className="text-xs text-red-500 mt-1">{errors.password}</p>}
                </div>
                <div className="flex items-center justify-between">
                    <label className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <input type="checkbox" checked={data.remember} onChange={(e) => setData("remember", e.target.checked)} className="rounded" /> Ingat saya
                    </label>
                    <a href="/auth/forgot-password" className="text-sm text-primary-500 hover:underline">Lupa password?</a>
                </div>
                <button type="submit" disabled={processing} className="w-full py-2.5 bg-primary-500 text-white rounded-lg text-sm font-medium hover:bg-primary-600 disabled:opacity-50">
                    {processing ? "Memproses..." : "Login"}
                </button>
            </form>
            <p className="text-center text-sm text-gray-500 mt-6">Belum punya akun? <a href="/auth/register" className="text-primary-500 hover:underline">Daftar</a></p>
        </GuestLayout>
    );
};

export default Login;
