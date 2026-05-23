import React, { useState, useEffect } from "react";
import AdminLayout from "../../../layouts/AdminLayout";
import { Head, Link, router } from "@inertiajs/react";

interface Customer {
    id: number; customer_code: string; full_name: string; email: string | null;
    phone: string; status: { value: string; label: string; badgeClass: string };
    current_package: string | null; registration_date: string; overdue_count?: number;
}
interface Props {
    customers: { data: Customer[]; current_page: number; last_page: number; per_page: number; total: number; links: any[] };
    filters?: { search?: string; status?: string };
}

const statusFilters = [
    { value: "", label: "Semua", color: "bg-gray-100 dark:bg-[#15203c] text-gray-600 dark:text-gray-300" },
    { value: "active", label: "Aktif", color: "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400" },
    { value: "suspended", label: "Suspend", color: "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400" },
    { value: "inactive", label: "Nonaktif", color: "bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400" },
];

const Index: React.FC<Props> = ({ customers, filters = {} }) => {
    const [search, setSearch] = useState(filters.search || "");
    const [activeStatus, setActiveStatus] = useState(filters.status || "");

    useEffect(() => {
        const timeout = setTimeout(() => {
            router.get("/admin/customers", { search, status: activeStatus }, { preserveState: true, replace: true });
        }, 300);
        return () => clearTimeout(timeout);
    }, [search, activeStatus]);

    const handleStatusFilter = (status: string) => {
        setActiveStatus(status);
        router.get("/admin/customers", { search, status }, { preserveState: true, replace: true });
    };

    return (
        <AdminLayout>
            <Head title="Pelanggan" />
            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <div>
                    <h1 className="text-2xl font-bold text-gray-800 dark:text-white">Pelanggan</h1>
                    <p className="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        Total <span className="font-semibold text-gray-700 dark:text-gray-300">{customers.total}</span> pelanggan terdaftar
                    </p>
                </div>
                <Link href="/admin/customers/create" className="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm font-medium transition-colors shadow-sm shadow-primary-500/25">
                    <i className="material-symbols-outlined !text-[18px]">person_add</i>
                    Tambah Pelanggan
                </Link>
            </div>

            {/* Filters */}
            <div className="flex flex-col sm:flex-row gap-3 mb-4">
                <div className="relative flex-1">
                    <i className="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 !text-[20px]">search</i>
                    <input type="text" placeholder="Cari nama, kode, atau telepon..."
                        value={search} onChange={(e) => setSearch(e.target.value)}
                        className="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-[#0c1427] border border-gray-200 dark:border-[#172036] rounded-lg text-sm text-gray-700 dark:text-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all" />
                </div>
                <div className="flex gap-2 flex-wrap">
                    {statusFilters.map((f) => (
                        <button key={f.value} onClick={() => handleStatusFilter(f.value)}
                            className={`px-3 py-2 rounded-lg text-xs font-medium transition-all ${activeStatus === f.value
                                    ? "bg-primary-500 text-white shadow-sm shadow-primary-500/25"
                                    : "bg-white dark:bg-[#0c1427] border border-gray-200 dark:border-[#172036] text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-[#15203c]"
                                }`}>
                            {f.label}
                        </button>
                    ))}
                </div>
            </div>

            {/* Table */}
            <div className="bg-white dark:bg-[#0c1427] rounded-xl border border-gray-100 dark:border-[#172036] overflow-hidden shadow-sm">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-gray-100 dark:border-[#172036] bg-gray-50/80 dark:bg-[#0a0f1a]">
                                {["Kode", "Nama", "Telepon", "Paket", "Status", "Tgl Daftar", ""].map((h, i) => (
                                    <th key={i} className={`text-left px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 ${i === 0 ? "pl-6" : ""} ${i === 6 ? "pr-6" : ""}`}>{h}</th>
                                ))}
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-50 dark:divide-[#0f1525]">
                            {customers.data.map((c) => (
                                <tr key={c.id} className="hover:bg-gray-50/50 dark:hover:bg-[#0f1525] transition-colors">
                                    <td className="px-4 py-3.5 pl-6">
                                        <span className="text-xs font-mono font-medium bg-gray-100 dark:bg-[#15203c] text-gray-600 dark:text-gray-300 px-2.5 py-1 rounded-md tracking-wide">{c.customer_code}</span>
                                    </td>
                                    <td className="px-4 py-3.5">
                                        <Link href={`/admin/customers/${c.id}`} className="font-medium text-gray-800 dark:text-white hover:text-primary-500 transition-colors">
                                            {c.full_name}
                                        </Link>
                                        {c.email && <p className="text-xs text-gray-400 mt-0.5">{c.email}</p>}
                                    </td>
                                    <td className="px-4 py-3.5 text-gray-600 dark:text-gray-300 text-xs">{c.phone}</td>
                                    <td className="px-4 py-3.5">
                                        {c.current_package
                                            ? <span className="text-xs font-medium text-gray-700 dark:text-gray-300">{c.current_package}</span>
                                            : <span className="text-xs text-gray-400 italic">Belum ada paket</span>}
                                    </td>
                                    <td className="px-4 py-3.5">
                                        <span className={`inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold ${c.status.badgeClass}`}>
                                            <span className={`w-1.5 h-1.5 rounded-full ${c.status.value === "active" ? "bg-green-500" : c.status.value === "suspended" ? "bg-red-500" : "bg-gray-400"}`} />
                                            {c.status.label}
                                        </span>
                                    </td>
                                    <td className="px-4 py-3.5 text-gray-500 text-xs whitespace-nowrap">{c.registration_date}</td>
                                    <td className="px-4 py-3.5 pr-6">
                                        <Link href={`/admin/customers/${c.id}`} className="inline-flex items-center gap-1 text-primary-500 hover:text-primary-600 text-xs font-medium">
                                            Detail
                                            <i className="material-symbols-outlined !text-[14px]">arrow_forward</i>
                                        </Link>
                                    </td>
                                </tr>
                            ))}
                            {customers.data.length === 0 && (
                                <tr>
                                    <td colSpan={7} className="px-6 py-16 text-center">
                                        <div className="flex flex-col items-center">
                                            <div className="w-14 h-14 rounded-full bg-gray-100 dark:bg-[#15203c] flex items-center justify-center mb-3">
                                                <i className="material-symbols-outlined !text-[28px] text-gray-400 dark:text-gray-500">{activeStatus ? "filter_alt_off" : "people_outline"}</i>
                                            </div>
                                            <h3 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tidak ada pelanggan</h3>
                                            <p className="text-xs text-gray-400 dark:text-gray-500">
                                                {activeStatus ? "Tidak ada pelanggan dengan filter ini" : "Belum ada pelanggan terdaftar"}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                {customers.last_page > 1 && (
                    <div className="px-6 py-3.5 border-t border-gray-100 dark:border-[#172036] flex items-center justify-between bg-gray-50/50 dark:bg-[#0a0f1a]">
                        <p className="text-xs text-gray-500 dark:text-gray-400">
                            Menampilkan <span className="font-medium">{(customers.current_page - 1) * customers.per_page + 1}–{Math.min(customers.current_page * customers.per_page, customers.total)}</span> dari <span className="font-medium">{customers.total}</span>
                        </p>
                        <div className="flex gap-1">
                            {customers.links.map((l: any, i: number) => {
                                if (l.label.includes("Sebelumnya") || l.label.includes("Berikutnya")) {
                                    return (
                                        <Link key={i} href={l.url || "#"}
                                            className={`px-3 py-1.5 rounded-lg text-xs font-medium transition-colors ${l.url ? "text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#15203c]" : "text-gray-300 dark:text-gray-600 cursor-not-allowed"
                                                }`}>
                                            {l.label.replace("&laquo; ", "").replace(" &raquo;", "")}
                                        </Link>
                                    );
                                }
                                return (
                                    <Link key={i} href={l.url || "#"}
                                        className={`w-8 h-8 flex items-center justify-center rounded-lg text-xs font-medium transition-colors ${l.active ? "bg-primary-500 text-white shadow-sm" : l.url ? "text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#15203c]" : "text-gray-300 dark:text-gray-600 cursor-not-allowed"
                                            }`}>
                                        {l.label}
                                    </Link>
                                );
                            })}
                        </div>
                    </div>
                )}
            </div>
        </AdminLayout>
    );
};

export default Index;
