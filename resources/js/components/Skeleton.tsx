import React from "react";

interface SkeletonProps {
    className?: string;
    count?: number;
}

export const CardSkeleton: React.FC = () => (
    <div className="bg-white dark:bg-[#0c1427] rounded-lg border border-gray-100 dark:border-[#172036] p-5 animate-pulse">
        <div className="flex items-center justify-between">
            <div className="space-y-2 flex-1">
                <div className="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/3" />
                <div className="h-6 bg-gray-200 dark:bg-gray-700 rounded w-1/2" />
            </div>
            <div className="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700" />
        </div>
    </div>
);

export const TableSkeleton: React.FC<{ rows?: number; cols?: number }> = ({ rows = 5, cols = 5 }) => (
    <div className="animate-pulse">
        {Array.from({ length: rows }).map((_, i) => (
            <div key={i} className="flex gap-4 px-4 py-3 border-b border-gray-100 dark:border-[#172036]">
                {Array.from({ length: cols }).map((_, j) => (
                    <div key={j} className="h-4 bg-gray-200 dark:bg-gray-700 rounded flex-1" />
                ))}
            </div>
        ))}
    </div>
);

export const TextSkeleton: React.FC<SkeletonProps> = ({ className = "h-4 w-full" }) => (
    <div className={`animate-pulse bg-gray-200 dark:bg-gray-700 rounded ${className}`} />
);
