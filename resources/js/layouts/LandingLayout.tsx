import React, { useState, useEffect, type ReactNode } from "react";
import Navbar from "../Components/Landing/Navbar";
import Footer from "../Components/Landing/Footer";
import FloatingWhatsApp from "../Components/Landing/FloatingWhatsApp";
import type { NavLink } from "../../Types/landing";

interface LandingLayoutProps {
    children: ReactNode;
    navLinks: NavLink[];
}

const LandingLayout: React.FC<LandingLayoutProps> = ({ children, navLinks }) => {
    const [darkMode, setDarkMode] = useState(false);

    useEffect(() => {
        if (typeof window !== "undefined") {
            const stored = localStorage.getItem("darkMode");
            if (stored === "dark") {
                setDarkMode(true);
                document.documentElement.classList.add("dark");
            }
        }
    }, []);

    useEffect(() => {
        document.documentElement.classList.toggle("dark", darkMode);
        localStorage.setItem("darkMode", darkMode ? "dark" : "light");
    }, [darkMode]);

    return (
        <div className={darkMode ? "dark" : ""}>
            <div className="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 min-h-screen">
                <Navbar navLinks={navLinks} darkMode={darkMode} setDarkMode={setDarkMode} />
                <main>{children}</main>
                <Footer />
                <FloatingWhatsApp />
            </div>
        </div>
    );
};

export default LandingLayout;
