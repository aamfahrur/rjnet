import React from "react";
import AdminLayout from "../../../layouts/AdminLayout";
import { Head } from "@inertiajs/react";

const Index: React.FC = () => (
    <AdminLayout>
        <Head title="Pembayaran" />
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h1 className="text-2xl font-bold text-gray-800 dark:text-white">Pembayaran</h1>
                <p className="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Riwayat semua pembayaran</p>
            </div>
        </div>
        <div className="bg-white dark:bg-[#0c1427] rounded-xl border border-gray-100 dark:border-[#172036] overflow-hidden shadow-sm">
            <div className="overflow-x-auto">
                <table className="w-full text-sm">
                    <thead>
                        <tr className="border-b border-gray-100 dark:border-[#172036] bg-gray-50/80 dark:bg-[#0a0f1a]">
                            {["No. Pembayaran", "Invoice", "Pelanggan", "Jumlah", "Gateway", "Status", "Tanggal"].map((h, i) => (
                                <th key={h} className={`text-left px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 ${i === 0 ? "pl-6" : ""} ${i === 6 ? "pr-6" : ""}`}>{h}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-50 dark:divide-[#0f1525]">
                        <tr>
                            <td colSpan={7} className="px-6 py-16 text-center">
                                <div className="flex flex-col items-center">
                                    <div className="w-14 h-14 rounded-full bg-gray-100 dark:bg-[#15203c] flex items-center justify-center mb-3">
                                        <i className="material-symbols-outlined !text-[28px] text-gray-400 dark:text-gray-500">payments</i>
                                    </div>
                                    <h3 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Belum ada pembayaran</h3>
                                    <p className="text-xs text-gray-400 dark:text-gray-500">Pembayaran akan muncul di sini setelah pelanggan melakukan pembayaran</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
);

export default Index;
