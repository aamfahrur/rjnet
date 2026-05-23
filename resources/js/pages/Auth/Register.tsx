import React from "react";
import GuestLayout from "../../layouts/GuestLayout";
import { Head, useForm } from "@inertiajs/react";

const Register: React.FC = () => {
    const { data, setData, post, processing, errors } = useForm({ name: "", email: "", phone: "", password: "", password_confirmation: "" });
    const ic = "w-full px-3 py-2.5 border border-gray-200 dark:border-[#172036] rounded-lg text-sm bg-white dark:bg-[#0c1427] text-black dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none";

    return (
        <GuestLayout>
            <Head title="Daftar" />
            <h2 className="text-xl font-bold text-black dark:text-white text-center mb-1">Daftar Akun Baru</h2>
            <p className="text-sm text-gray-500 text-center mb-6">Registrasi pelanggan RJNet</p>
            <form onSubmit={(e) => { e.preventDefault(); post("/auth/register"); }} className="space-y-4">
                {["name", "email", "phone", "password", "password_confirmation"].map((f) => (
                    <div key={f}>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {f === "name" ? "Nama Lengkap" : f === "password_confirmation" ? "Konfirmasi Password" : f.charAt(0).toUpperCase() + f.slice(1)}
                        </label>
                        <input
                            type={f.startsWith("password") ? "password" : f === "email" ? "email" : "text"}
                            value={(data as any)[f]}
                            onChange={(e) => setData(f as any, e.target.value)}
                            className={ic}
                            placeholder={f === "email" ? "email@example.com" : f === "phone" ? "0812xxxxxxxx" : ""}
                        />
                    </div>
                ))}
                <button type="submit" disabled={processing} className="w-full py-2.5 bg-primary-500 text-white rounded-lg text-sm font-medium hover:bg-primary-600 disabled:opacity-50">
                    {processing ? "Memproses..." : "Daftar"}
                </button>
            </form>
            <p className="text-center text-sm text-gray-500 mt-6">Sudah punya akun? <a href="/auth/login" className="text-primary-500 hover:underline">Login</a></p>
        </GuestLayout>
    );
};

export default Register;
