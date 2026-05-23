import React from "react";
import AdminLayout from "../../../layouts/AdminLayout";
import { Head, Link } from "@inertiajs/react";

interface Ticket { id: number; ticket_number: string; subject: string; customer: { full_name: string } | null; status: { value: string; label: string; badgeClass: string }; priority: { value: string; label: string; color: string }; assigned_technician: { name: string } | null; created_at: string; }
interface Props { tickets: { data: Ticket[]; total: number; }; }

const Index: React.FC<Props> = ({ tickets }) => (
    <AdminLayout>
        <Head title="Tiket Support" />
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h1 className="text-2xl font-bold text-gray-800 dark:text-white">Tiket Support</h1>
                <p className="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Total <span className="font-semibold text-gray-700 dark:text-gray-300">{tickets.total}</span> tiket</p>
            </div>
        </div>
        <div className="bg-white dark:bg-[#0c1427] rounded-xl border border-gray-100 dark:border-[#172036] overflow-hidden shadow-sm">
            <div className="overflow-x-auto">
                <table className="w-full text-sm">
                    <thead>
                        <tr className="border-b border-gray-100 dark:border-[#172036] bg-gray-50/80 dark:bg-[#0a0f1a]">
                            {["Ticket", "Subject", "Pelanggan", "Priority", "Teknisi", "Status", "Tanggal"].map((h, i) => (
                                <th key={h} className={`text-left px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 ${i === 0 ? "pl-6" : ""} ${i === 6 ? "pr-6" : ""}`}>{h}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-50 dark:divide-[#0f1525]">
                        {tickets.data.map((t) => (
                            <tr key={t.id} className="hover:bg-gray-50/50 dark:hover:bg-[#0f1525] transition-colors">
                                <td className="px-4 py-3.5 pl-6">
                                    <Link href={`/admin/tickets/${t.id}`} className="font-mono text-xs font-medium text-primary-500 hover:text-primary-600">{t.ticket_number}</Link>
                                </td>
                                <td className="px-4 py-3.5">
                                    <Link href={`/admin/tickets/${t.id}`} className="font-medium text-gray-800 dark:text-white hover:text-primary-500 transition-colors line-clamp-1">{t.subject}</Link>
                                </td>
                                <td className="px-4 py-3.5 text-xs text-gray-600 dark:text-gray-300">{t.customer?.full_name || <span className="text-gray-400 italic">-</span>}</td>
                                <td className="px-4 py-3.5">
                                    <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold ${
                                        t.priority.value === "high" || t.priority.value === "urgent"
                                            ? "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400"
                                            : t.priority.value === "medium"
                                                ? "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400"
                                                : "bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"
                                    }`}>
                                        <span className={`w-1.5 h-1.5 rounded-full ${
                                            t.priority.value === "high" || t.priority.value === "urgent" ? "bg-red-500" : t.priority.value === "medium" ? "bg-amber-500" : "bg-blue-500"
                                        }`} />
                                        {t.priority.label}
                                    </span>
                                </td>
                                <td className="px-4 py-3.5 text-xs text-gray-600 dark:text-gray-300">
                                    {t.assigned_technician?.name || <span className="text-gray-400 italic">Unassigned</span>}
                                </td>
                                <td className="px-4 py-3.5">
                                    <span className={`inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold ${t.status.badgeClass}`}>
                                        <span className={`w-1.5 h-1.5 rounded-full ${t.status.value === "open" ? "bg-blue-500" : t.status.value === "in_progress" ? "bg-amber-500" : "bg-green-500"}`} />
                                        {t.status.label}
                                    </span>
                                </td>
                                <td className="px-4 py-3.5 pr-6 text-xs text-gray-500 whitespace-nowrap">{t.created_at}</td>
                            </tr>
                        ))}
                        {tickets.data.length === 0 && (
                            <tr>
                                <td colSpan={7} className="px-6 py-16 text-center">
                                    <div className="flex flex-col items-center">
                                        <div className="w-14 h-14 rounded-full bg-gray-100 dark:bg-[#15203c] flex items-center justify-center mb-3">
                                            <i className="material-symbols-outlined !text-[28px] text-gray-400 dark:text-gray-500">confirmation_number</i>
                                        </div>
                                        <h3 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tidak ada tiket</h3>
                                        <p className="text-xs text-gray-400 dark:text-gray-500">Semua tiket telah terselesaikan</p>
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
