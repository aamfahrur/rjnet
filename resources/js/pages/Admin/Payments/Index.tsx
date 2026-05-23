import React from "react";
import AdminLayout from "../../../layouts/AdminLayout";
import { Head } from "@inertiajs/react";

const Index: React.FC = () => (
    <AdminLayout>
        <Head title="Pembayaran" />
        <h1 className="text-xl font-bold text-black dark:text-white mb-6">Pembayaran</h1>
        <div className="bg-white dark:bg-[#0c1427] rounded-lg border border-gray-100 dark:border-[#172036] overflow-hidden">
            <table className="w-full text-sm">
                <thead className="bg-gray-50 dark:bg-[#15203c]"><tr>{["No. Pembayaran", "Invoice", "Pelanggan", "Jumlah", "Gateway", "Status", "Tanggal"].map(h => <th key={h} className="text-left px-4 py-3 font-medium text-gray-600 dark:text-gray-300">{h}</th>)}</tr></thead>
                <tbody className="divide-y divide-gray-100 dark:divide-[#172036]">
                    {[{ id: 1, number: "PAY-001", inv: "INV-001", cust: "Budi S.", amt: "Rp 350.000", gw: "iPaymu", st: "success", dt: "20 Mei" }].map(p => (
                        <tr key={p.id} className="hover:bg-gray-50 dark:hover:bg-[#15203c]">
                            <td className="px-4 py-3 font-mono text-xs text-primary-500">{p.number}</td>
                            <td className="px-4 py-3 text-xs">{p.inv}</td><td className="px-4 py-3">{p.cust}</td>
                            <td className="px-4 py-3 font-medium text-black dark:text-white">{p.amt}</td>
                            <td className="px-4 py-3">{p.gw}</td>
                            <td className="px-4 py-3"><span className="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-800">{p.st}</span></td>
                            <td className="px-4 py-3 text-xs text-gray-500">{p.dt}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    </AdminLayout>
);

export default Index;
