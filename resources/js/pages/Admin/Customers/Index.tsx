import React from "react";
import AdminLayout from "../../../layouts/AdminLayout";
import { Head, Link } from "@inertiajs/react";

interface Customer { id: number; customer_code: string; full_name: string; email: string | null; phone: string; status: { value: string; label: string; badgeClass: string }; current_package: string | null; registration_date: string; overdue_count?: number; }
interface Props { customers: { data: Customer[]; current_page: number; last_page: number; per_page: number; total: number; links: any[]; }; }

const Index: React.FC<Props> = ({ customers }) => (
    <AdminLayout>
        <Head title="Pelanggan" />
        <div className="flex items-center justify-between mb-6">
            <div><h1 className="text-xl font-bold text-black dark:text-white">Pelanggan</h1><p className="text-sm text-gray-500 dark:text-gray-400 mt-1">Total {customers.total} pelanggan</p></div>
            <Link href="/admin/customers/create" className="inline-flex items-center gap-2 bg-primary-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-600"><i className="material-symbols-outlined !text-[18px]">person_add</i>Tambah</Link>
        </div>
        <div className="bg-white dark:bg-[#0c1427] rounded-lg border border-gray-100 dark:border-[#172036] overflow-hidden">
            <div className="overflow-x-auto">
                <table className="w-full text-sm">
                    <thead className="bg-gray-50 dark:bg-[#15203c]">
                        <tr>{["Kode", "Nama", "Telepon", "Paket", "Status", "Tgl Daftar", ""].map(h => <th key={h} className="text-left px-4 py-3 font-medium text-gray-600 dark:text-gray-300">{h}</th>)}</tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100 dark:divide-[#172036]">
                        {customers.data.map(c => (
                            <tr key={c.id} className="hover:bg-gray-50 dark:hover:bg-[#15203c]">
                                <td className="px-4 py-3"><span className="text-xs font-mono bg-gray-100 dark:bg-[#15203c] px-2 py-0.5 rounded">{c.customer_code}</span></td>
                                <td className="px-4 py-3"><Link href={`/admin/customers/${c.id}`} className="font-medium text-primary-500 hover:underline">{c.full_name}</Link>{c.email && <p className="text-xs text-gray-400">{c.email}</p>}</td>
                                <td className="px-4 py-3 text-gray-600 dark:text-gray-300">{c.phone}</td>
                                <td className="px-4 py-3 text-gray-600 dark:text-gray-300">{c.current_package || <span className="text-gray-400 italic">-</span>}</td>
                                <td className="px-4 py-3"><span className={`inline-block px-2 py-1 rounded-full text-xs font-medium ${c.status.badgeClass}`}>{c.status.label}</span></td>
                                <td className="px-4 py-3 text-gray-500 text-xs">{c.registration_date}</td>
                                <td className="px-4 py-3"><Link href={`/admin/customers/${c.id}`} className="text-primary-500 hover:text-primary-600 text-sm font-medium">Detail</Link></td>
                            </tr>
                        ))}
                        {customers.data.length === 0 && <tr><td colSpan={7} className="px-4 py-12 text-center text-gray-500">Tidak ada data</td></tr>}
                    </tbody>
                </table>
            </div>
            {customers.last_page > 1 && (
                <div className="px-4 py-3 border-t border-gray-100 dark:border-[#172036] flex items-center justify-between">
                    <p className="text-sm text-gray-500">Menampilkan {(customers.current_page - 1) * customers.per_page + 1}–{Math.min(customers.current_page * customers.per_page, customers.total)} dari {customers.total}</p>
                    <div className="flex gap-1">
                        {customers.links.map((l: any, i: number) => <Link key={i} href={l.url || "#"} className={`px-3 py-1 rounded text-sm ${l.active ? "bg-primary-500 text-white" : l.url ? "text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#15203c]" : "text-gray-400 cursor-not-allowed"}`} dangerouslySetInnerHTML={{ __html: l.label }} />)}
                    </div>
                </div>
            )}
        </div>
    </AdminLayout>
);

export default Index;
