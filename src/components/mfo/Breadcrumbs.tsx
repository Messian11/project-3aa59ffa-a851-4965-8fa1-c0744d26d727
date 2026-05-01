import { ChevronRight } from "lucide-react";

interface Crumb {
  label: string;
  href?: string;
}

export function Breadcrumbs({ items }: { items: Crumb[] }) {
  return (
    <nav aria-label="Хлебные крошки" className="px-6 pt-6">
      <ol className="mx-auto flex max-w-7xl flex-wrap items-center gap-1.5 text-sm">
        {items.map((c, i) => (
          <li key={c.label} className="flex items-center gap-1.5">
            {c.href ? (
              <a
                href={c.href}
                className="font-semibold text-brand-muted transition-colors hover:text-brand-blue"
              >
                {c.label}
              </a>
            ) : (
              <span className="font-bold text-brand-ink">{c.label}</span>
            )}
            {i < items.length - 1 && (
              <ChevronRight className="h-3.5 w-3.5 text-brand-muted" strokeWidth={2.5} />
            )}
          </li>
        ))}
      </ol>
    </nav>
  );
}
