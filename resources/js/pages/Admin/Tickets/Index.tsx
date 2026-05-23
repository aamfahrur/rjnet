import React from "react";
import AdminLayout from "../../../layouts/AdminLayout";
import { Head, Link } from "@inertiajs/react";

interface Ticket { id: number; ticket_number: string; subject: string; customer: { full_name: string } | null; status: { value: string; label: string; badgeClass: string }; priority: { value: string; label: string; color: string }; assigned_technician: { name: string } | null; created_at: string; }
interface Props { tickets: { data: Ticket[]; total: number; }; }

const Index: React.FC<Props> = ({ tickets }) => (
    <AdminLayout>
        <Head title="Tiket Support" />
        <div className="mb-6"><h1 className="text-xl font-bold text-black dark:text-white">Tiket Support</h1><p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{tickets.total} tiket</p></div>
        <div className="bg-white dark:bg-[#0c1427] rounded-lg border border-gray-100 dark:border-[#172036] overflow-hidden">
            <div className="overflow-x-auto">
                <table className="w-full text-sm">
                    <thead className="bg-gray-50 dark:bg-[#15203c]">
                        <tr>{["Ticket", "Subject", "Pelanggan", "Priority", "Teknisi", "Status", "Tanggal"].map(h => <th key={h} className="text-left px-4 py-3 font-medium text-gray-600 dark:text-gray-300">{h}</th>)}</tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100 dark:divide-[#172036]">
                        {tickets.data.map(t => (
                            <tr key={t.id} className="hover:bg-gray-50 dark:hover:bg-[#15203c]">
                                <td className="px-4 py-3"><Link href={`/admin/tickets/${t.id}`} className="font-mono text-xs text-primary-500">{t.ticket_number}</Link></td>
                                <td className="px-4 py-3"><Link href={`/admin/tickets/${t.id}`} className="font-medium text-black dark:text-white hover:text-primary-500">{t.subject}</Link></td>
                                <td className="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{t.customer?.full_name || "-"}</td>
                                <td className="px-4 py-3"><span className={`inline-block w-2 h-2 rounded-full bg-${t.priority.color}-500 mr-1`} /><span className="text-xs">{t.priority.label}</span></td>
                                <td className="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{t.assigned_technician?.name || <span className="text-gray-400 italic">Unassigned</span>}</td>
                                <td className="px-4 py-3"><span className={`px-2 py-0.5 rounded-full text-xs ${t.status.badgeClass}`}>{t.status.label}</span></td>
                                <td className="px-4 py-3 text-xs text-gray-500">{t.created_at}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
);

export default Index;
