import React from "react";
import CustomerLayout from "../../layouts/CustomerLayout";
import { Head, Link, usePage } from "@inertiajs/react";

const Dashboard: React.FC = () => {
    const { auth } = usePage().props as any;
    return (
        <CustomerLayout>
            <Head title="Dashboard" />
            <div className="mb-6"><h1 className="text-2xl font-bold text-black dark:text-white">Halo, {auth?.user?.name || "Pelanggan"}! 👋</h1><p className="text-gray-500 dark:text-gray-400 mt-1">Selamat datang di panel pelanggan RJNet</p></div>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                {[{ label: "Status Internet", value: "Online", icon: "w-3 h-3 rounded-full bg-green-500 animate-pulse", color: "bg-green-100 dark:bg-green-900" }, { label: "Tagihan", value: "1 belum dibayar", icon: "receipt_long", color: "bg-orange-100 dark:bg-orange-900" }, { label: "Tiket", value: "0 open", icon: "support_agent", color: "bg-blue-100 dark:bg-blue-900" }].map(c => (
                    <div key={c.label} className="bg-white dark:bg-[#0c1427] rounded-lg border border-gray-100 dark:border-[#172036] p-5"><div className="flex items-center gap-3"><div className={`w-10 h-10 rounded-full ${c.color} flex items-center justify-center`}>{c.icon.startsWith("w-") ? <span className={c.icon} /> : <i className={`material-symbols-outlined text-${c.color.includes("green") ? "green" : c.color.includes("orange") ? "orange" : "blue"}-500`}>{c.icon}</i>}</div><div><p className="text-sm text-gray-500">{c.label}</p><p className="font-bold text-black dark:text-white">{c.value}</p></div></div></div>
                ))}
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="bg-white dark:bg-[#0c1427] rounded-lg border border-gray-100 dark:border-[#172036] p-6"><h3 className="font-semibold text-black dark:text-white mb-3">Paket Saya</h3><p className="text-lg font-bold text-primary-500">Paket Premium 25 Mbps</p><p className="text-sm text-gray-500 mt-1">Tagihan berikutnya: 2026-06-01</p></div>
                <div className="bg-white dark:bg-[#0c1427] rounded-lg border border-gray-100 dark:border-[#172036] p-6"><h3 className="font-semibold text-black dark:text-white mb-3">Aksi Cepat</h3><div className="grid grid-cols-2 gap-2">
                    {[{ href: "/panel/invoices", icon: "receipt", label: "Bayar Tagihan" }, { href: "/panel/tickets/create", icon: "confirmation_number", label: "Buat Tiket" }, { href: "/panel/status", icon: "monitoring", label: "Cek Status" }, { href: "/panel/profile", icon: "settings", label: "Profil" }].map(a => <Link key={a.href} href={a.href} className="flex items-center gap-2 p-3 rounded-lg border border-gray-100 dark:border-[#172036] hover:bg-gray-50 dark:hover:bg-[#15203c] text-sm"><i className="material-symbols-outlined text-primary-500">{a.icon}</i>{a.label}</Link>)}
                </div></div>
            </div>
        </CustomerLayout>
    );
};

export default Dashboard;
