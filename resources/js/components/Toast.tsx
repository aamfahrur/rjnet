import React, { useEffect, useState, useCallback, createContext, useContext, type ReactNode } from "react";

type ToastType = "success" | "error" | "warning" | "info";

interface Toast {
    id: string;
    type: ToastType;
    message: string;
    exiting?: boolean;
}

interface ToastContextType {
    addToast: (type: ToastType, message: string) => void;
    flash: Record<string, string> | null;
    setFlash: (flash: Record<string, string> | null) => void;
}

const ToastContext = createContext<ToastContextType | null>(null);

export const useToast = () => {
    const ctx = useContext(ToastContext);
    if (!ctx) throw new Error("useToast must be used within ToastProvider");
    return ctx;
};

const icons: Record<ToastType, string> = {
    success: "check_circle",
    error: "error",
    warning: "warning",
    info: "info",
};

const colors: Record<ToastType, string> = {
    success: "bg-green-50 border-green-200 text-green-800 dark:bg-green-900/40 dark:border-green-800 dark:text-green-300",
    error: "bg-red-50 border-red-200 text-red-800 dark:bg-red-900/40 dark:border-red-800 dark:text-red-300",
    warning: "bg-orange-50 border-orange-200 text-orange-800 dark:bg-orange-900/40 dark:border-orange-800 dark:text-orange-300",
    info: "bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/40 dark:border-blue-800 dark:text-blue-300",
};

const iconColors: Record<ToastType, string> = {
    success: "text-green-500",
    error: "text-red-500",
    warning: "text-orange-500",
    info: "text-blue-500",
};

export const ToastProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
    const [toasts, setToasts] = useState<Toast[]>([]);
    const [flash, setFlash] = useState<Record<string, string> | null>(null);

    const addToast = useCallback((type: ToastType, message: string) => {
        const id = Math.random().toString(36).slice(2);
        setToasts((prev) => [...prev, { id, type, message }]);
        setTimeout(() => removeToast(id), 4000);
    }, []);

    const removeToast = (id: string) => {
        setToasts((prev) => prev.map((t) => (t.id === id ? { ...t, exiting: true } : t)));
        setTimeout(() => setToasts((prev) => prev.filter((t) => t.id !== id)), 300);
    };

    // Watch for flash messages passed via setFlash
    useEffect(() => {
        if (flash?.success) addToast("success", flash.success);
        if (flash?.error) addToast("error", flash.error);
        if (flash?.warning) addToast("warning", flash.warning);
        if (flash?.info) addToast("info", flash.info);
    }, [flash]);

    return (
        <ToastContext.Provider value={{ addToast, flash, setFlash }}>
            {children}
            <div className="fixed top-5 right-5 z-[9999] flex flex-col gap-2 max-w-sm w-full pointer-events-none">
                {toasts.map((toast) => (
                    <div
                        key={toast.id}
                        className={`pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg border shadow-lg transition-all duration-300 ${
                            toast.exiting ? "opacity-0 translate-x-4 scale-95" : "opacity-100 translate-x-0 scale-100"
                        } ${colors[toast.type]}`}
                    >
                        <i className={`material-symbols-outlined !text-[20px] shrink-0 ${iconColors[toast.type]}`}>
                            {icons[toast.type]}
                        </i>
                        <p className="text-sm font-medium flex-1">{toast.message}</p>
                        <button onClick={() => removeToast(toast.id)} className="shrink-0 hover:opacity-70">
                            <i className="material-symbols-outlined !text-[16px]">close</i>
                        </button>
                    </div>
                ))}
            </div>
        </ToastContext.Provider>
    );
};

