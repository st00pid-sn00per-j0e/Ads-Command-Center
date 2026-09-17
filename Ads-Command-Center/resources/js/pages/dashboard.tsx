import { Head } from '@inertiajs/react';
import { dashboard } from '@/routes';

type DashboardProps = {
    scope: string;
    summary: {
        accounts: number;
        spendMicros: number;
        impressions: number;
        clicks: number;
        conversions: number;
        pendingApprovals: number;
    };
};

export default function Dashboard({ scope, summary }: DashboardProps) {
    const cards = [
        ['Accounts', summary.accounts.toLocaleString()],
        [
            'Spend',
            `$${(summary.spendMicros / 1_000_000).toLocaleString(undefined, { maximumFractionDigits: 2 })}`,
        ],
        [
            'CTR',
            summary.impressions
                ? `${((summary.clicks / summary.impressions) * 100).toFixed(2)}%`
                : '—',
        ],
        ['Conversions', summary.conversions.toLocaleString()],
        ['Pending approvals', summary.pendingApprovals.toLocaleString()],
    ];

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div>
                    <p className="text-muted-foreground text-sm">{scope}</p>
                    <h1 className="text-2xl font-semibold">
                        Google Ads command center
                    </h1>
                </div>
                <div className="grid gap-4 md:grid-cols-3 xl:grid-cols-5">
                    {cards.map(([label, value]) => (
                        <section key={label} className="rounded-xl border p-4">
                            <p className="text-muted-foreground text-sm">
                                {label}
                            </p>
                            <p className="mt-2 text-2xl font-semibold">
                                {value}
                            </p>
                        </section>
                    ))}
                </div>
                <section className="text-muted-foreground rounded-xl border p-6 text-sm">
                    Metrics are read from the synchronized local data store.
                    Google Ads mutations require an approved request and are
                    processed asynchronously.
                </section>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
