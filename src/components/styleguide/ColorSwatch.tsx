interface SwatchProps {
  name: string;
  hex: string;
  description?: string;
  textOn?: "light" | "dark";
}

export function ColorSwatch({ name, hex, description, textOn = "light" }: SwatchProps) {
  return (
    <div className="group overflow-hidden rounded-lg border border-brand-line bg-white shadow-card transition hover:shadow-hover">
      <div
        className="relative h-28 w-full"
        style={{ backgroundColor: hex }}
      >
        <span
          className={`absolute bottom-2 right-3 font-mono text-xs font-semibold ${
            textOn === "dark" ? "text-white/90" : "text-brand-ink/70"
          }`}
        >
          {hex.toUpperCase()}
        </span>
      </div>
      <div className="p-4">
        <div className="text-sm font-bold text-brand-ink">{name}</div>
        {description && (
          <div className="mt-1 text-xs text-brand-muted">{description}</div>
        )}
      </div>
    </div>
  );
}
