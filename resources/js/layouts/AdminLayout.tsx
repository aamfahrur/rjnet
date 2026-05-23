import React, { useState, useEffect, type ReactNode } from "react";
import { Link, usePage } from "@inertiajs/react";

interface AdminLayoutProps { children: ReactNode; }

// Inline Header
const AdminHeader: React.FC<{ toggleActive: () => void; darkMode: boolean; setDarkMode: (v: boolean) => void }> = ({ toggleActive, darkMode, setDarkMode }) => {
    const { auth } = usePage().props as any;
    return (
        <div id="header" className="header-area bg-white dark:bg-[#0c1427] py-[13px] px-[20px] md:px-[25px] fixed top-0 z-[6] rounded-b-md transition-all shadow-sm" style={{ left: 0, right: 0 }}>
            <div className="flex items-center justify-between">
                <button type="button" className="hide-sidebar-toggle transition-all hover:text-primary-500" onClick={toggleActive}>
                    <i className="material-symbols-outlined !text-[20px]">menu</i>
                </button>
                <div className="flex items-center gap-3">
                    <button onClick={() => setDarkMode(!darkMode)} className="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-[#15203c] transition-colors">
                        <i className="material-symbols-outlined !text-[20px] text-gray-500 dark:text-gray-400">{darkMode ? "light_mode" : "dark_mode"}</i>
                    </button>
                    <div className="flex items-center gap-2">
                        <div className="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center text-white text-sm font-medium">
                            {auth?.user?.name?.charAt(0) || "A"}
                        </div>
                        <span className="text-sm font-medium text-black dark:text-white hidden md:block">{auth?.user?.name || "Admin"}</span>
                    </div>
                </div>
            </div>
        </div>
    );
};

// Inline Footer
const AdminFooter: React.FC = () => (
    <div className="bg-white dark:bg-[#0c1427] border-t border-gray-100 dark:border-[#172036] py-4 px-[25px] text-center">
        <p className="text-sm text-gray-500">&copy; {new Date().getFullYear()} <span className="font-medium text-black dark:text-white">RJNet</span>. All rights reserved.</p>
    </div>
);

const AdminLayout: React.FC<AdminLayoutProps> = ({ children }) => {
    const [toggleActive, setToggleActive] = useState(false);
    const [darkMode, setDarkMode] = useState(() => {
        if (typeof window !== "undefined") return document.documentElement.classList.contains("dark");
        return false;
    });

    useEffect(() => {
        document.documentElement.classList.toggle("dark", darkMode);
        localStorage.setItem("darkMode", darkMode ? "dark" : "light");
    }, [darkMode]);

    const handleToggle = () => setToggleActive(!toggleActive);

    return (
        <div className={darkMode ? "dark" : ""}>
            <div className="min-h-screen bg-gray-50 dark:bg-[#0a0e17]">
                <div className={`fixed top-0 ltr:left-0 rtl:right-0 h-screen z-50 transition-all duration-300 ${toggleActive ? "ltr:-left-[285px] rtl:-right-[285px]" : "left-0"}`}>
                    <AdminSidebar toggleActive={handleToggle} />
                </div>
                <div className={`transition-all duration-300 ${toggleActive ? "ltr:ml-[0px] rtl:mr-[0px]" : "ltr:ml-[285px] rtl:mr-[285px]"}`}>
                    <AdminHeader toggleActive={handleToggle} darkMode={darkMode} setDarkMode={setDarkMode} />
                    <div className="pt-[80px] px-[20px] md:px-[25px] pb-[25px]">
                        <main className="min-h-[calc(100vh-160px)]">{children}</main>
                    </div>
                    <AdminFooter />
                </div>
            </div>
        </div>
    );
};

