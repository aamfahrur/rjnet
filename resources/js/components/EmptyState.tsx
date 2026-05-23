import React from "react";

interface EmptyStateProps {
    icon?: string;
    title: string;
    description?: string;
    action?: { label: string; href: string };
}

const EmptyState: React.FC<EmptyStateProps> = ({ icon = "inbox", title, description, action }) => (
    <div className="flex flex-col items-center justify-center py-16 px-4 text-center">
        <div className="w-16 h-16 rounded-full bg-gray-100 dark:bg-[#15203c] flex items-center justify-center mb-4">
            <i className="material-symbols-outlined !text-[32px] text-gray-400 dark:text-gray-500">{icon}</i>
        </div>
        <h3 className="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">{title}</h3>
        {description && <p className="text-sm text-gray-500 dark:text-gray-400 max-w-sm mb-4">{description}</p>}
        {action && (
            <a href={action.href} className="inline-flex items-center gap-2 px-4 py-2 bg-primary-500 text-white rounded-lg text-sm font-medium hover:bg-primary-600 transition-colors">
                <i className="material-symbols-outlined !text-[18px]">add</i>
                {action.label}
            </a>
        )}
    </div>
);

export default EmptyState;
