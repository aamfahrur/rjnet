import React from "react";
import AdminLayout from "../../../layouts/AdminLayout";
import { Head, Link } from "@inertiajs/react";

interface Router { id: number; name: string; host: string; status: { value: string; label: string; dotClass: string }; router_os_version: string | null; pppoe_accounts_count: number; }
interface Props { routers: { data: Router[]; total: number; }; }

const Index: React.FC<Props> = ({ routers }) => (
    <AdminLayout>
        <Head title="Router" />
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h1 className="text-2xl font-bold text-gray-800 dark:text-white">Router</h1>
                <p className="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Total <span className="font-semibold text-gray-700 dark:text-gray-300">{routers.total}</span> router</p>
            </div>
            <Link href="/admin/routers/create" className="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm font-medium transition-colors shadow-sm shadow-primary-500/25">
                <i className="material-symbols-outlined !text-[18px]">add_circle</i>
                Tambah Router
            </Link>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {routers.data.map((r) => (
                <Link key={r.id} href={`/admin/routers/${r.id}`} className="group bg-white dark:bg-[#0c1427] rounded-xl border border-gray-100 dark:border-[#172036] p-5 hover:border-primary-200 dark:hover:border-primary-800 hover:shadow-lg hover:shadow-primary-500/5 transition-all duration-300">
                    <div className="flex items-start justify-between mb-3">
                        <div>
                            <h3 className="font-semibold text-gray-800 dark:text-white group-hover:text-primary-500 transition-colors">{r.name}</h3>
                            <p className="text-xs text-gray-500 font-mono mt-0.5">{r.host}</p>
                        </div>
                        <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold ${
                            r.status.value === "online" ? "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400" : "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400"
                        }`}>
                            <span className={`w-1.5 h-1.5 rounded-full ${r.status.dotClass}`} />
                            {r.status.label}
                        </span>
                    </div>
                    <div className="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                        <span className="inline-flex items-center gap-1">
                            <i className="material-symbols-outlined !text-[14px]">person</i>
                            <span className="font-medium text-gray-700 dark:text-gray-300">{r.pppoe_accounts_count}</span> akun
                        </span>
                        {r.router_os_version && (
                            <span className="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 dark:bg-[#15203c] rounded-md text-gray-600 dark:text-gray-300">
                                v{r.router_os_version}
                            </span>
                        )}
                    </div>
                </Link>
            ))}
            {routers.data.length === 0 && (
                <div className="md:col-span-2 bg-white dark:bg-[#0c1427] rounded-xl border border-gray-100 dark:border-[#172036] p-16 text-center">
                    <div className="w-14 h-14 rounded-full bg-gray-100 dark:bg-[#15203c] flex items-center justify-center mx-auto mb-3">
                        <i className="material-symbols-outlined !text-[28px] text-gray-400 dark:text-gray-500">dns</i>
                    </div>
                    <h3 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tidak ada router</h3>
                    <p className="text-xs text-gray-400 dark:text-gray-500">Belum ada router yang terdaftar</p>
                </div>
            )}
        </div>
    </AdminLayout>
);

export default Index;
