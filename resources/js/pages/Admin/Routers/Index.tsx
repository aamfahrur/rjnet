import React from "react";
import AdminLayout from "../../../layouts/AdminLayout";
import { Head, Link } from "@inertiajs/react";

interface Router { id: number; name: string; host: string; status: { value: string; label: string; dotClass: string }; router_os_version: string | null; pppoe_accounts_count: number; }
interface Props { routers: { data: Router[]; total: number; }; }

const Index: React.FC<Props> = ({ routers }) => (
    <AdminLayout>
        <Head title="Router" />
        <div className="flex items-center justify-between mb-6">
            <div><h1 className="text-xl font-bold text-black dark:text-white">Router</h1><p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{routers.total} router</p></div>
            <Link href="/admin/routers/create" className="inline-flex items-center gap-2 bg-primary-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-600"><i className="material-symbols-outlined !text-[18px]">add_circle</i>Tambah</Link>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {routers.data.map(r => (
                <Link key={r.id} href={`/admin/routers/${r.id}`} className="bg-white dark:bg-[#0c1427] rounded-lg border border-gray-100 dark:border-[#172036] p-5 hover:shadow-md transition-shadow">
                    <div className="flex items-start justify-between mb-3">
                        <div><h3 className="font-semibold text-black dark:text-white">{r.name}</h3><p className="text-xs text-gray-500 font-mono">{r.host}</p></div>
                        <span className={`inline-block w-2.5 h-2.5 rounded-full ${r.status.dotClass}`} />
                    </div>
                    <div className="flex items-center gap-4 text-xs text-gray-500">
                        <span><i className="material-symbols-outlined !text-[14px] align-text-bottom">person</i> {r.pppoe_accounts_count} akun</span>
                        {r.router_os_version && <span>v{r.router_os_version}</span>}
                        <span className="ml-auto text-gray-400">{r.status.label}</span>
                    </div>
                </Link>
            ))}
        </div>
    </AdminLayout>
);

export default Index;
