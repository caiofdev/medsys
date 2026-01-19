import { Link } from '@inertiajs/react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationProps {
    links: PaginationLink[];
    currentPage: number;
    lastPage: number;
    total: number;
    perPage: number;
}

export default function Pagination({ links, currentPage, lastPage, total, perPage }: PaginationProps) {
    if (lastPage <= 1) return null;

    const startItem = (currentPage - 1) * perPage + 1;
    const endItem = Math.min(currentPage * perPage, total);

    return (
        <div className="flex items-center justify-center px-4 py-4 sm:px-6">
            <div className="flex flex-1 justify-between sm:hidden">
                {links[0]?.url && (
                    <Link
                        href={links[0].url}
                        className="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition"
                    >
                        Anterior
                    </Link>
                )}

                {links[links.length - 1]?.url && (
                    <Link
                        href={links[links.length - 1].url!}
                        className="ml-3 inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition"
                    >
                        Próximo
                    </Link>
                )}
            </div>

            <div className="hidden sm:flex sm:flex-col sm:items-center sm:gap-4">
                <nav className="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    {/* Previous */}
                    {links[0]?.url ? (
                        <Link
                            href={links[0].url}
                            className="inline-flex items-center rounded-l-md bg-white px-2 py-2 text-gray-600 ring-1 ring-inset ring-gray-300 hover:bg-gray-100 transition"
                        >
                            <span className="sr-only">Anterior</span>
                            <ChevronLeft className="h-5 w-5" />
                        </Link>
                    ) : (
                        <span className="inline-flex items-center rounded-l-md bg-white px-2 py-2 text-gray-300 ring-1 ring-inset ring-gray-200">
                            <ChevronLeft className="h-5 w-5" />
                        </span>
                    )}

                    {links.slice(1, -1).map((link, index) => {
                        if (!link.url && link.label === '...') {
                            return (
                                <span
                                    key={index}
                                    className="inline-flex items-center bg-white px-4 py-2 text-sm font-semibold text-gray-500 ring-1 ring-inset ring-gray-300"
                                >
                                    ...
                                </span>
                            );
                        }

                        return link.url ? (
                            <Link
                                key={index}
                                href={link.url}
                                aria-current={link.active ? 'page' : undefined}
                                className={`inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset transition
                                    ${
                                        link.active
                                            ? 'z-10 bg-gray-200 text-gray-900 ring-gray-400'
                                            : 'bg-white text-gray-600 ring-gray-300 hover:bg-gray-100'
                                    }
                                `}
                            >
                                {link.label}
                            </Link>
                        ) : (
                            <span
                                key={index}
                                className="inline-flex items-center bg-white px-4 py-2 text-sm font-semibold text-gray-400 ring-1 ring-inset ring-gray-200"
                            >
                                {link.label}
                            </span>
                        );
                    })}

                    {/* Next */}
                    {links[links.length - 1]?.url ? (
                        <Link
                            href={links[links.length - 1].url!}
                            className="inline-flex items-center rounded-r-md bg-white px-2 py-2 text-gray-600 ring-1 ring-inset ring-gray-300 hover:bg-gray-100 transition"
                        >
                            <span className="sr-only">Próximo</span>
                            <ChevronRight className="h-5 w-5" />
                        </Link>
                    ) : (
                        <span className="inline-flex items-center rounded-r-md bg-white px-2 py-2 text-gray-300 ring-1 ring-inset ring-gray-200">
                            <ChevronRight className="h-5 w-5" />
                        </span>
                    )}
                </nav>

                <p className="text-sm text-gray-600">
                    Mostrando <span className="font-medium text-gray-900">{startItem}</span> até{' '}
                    <span className="font-medium text-gray-900">{endItem}</span> de{' '}
                    <span className="font-medium text-gray-900">{total}</span> resultados
                </p>
            </div>
        </div>
    );
}
