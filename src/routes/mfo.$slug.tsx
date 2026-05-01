import { createFileRoute } from "@tanstack/react-router";
import { SiteHeader } from "@/components/site/SiteHeader";
import { SiteFooter } from "@/components/home/SiteFooter";
import { Breadcrumbs } from "@/components/mfo/Breadcrumbs";
import { MfoHeroCard } from "@/components/mfo/MfoHeroCard";
import { ConditionsSection } from "@/components/mfo/ConditionsSection";
import { FitSection } from "@/components/mfo/FitSection";
import { StepsSection } from "@/components/mfo/StepsSection";
import { ProsConsSection } from "@/components/mfo/ProsConsSection";
import { RatesTable } from "@/components/mfo/RatesTable";
import { ReviewsSection } from "@/components/mfo/ReviewsSection";
import { MfoFaqSection } from "@/components/mfo/MfoFaqSection";
import { SimilarMfos } from "@/components/mfo/SimilarMfos";
import { FinalCta } from "@/components/mfo/FinalCta";
import { SeoContent } from "@/components/mfo/SeoContent";

export const Route = createFileRoute("/mfo/$slug")({
  head: () => ({
    meta: [
      { title: "Займер — обзор, условия и отзывы 2026 | Zaymi Online" },
      {
        name: "description",
        content:
          "Займер: займы онлайн до 30 000 ₽ под 0% для новых клиентов. Одобрение 95%, решение за 5 минут, деньги на карту мгновенно. Условия, тарифы и реальные отзывы 2026.",
      },
      { property: "og:title", content: "Займер — обзор, условия и отзывы 2026" },
      {
        property: "og:description",
        content: "Первый займ под 0%, одобрение 95%, деньги за 5 минут на карту любого банка.",
      },
    ],
  }),
  component: MfoPage,
});

function MfoPage() {
  return (
    <div className="min-h-screen scroll-smooth bg-white">
      <SiteHeader />
      <Breadcrumbs
        items={[
          { label: "Главная", href: "/" },
          { label: "Каталог МФО", href: "#" },
          { label: "Займер" },
        ]}
      />
      <main>
        <MfoHeroCard />
        <ConditionsSection />
        <FitSection />
        <StepsSection />
        <ProsConsSection />
        <RatesTable />
        <ReviewsSection />
        <MfoFaqSection />
        <SimilarMfos />
        <FinalCta />
        <SeoContent />
      </main>
      <SiteFooter />
    </div>
  );
}
