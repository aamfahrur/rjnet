import React, { useState, useEffect, type ReactNode } from "react";
import { Link, usePage } from "@inertiajs/react";
import { ToastProvider, useToast } from "../components/Toast";

interface AdminLayoutProps { children: ReactNode; }

const menuItems = [
    { key: "dashboard", icon: "dashboard", label: "Dashboard",
        children: [
            { path: "/admin/dashboard", label: "Overview", icon: "space_dashboard" },
            { path: "/admin/monitoring", label: "NOC Monitoring", icon: "monitor_heart" },
        ]},
    { key: "customers", icon: "group", label: "Pelanggan",
        children: [
            { path: "/admin/customers", label: "Semua Pelanggan", icon: "people" },
            { path: "/admin/customers/create", label: "Tambah Pelanggan", icon: "person_add" },
        ]},
    { key: "routers", icon: "router", label: "Router",
        children: [
            { path: "/admin/routers", label: "Semua Router", icon: "dns" },
            { path: "/admin/routers/create", label: "Tambah Router", icon: "add_circle" },
        ]},
    { key: "billing", icon: "receipt_long", label: "Billing",
        children: [
            { path: "/admin/invoices", label: "Invoice", icon: "description" },
            { path: "/admin/payments", label: "Pembayaran", icon: "payments" },
        ]},
    { key: "tickets", icon: "confirmation_number", label: "Tiket Support",
        children: [
            { path: "/admin/tickets", label: "Semua Tiket", icon: "support_agent" },
        ]},
    { key: "network", icon: "share", label: "Jaringan",
        children: [
            { path: "/admin/topology", label: "Topologi", icon: "account_tree" },
        ]},
    { key: "settings", icon: "settings", label: "Pengaturan",
        children: [
            { path: "/admin/users", label: "Pengguna", icon: "manage_accounts" },
            { path: "/admin/settings", label: "Konfigurasi", icon: "tune" },
        ]},
];

const AdminSidebar: React.FC<{ collapsed: boolean; toggleActive: () => void }> = ({ collapsed, toggleActive }) => {
    const { url } = usePage();
    const [openMenus, setOpenMenus] = useState<Record<string, boolean>>({ dashboard: true });
    const toggleMenu = (key: string) => setOpenMenus((p) => ({ ...p, [key]: !p[key] }));
    const isActive = (path: string) => url === path || url.startsWith(path + "/");

    return (
        <aside className={`fixed top-0 left-0 z-40 h-screen bg-white dark:bg-[#0c1427] border-r border-gray-100 dark:border-[#172036] transition-all duration-300 flex flex-col ${
            collapsed ? "w-[72px]" : "w-[260px]"
        }`}>
            <div className="flex items-center h-16 px-4 border-b border-gray-100 dark:border-[#172036] shrink-0">
                <Link href="/admin/dashboard" className="flex items-center gap-3 overflow-hidden">
                    <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-indigo-600 flex items-center justify-center shrink-0 shadow-sm">
                        <span className="text-white font-bold text-sm">R</span>
                    </div>
                    <span className={`font-bold text-lg text-gray-800 dark:text-white whitespace-nowrap transition-all duration-300 ${collapsed ? "opacity-0 w-0 scale-0" : "opacity-100 scale-100"}`}>RJNet</span>
                </Link>
                <button type="button" className="ml-auto lg:hidden hover:text-primary-500" onClick={toggleActive}>
                    <i className="material-symbols-outlined">close</i>
                </button>
            </div>
            <nav className="p-3 space-y-1 overflow-y-auto flex-1">
                {menuItems.map((menu) => (
                    <div key={menu.key}>
                        <button onClick={() => toggleMenu(menu.key)}
                            className={`w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors overflow-hidden ${
                                openMenus[menu.key]
                                    ? "text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-500/10"
                                    : "text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-[#15203c]"
                            }`}>
                            <i className="material-symbols-outlined !text-[22px] shrink-0">{menu.icon}</i>
                            <span className={`flex-1 text-left truncate transition-all duration-300 ${collapsed ? "opacity-0 w-0 overflow-hidden" : "opacity-100"}`}>{menu.label}</span>
                            <i className={`material-symbols-outlined !text-[16px] transition-transform shrink-0 ${openMenus[menu.key] ? "rotate-180" : ""} ${collapsed ? "hidden" : ""}`}>expand_more</i>
                        </button>
                        {openMenus[menu.key] && !collapsed && (
                            <div className="ml-9 mt-1 space-y-0.5">
                                {menu.children.map((child) => (
                                    <Link key={child.path} href={child.path}
                                        className={`flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors ${
                                            isActive(child.path)
                                                ? "text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-500/10 font-medium"
                                                : "text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-[#15203c]"
                                        }`}>
                                        <i className="material-symbols-outlined !text-[16px]">{child.icon}</i>
                                        <span className="truncate">{child.label}</span>
                                    </Link>
                                ))}
                            </div>
                        )}
                    </div>
                ))}
            </nav>
        </aside>
    );
};