// Inline Sidebar
const AdminSidebar: React.FC<{ toggleActive: () => void }> = ({ toggleActive }) => {
    const { url } = usePage();
    const [openMenus, setOpenMenus] = useState<Record<string, boolean>>({
        dashboard: true, customers: false, routers: false,
        billing: false, tickets: false, network: false, settings: false,
    });

    const toggleMenu = (key: string) => {
        setOpenMenus((prev) => ({ ...prev, [key]: !prev[key] }));
    };

    const isActive = (path: string) => url.startsWith(path);

    const menuItems = [
        {
            key: "dashboard", icon: "dashboard", label: "Dashboard",
            children: [
                { path: "/admin/dashboard", label: "Overview", icon: "monitoring" },
                { path: "/admin/monitoring", label: "NOC Monitoring", icon: "sensors" },
            ]
        },
        {
            key: "customers", icon: "people", label: "Pelanggan",
            children: [
                { path: "/admin/customers", label: "Semua Pelanggan", icon: "group" },
                { path: "/admin/customers/create", label: "Tambah Pelanggan", icon: "person_add" },
            ]
        },
        {
            key: "routers", icon: "router", label: "Router",
            children: [
                { path: "/admin/routers", label: "Semua Router", icon: "dns" },
                { path: "/admin/routers/create", label: "Tambah Router", icon: "add_circle" },
            ]
        },
        {
            key: "billing", icon: "receipt_long", label: "Billing",
            children: [
                { path: "/admin/invoices", label: "Invoice", icon: "description" },
                { path: "/admin/payments", label: "Pembayaran", icon: "payments" },
            ]
        },
        {
            key: "tickets", icon: "support_agent", label: "Tiket",
            children: [
                { path: "/admin/tickets", label: "Semua Tiket", icon: "confirmation_number" },
            ]
        },
        {
            key: "network", icon: "share", label: "Jaringan",
            children: [
                { path: "/admin/topology", label: "Topologi", icon: "account_tree" },
            ]
        },
        {
            key: "settings", icon: "settings", label: "Pengaturan",
            children: [
                { path: "/admin/users", label: "Pengguna", icon: "manage_accounts" },
                { path: "/admin/settings", label: "Konfigurasi", icon: "tune" },
            ]
        },
    ];

    return (
        <div className="sidebar-area bg-white dark:bg-[#0c1427] fixed z-[7] top-0 h-screen transition-all rounded-r-md w-[285px]">
            <div className="logo bg-white dark:bg-[#0c1427] border-b border-gray-100 dark:border-[#172036] px-[25px] pt-[19px] pb-[15px] absolute z-[2] right-0 top-0 left-0">
                <Link href="/admin/dashboard" className="transition-none relative flex items-center outline-none">
                    <img src="/images/logo-icon.svg" alt="logo" width={26} height={26} />
                    <span className="font-bold text-black dark:text-white relative ltr:ml-[8px] top-px text-xl">RJNet</span>
                </Link>
                <button type="button" className="burger-menu inline-block absolute z-[3] top-[24px] ltr:right-[25px] transition-all hover:text-primary-500 lg:hidden" onClick={toggleActive}>
                    <i className="material-symbols-outlined">close</i>
                </button>
            </div>
            <div className="pt-[89px] px-[22px] pb-[20px] h-screen overflow-y-scroll sidebar-custom-scrollbar">
                <div className="accordion">
                    {menuItems.map((menu) => (
                        <div key={menu.key} className="accordion-item rounded-md text-black dark:text-white mb-[5px] whitespace-nowrap">
                            <button
                                className={`accordion-button toggle flex items-center transition-all py-[9px] ltr:pl-[14px] ltr:pr-[30px] rtl:pr-[14px] rtl:pl-[30px] rounded-md font-medium w-full relative hover:bg-gray-50 dark:hover:bg-[#15203c] text-left ${openMenus[menu.key] ? "open" : ""}`}
                                onClick={() => toggleMenu(menu.key)}
                            >
                                <i className="material-symbols-outlined transition-all text-gray-500 dark:text-gray-400 ltr:mr-[7px] !text-[22px] leading-none relative -top-px">{menu.icon}</i>
                                <span className="title leading-none">{menu.label}</span>
                                <i className={`material-symbols-outlined ltr:ml-auto rtl:mr-auto transition-all !text-[18px] ${openMenus[menu.key] ? "rotate-180" : ""}`}>expand_more</i>
                            </button>
                            <div className={`accordion-collapse ${openMenus[menu.key] ? "block" : "hidden"} pt-[4px]`}>
                                <ul className="sidebar-submenu ltr:pl-[20px] rtl:pr-[20px]">
                                    {menu.children.map((child) => (
                                        <li key={child.path} className="relative">
                                            <Link
                                                href={child.path}
                                                className={`flex items-center transition-all rounded-md py-[9px] ltr:pl-[20px] ltr:pr-[30px] font-medium hover:bg-gray-50 dark:hover:bg-[#15203c] before:absolute before:content-[''] before:top-1/2 before:-translate-y-1/2 before:bg-gray-300 dark:before:bg-gray-600 before:rounded-full ltr:before:left-0 before:w-[8px] before:h-[8px] ${isActive(child.path) ? "text-primary-500 bg-primary-50 dark:bg-[#15203c]" : "text-gray-500 dark:text-gray-400"
                                                    }`}
                                            >
                                                <i className="material-symbols-outlined ltr:mr-[7px] !text-[18px]">{child.icon}</i>
                                                <span className="leading-none text-sm">{child.label}</span>
                                            </Link>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default AdminLayout;
