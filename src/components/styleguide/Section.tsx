import { ReactNode } from "react";

interface SectionProps {
  id: string;
  eyebrow: string;
  title: string;
  description?: string;
  children: ReactNode;
}

export function Section({ id, eyebrow, title, description, children }: SectionProps) {
  return (
    <section id={id} className="scroll-mt-24 py-12 md:py-16">
      <div className="mb-8 md:mb-10">
        <span className="inline-flex items-center rounded-pill bg-brand-soft px-3 py-1 text-xs font-semibold uppercase tracking-wider text-brand-blue">
          {eyebrow}
        </span>
        <h2 className="mt-3 text-3xl md:text-4xl font-extrabold text-brand-ink">{title}</h2>
        {description && (
          <p className="mt-2 max-w-2xl text-base text-brand-muted">{description}</p>
        )}
      </div>
      {children}
    </section>
  );
}