const AdminHeader: React.FC<{ toggleActive: () => void; darkMode: boolean; setDarkMode: (v: boolean) => void }> = ({ toggleActive, darkMode, setDarkMode }) => {
    const { auth } = usePage().props as any;
    const user = auth?.user;
    return (
        <header className="sticky top-0 z-30 bg-white/80 dark:bg-[#0c1427]/80 backdrop-blur-lg border-b border-gray-100 dark:border-[#172036]">
            <div className="flex items-center justify-between h-16 px-4 md:px-6">
                <button onClick={toggleActive} className="p-2 hover:bg-gray-100 dark:hover:bg-[#15203c] rounded-lg transition-colors">
                    <i className="material-symbols-outlined text-gray-600 dark:text-gray-300 !text-[22px]">menu</i>
                </button>
                <div className="flex items-center gap-2">
                    <button onClick={() => setDarkMode(!darkMode)} className="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-[#15203c] transition-colors">
                        <i className="material-symbols-outlined text-gray-500 dark:text-gray-400 !text-[20px]">{darkMode ? "light_mode" : "dark_mode"}</i>
                    </button>
                    <div className="flex items-center gap-3 pl-3 border-l border-gray-200 dark:border-gray-700">
                        <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-indigo-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                            {user?.name?.charAt(0)?.toUpperCase() || "A"}
                        </div>
                        <div className="hidden sm:block text-sm leading-tight">
                            <p className="font-semibold text-gray-800 dark:text-white">{user?.name || "Admin"}</p>
                            <p className="text-xs text-gray-500 dark:text-gray-400">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    );
};

const AdminFooter: React.FC = () => (
    <footer className="bg-white dark:bg-[#0c1427] border-t border-gray-100 dark:border-[#172036] py-4 px-6 text-center">
        <p className="text-sm text-gray-500">&copy; {new Date().getFullYear()} <span className="font-semibold text-gray-700 dark:text-gray-300">RJNet</span>. All rights reserved.</p>
    </footer>
);

const AdminLayoutContent: React.FC<AdminLayoutProps> = ({ children }) => {
    const [collapsed, setCollapsed] = useState(false);
    const [darkMode, setDarkMode] = useState(() => {
        if (typeof window !== "undefined") return document.documentElement.classList.contains("dark");
        return false;
    });
    const { flash } = usePage().props as any;
    const { setFlash } = useToast();

    useEffect(() => {
        document.documentElement.classList.toggle("dark", darkMode);
        localStorage.setItem("darkMode", darkMode ? "dark" : "light");
    }, [darkMode]);

    useEffect(() => {
        if (flash && Object.keys(flash).length > 0) {
            setFlash(flash);
        }
    }, [flash]);

    const toggleSidebar = () => setCollapsed((prev) => !prev);

    return (
        <div className={darkMode ? "dark" : ""}>
            <div className="min-h-screen bg-gray-50 dark:bg-[#0a0e17]">
                <AdminSidebar collapsed={collapsed} toggleActive={toggleSidebar} />
                <div className={`transition-all duration-300 ${collapsed ? "ml-[72px]" : "ml-[260px]"}`}>
                    <AdminHeader toggleActive={toggleSidebar} darkMode={darkMode} setDarkMode={setDarkMode} />
                    <main className="p-4 md:p-6 min-h-[calc(100vh-64px)]">{children}</main>
                    <AdminFooter />
                </div>
            </div>
        </div>
    );
};

const AdminLayout: React.FC<AdminLayoutProps> = ({ children }) => (
    <ToastProvider>
        <AdminLayoutContent>{children}</AdminLayoutContent>
    </ToastProvider>
);

export default AdminLayout;
