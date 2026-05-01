import { createFileRoute } from "@tanstack/react-router";
import { SiteHeader } from "@/components/site/SiteHeader";
import { HeroSection } from "@/components/home/HeroSection";
import { SocialProofStrip } from "@/components/home/SocialProofStrip";
import { MfoCatalogSection } from "@/components/home/MfoCatalogSection";
import { SituationsSection } from "@/components/home/SituationsSection";
import { AmountQuickLinks } from "@/components/home/AmountQuickLinks";
import { ComparisonSection } from "@/components/home/ComparisonSection";
import { HowItWorksSection } from "@/components/home/HowItWorksSection";
import { BlogSection } from "@/components/home/BlogSection";
import { SeoHubSection } from "@/components/home/SeoHubSection";
import { FaqSection } from "@/components/home/FaqSection";
import { NewsletterCta } from "@/components/home/NewsletterCta";
import { SiteFooter } from "@/components/home/SiteFooter";

export const Route = createFileRoute("/")({
  head: () => ({
    meta: [
      { title: "Займ онлайн без отказа за 5 минут — ТОП-50 МФО 2026 | Zaymi Online" },
      {
        name: "description",
        content:
          "Сравнение 50+ МФО России. Займ онлайн без отказа на карту с любой кредитной историей. Первый займ под 0%, одобрение за 5 минут.",
      },
      { property: "og:title", content: "Займ онлайн без отказа за 5 минут — Zaymi Online" },
      {
        property: "og:description",
        content: "ТОП-50 МФО 2026: сравнили условия, одобрение 97%, деньги за 5-15 минут.",
      },
    ],
  }),
  component: HomePage,
});

function HomePage() {
  return (
    <div className="min-h-screen scroll-smooth bg-white">
      <SiteHeader />
      <main>
        <HeroSection />
        <SocialProofStrip />
        <MfoCatalogSection />
        <SituationsSection />
        <AmountQuickLinks />
        <ComparisonSection />
        <HowItWorksSection />
        <BlogSection />
        <SeoHubSection />
        <FaqSection />
        <NewsletterCta />
      </main>
      <SiteFooter />
    </div>
  );
}
