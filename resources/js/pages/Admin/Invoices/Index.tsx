import React from "react";
import AdminLayout from "../../../layouts/AdminLayout";
import { Head, Link } from "@inertiajs/react";

interface Invoice { id: number; invoice_number: string; customer: { full_name: string } | null; total_amount: number; total_formatted: string; remaining_amount: number; status: { value: string; label: string; badgeClass: string }; issue_date: string; due_date: string; }
interface Props { invoices: { data: Invoice[]; total: number; }; }

const Index: React.FC<Props> = ({ invoices }) => (
    <AdminLayout>
        <Head title="Invoice" />
        <div className="flex items-center justify-between mb-6">
            <div><h1 className="text-xl font-bold text-black dark:text-white">Invoice</h1><p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{invoices.total} invoice</p></div>
        </div>
        <div className="bg-white dark:bg-[#0c1427] rounded-lg border border-gray-100 dark:border-[#172036] overflow-hidden">
            <div className="overflow-x-auto">
                <table className="w-full text-sm">
                    <thead className="bg-gray-50 dark:bg-[#15203c]">
                        <tr>{["No. Invoice", "Pelanggan", "Jatuh Tempo", "Total", "Sisa", "Status"].map(h => <th key={h} className="text-left px-4 py-3 font-medium text-gray-600 dark:text-gray-300">{h}</th>)}</tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100 dark:divide-[#172036]">
                        {invoices.data.map(inv => (
                            <tr key={inv.id} className="hover:bg-gray-50 dark:hover:bg-[#15203c]">
                                <td className="px-4 py-3"><Link href={`/admin/invoices/${inv.id}`} className="font-mono text-xs text-primary-500">{inv.invoice_number}</Link></td>
                                <td className="px-4 py-3 text-gray-600 dark:text-gray-300">{inv.customer?.full_name || "-"}</td>
                                <td className="px-4 py-3 text-xs">{inv.due_date}</td>
                                <td className="px-4 py-3 font-medium text-black dark:text-white">{inv.total_formatted}</td>
                                <td className="px-4 py-3 text-gray-600 dark:text-gray-300">{inv.remaining_amount > 0 ? `Rp ${inv.remaining_amount.toLocaleString("id-ID")}` : "-"}</td>
                                <td className="px-4 py-3"><span className={`px-2 py-0.5 rounded-full text-xs ${inv.status.badgeClass}`}>{inv.status.label}</span></td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
);

export default Index;
