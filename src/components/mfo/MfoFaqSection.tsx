import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "@/components/ui/accordion";

const items = [
  {
    q: "Можно ли получить займ в Займере с плохой КИ?",
    a: "Да, Займер работает с заёмщиками, у которых есть открытые просрочки и закрытые счета в других МФО. Процент одобрения для клиентов с плохой кредитной историей — около 80%. Главное условие: возраст 18-70 лет и действующая банковская карта.",
  },
  {
    q: "Какая максимальная сумма займа в Займере?",
    a: "Максимум для нового клиента — 15 000 ₽. После первого успешного погашения лимит постепенно увеличивается до 30 000 ₽. Для повышения лимита нужно несколько раз вернуть займы вовремя.",
  },
  {
    q: "Сколько раз можно брать займ под 0% в Займере?",
    a: "Акция «0%» доступна только для первого займа в Займере. Повторные займы выдаются по стандартной ставке от 0,99% в день. Если у вас уже был займ в Займере — даже несколько лет назад — нулевая ставка не применяется.",
  },
  {
    q: "На какие карты Займер выдаёт займы?",
    a: "Займер переводит деньги на карты любых российских банков: Сбербанк, Тинькофф, Альфа-Банк, ВТБ, Райффайзенбанк и другие. Также возможен перевод на QIWI кошелёк, банковский счёт или получение наличными в системе Контакт.",
  },
  {
    q: "Что делать если Займер отказал?",
    a: "Не отчаивайтесь — попробуйте подать заявку в другие МФО из нашего каталога. У разных компаний разные критерии скоринга. Также через 30 дней можно повторно подать заявку в Займер: данные могли обновиться. В разделе «Без отказа» собраны МФО с самым высоким процентом одобрения.",
  },
  {
    q: "Можно ли продлить займ в Займере?",
    a: "Да, продление доступно в личном кабинете до даты погашения. Минимальный срок продления — 7 дней, максимальный — 30 дней. За продление взимается плата согласно тарифам. Лучше планировать возврат заранее, чтобы избежать дополнительных расходов.",
  },
];

export function MfoFaqSection() {
  return (
    <section className="px-6 py-16">
      <div className="mx-auto max-w-3xl">
        <div className="text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-brand-ink md:text-4xl">
            Частые вопросы о Займере
          </h2>
          <p className="mt-3 text-base text-brand-muted md:text-lg">
            Ответы на вопросы, которые чаще всего задают наши пользователи
          </p>
        </div>

        <Accordion type="single" collapsible className="mt-10 space-y-3">
          {items.map((it, i) => (
            <AccordionItem
              key={i}
              value={`q-${i}`}
              className="rounded-2xl border border-brand-line bg-white px-6 shadow-card transition-shadow data-[state=open]:shadow-hover"
            >
              <AccordionTrigger className="py-5 text-left text-base font-extrabold text-brand-ink hover:no-underline md:text-lg [&>svg]:h-5 [&>svg]:w-5 [&>svg]:text-brand-blue">
                {it.q}
              </AccordionTrigger>
              <AccordionContent className="pb-5 pt-1 text-sm font-medium leading-relaxed text-brand-muted md:text-base">
                {it.a}
              </AccordionContent>
            </AccordionItem>
          ))}
        </Accordion>
      </div>
    </section>
  );
}
