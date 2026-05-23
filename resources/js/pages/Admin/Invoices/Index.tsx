import React from "react";
import AdminLayout from "../../../layouts/AdminLayout";
import { Head, Link } from "@inertiajs/react";

interface Invoice { id: number; invoice_number: string; customer: { full_name: string } | null; total_amount: number; total_formatted: string; remaining_amount: number; status: { value: string; label: string; badgeClass: string }; issue_date: string; due_date: string; }
interface Props { invoices: { data: Invoice[]; total: number; current_page?: number; last_page?: number; per_page?: number; links?: any[] }; filters?: { search?: string; status?: string } }

const Index: React.FC<Props> = ({ invoices }) => (
    <AdminLayout>
        <Head title="Invoice" />
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h1 className="text-2xl font-bold text-gray-800 dark:text-white">Invoice</h1>
                <p className="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Total <span className="font-semibold text-gray-700 dark:text-gray-300">{invoices.total}</span> invoice</p>
            </div>
        </div>
        <div className="bg-white dark:bg-[#0c1427] rounded-xl border border-gray-100 dark:border-[#172036] overflow-hidden shadow-sm">
            <div className="overflow-x-auto">
                <table className="w-full text-sm">
                    <thead>
                        <tr className="border-b border-gray-100 dark:border-[#172036] bg-gray-50/80 dark:bg-[#0a0f1a]">
                            {["No. Invoice", "Pelanggan", "Jatuh Tempo", "Total", "Sisa", "Status"].map((h, i) => (
                                <th key={h} className={`text-left px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 ${i === 0 ? "pl-6" : ""} ${i === 5 ? "pr-6" : ""}`}>{h}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-50 dark:divide-[#0f1525]">
                        {invoices.data.map((inv) => (
                            <tr key={inv.id} className="hover:bg-gray-50/50 dark:hover:bg-[#0f1525] transition-colors">
                                <td className="px-4 py-3.5 pl-6">
                                    <Link href={`/admin/invoices/${inv.id}`} className="font-mono text-xs font-medium text-primary-500 hover:text-primary-600">{inv.invoice_number}</Link>
                                </td>
                                <td className="px-4 py-3.5 text-gray-700 dark:text-gray-300 font-medium text-xs">{inv.customer?.full_name || <span className="text-gray-400 italic">-</span>}</td>
                                <td className="px-4 py-3.5 text-gray-500 text-xs whitespace-nowrap">{inv.due_date}</td>
                                <td className="px-4 py-3.5 font-semibold text-gray-800 dark:text-white text-xs">{inv.total_formatted}</td>
                                <td className="px-4 py-3.5 text-gray-600 dark:text-gray-300 text-xs">
                                    {inv.remaining_amount > 0
                                        ? <span className="text-red-500 font-medium">Rp {inv.remaining_amount.toLocaleString("id-ID")}</span>
                                        : <span className="text-green-500 font-medium">Lunas</span>}
                                </td>
                                <td className="px-4 py-3.5 pr-6">
                                    <span className={`inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold ${inv.status.badgeClass}`}>
                                        <span className={`w-1.5 h-1.5 rounded-full ${inv.status.value === "paid" ? "bg-green-500" : inv.status.value === "pending" ? "bg-amber-500" : "bg-red-500"}`} />
                                        {inv.status.label}
                                    </span>
                                </td>
                            </tr>
                        ))}
                        {invoices.data.length === 0 && (
                            <tr>
                                <td colSpan={6} className="px-6 py-16 text-center">
                                    <div className="flex flex-col items-center">
                                        <div className="w-14 h-14 rounded-full bg-gray-100 dark:bg-[#15203c] flex items-center justify-center mb-3">
                                            <i className="material-symbols-outlined !text-[28px] text-gray-400 dark:text-gray-500">receipt_long</i>
                                        </div>
                                        <h3 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tidak ada invoice</h3>
                                        <p className="text-xs text-gray-400 dark:text-gray-500">Belum ada invoice yang dibuat</p>
                                    </div>
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
);

export default Index;
