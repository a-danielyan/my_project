<?php

namespace App\Http\Services;

use App\Exceptions\CustomErrorException;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardService extends BaseService
{
    public const INTERVAL_MONTH_TO_DATE = 'Month';
    public const INTERVAL_QUARTER_TO_DATE = 'Quarter';
    public const INTERVAL_YEAR_TO_DATE = 'Year';
    public const MARKETING_CACHE_KEY = 'dashboard_marketing_stats';
    public const SALES_CACHE_KEY = 'dashboard_sales_stats';
    public const ACCOUNT_CACHE_KEY = 'dashboard_account_stats';

    public const DASHBOARD_MARKETING_SECTION = 'Marketing';
    public const DASHBOARD_SALES_AND_REVENUE_SECTION = 'SalesRevenue';
    public const DASHBOARD_ACCOUNT_SECTION = 'AccountReceivables';
    public const DASHBOARD_PRODUCTION_SECTION = 'Production';
    public const DASHBOARD_SHIPPING_SECTION = 'Shipping';
    public const DASHBOARD_ALLOWED_SECTIONS = [
        self::DASHBOARD_MARKETING_SECTION,
        self::DASHBOARD_SALES_AND_REVENUE_SECTION,
        self::DASHBOARD_ACCOUNT_SECTION,
        self::DASHBOARD_PRODUCTION_SECTION,
        self::DASHBOARD_SHIPPING_SECTION,
    ];

    public function __construct()
    {
    }

    public function resource(): string
    {
        return '';
    }

    public function getStats(User $user): array
    {
        $dashboardBlocks = $user->dashboard_blocks;
        $stats = [];
        if (!empty($dashboardBlocks[DashboardService::DASHBOARD_MARKETING_SECTION])) {
            $stats['marketing'] = $this->getMarketingStats();
        }
        if (!empty($dashboardBlocks[DashboardService::DASHBOARD_SALES_AND_REVENUE_SECTION])) {
            $stats['sales'] = $this->getSalesStats();
        }
        if (!empty($dashboardBlocks[DashboardService::DASHBOARD_ACCOUNT_SECTION])) {
            $stats['account'] = $this->getAccountStats();
        }
        if (!empty($dashboardBlocks[DashboardService::DASHBOARD_SHIPPING_SECTION])) {
            $stats['shipping'] = [];
        }
        if (!empty($dashboardBlocks[DashboardService::DASHBOARD_PRODUCTION_SECTION])) {
            $stats['production'] = [];
        }

        return $stats;
    }

    private function getMarketingStats()
    {
        if (Cache::has(self::MARKETING_CACHE_KEY)) {
            return Cache::get(self::MARKETING_CACHE_KEY);
        }
        $this->calculateMarketingStats();

        return Cache::get(self::MARKETING_CACHE_KEY);
    }

    private function getSalesStats()
    {
        if (Cache::has(self::SALES_CACHE_KEY)) {
            return Cache::get(self::SALES_CACHE_KEY);
        }
        $this->calculateSalesStats();

        return Cache::get(self::SALES_CACHE_KEY);
    }

    private function getAccountStats()
    {
        if (Cache::has(self::ACCOUNT_CACHE_KEY)) {
            return Cache::get(self::ACCOUNT_CACHE_KEY);
        }
        $this->calculateSalesStats();

        return Cache::get(self::ACCOUNT_CACHE_KEY);
    }

    public function calculateMarketingStats(): void
    {
        Cache::put(self::MARKETING_CACHE_KEY, [
            'leadData' => $this->calculateLeadData(),
            'newCustomers' => $this->calculateNewCustomersData(),
            'calculatedAt' => now(),
        ]);
    }


    private function calculateLeadData(): array
    {
        return [
            'lastYear' => [
                self::INTERVAL_MONTH_TO_DATE => [
                    [
                        "id" => "allLeads",
                        "value" => $this->getAllLeads(self::INTERVAL_MONTH_TO_DATE, 'last'),
                        "label" => "All leads",
                    ],
                    [
                        "id" => "MQL",
                        "value" => $this->getMQLLeads(self::INTERVAL_MONTH_TO_DATE, 'last'),
                        "label" => "Mql",
                    ],
                    [
                        "id" => "SQL",
                        "value" => $this->getSQL(self::INTERVAL_MONTH_TO_DATE, 'last'),
                        "label" => "Sql",
                    ],
                    [
                        "id" => "NewOrder",
                        "value" => $this->getNewOrder(self::INTERVAL_MONTH_TO_DATE, 'last'),
                        "label" => "New order",
                    ],
                ],
                self::INTERVAL_QUARTER_TO_DATE => [
                    [
                        "id" => "allLeads",
                        "value" => $this->getAllLeads(self::INTERVAL_QUARTER_TO_DATE, 'last'),
                        "label" => "All leads",
                    ],
                    [
                        "id" => "MQL",
                        "value" => $this->getMQLLeads(self::INTERVAL_QUARTER_TO_DATE, 'last'),
                        "label" => "Mql",
                    ],
                    [
                        "id" => "SQL",
                        "value" => $this->getSQL(self::INTERVAL_QUARTER_TO_DATE, 'last'),
                        "label" => "Sql",
                    ],
                    [
                        "id" => "NewOrder",
                        "value" => $this->getNewOrder(self::INTERVAL_QUARTER_TO_DATE, 'last'),
                        "label" => "New order",
                    ],
                ],
                self::INTERVAL_YEAR_TO_DATE => [
                    [
                        "id" => "allLeads",
                        "value" => $this->getAllLeads(self::INTERVAL_YEAR_TO_DATE, 'last'),
                        "label" => "All leads",
                    ],
                    [
                        "id" => "MQL",
                        "value" => $this->getMQLLeads(self::INTERVAL_YEAR_TO_DATE, 'last'),
                        "label" => "Mql",
                    ],
                    [
                        "id" => "SQL",
                        "value" => $this->getSQL(self::INTERVAL_YEAR_TO_DATE, 'last'),
                        "label" => "Sql",
                    ],
                    [
                        "id" => "NewOrder",
                        "value" => $this->getNewOrder(self::INTERVAL_YEAR_TO_DATE, 'last'),
                        "label" => "New order",
                    ],
                ],
            ],
            'currentYear' => [
                self::INTERVAL_MONTH_TO_DATE => [
                    [
                        "id" => "allLeads",
                        "value" => $this->getAllLeads(self::INTERVAL_MONTH_TO_DATE),
                        "label" => "All leads",
                    ],
                    [
                        "id" => "MQL",
                        "value" => $this->getMQLLeads(self::INTERVAL_MONTH_TO_DATE),
                        "label" => "Mql",
                    ],
                    [
                        "id" => "SQL",
                        "value" => $this->getSQL(self::INTERVAL_MONTH_TO_DATE),
                        "label" => "Sql",
                    ],
                    [
                        "id" => "NewOrder",
                        "value" => $this->getNewOrder(self::INTERVAL_MONTH_TO_DATE),
                        "label" => "New order",
                    ],
                ],
                self::INTERVAL_QUARTER_TO_DATE => [
                    [
                        "id" => "allLeads",
                        "value" => $this->getAllLeads(self::INTERVAL_QUARTER_TO_DATE),
                        "label" => "All leads",
                    ],
                    [
                        "id" => "MQL",
                        "value" => $this->getMQLLeads(self::INTERVAL_QUARTER_TO_DATE),
                        "label" => "Mql",
                    ],
                    [
                        "id" => "SQL",
                        "value" => $this->getSQL(self::INTERVAL_QUARTER_TO_DATE),
                        "label" => "Sql",
                    ],
                    [
                        "id" => "NewOrder",
                        "value" => $this->getNewOrder(self::INTERVAL_QUARTER_TO_DATE),
                        "label" => "New order",
                    ],
                ],
                self::INTERVAL_YEAR_TO_DATE => [
                    [
                        "id" => "allLeads",
                        "value" => $this->getAllLeads(self::INTERVAL_YEAR_TO_DATE),
                        "label" => "All leads",
                    ],
                    [
                        "id" => "MQL",
                        "value" => $this->getMQLLeads(self::INTERVAL_YEAR_TO_DATE),
                        "label" => "Mql",
                    ],
                    [
                        "id" => "SQL",
                        "value" => $this->getSQL(self::INTERVAL_YEAR_TO_DATE),
                        "label" => "Sql",
                    ],
                    [
                        "id" => "NewOrder",
                        "value" => $this->getNewOrder(self::INTERVAL_YEAR_TO_DATE),
                        "label" => "New order",
                    ],
                ],
            ],
        ];
    }

    private function calculateNewCustomersData(): array
    {
        return [
            'lastYear' => [
                'count' => [
                    'byMonth' => $this->getNewCustomerCountMonthly('last'),
                    'byQuarter' => $this->getNewCustomerCountQuarterly('last'),
                ],
                'amount' => [
                    'byMonth' => $this->getNewCustomerRevenueMonthly('last'),
                    'byQuarter' => $this->getNewCustomerRevenueQuarterly('last'),
                ],
            ],
            'currentYear' => [
                'count' => [
                    'byMonth' => $this->getNewCustomerCountMonthly(),
                    'byQuarter' => $this->getNewCustomerCountQuarterly(),
                ],
                'amount' => [
                    'byMonth' => $this->getNewCustomerRevenueMonthly(),
                    'byQuarter' => $this->getNewCustomerRevenueQuarterly(),
                ],
            ],
        ];
    }

    private function getNewCustomerCountMonthly(string $year = 'current'): Collection|array
    {
        if ($year === 'current') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
        } else {
            $startDate = now()->subYear()->startOfYear();
            $endDate = now()->subYear()->endOfYear();
        }

        return Account::query()->selectRaw('COUNT(*) AS Total, MONTH(created_at) As Month')
            ->groupByRaw('MONTH(created_at)')
            ->where('account.created_at', '>', $startDate)
            ->where('account.created_at', '<=', $endDate)->get();
    }

    private function getNewCustomerCountQuarterly(string $year = 'current'): Collection|array
    {
        if ($year === 'current') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
        } else {
            $startDate = now()->subYear()->startOfYear();
            $endDate = now()->subYear()->endOfYear();
        }

        return Account::query()->selectRaw('COUNT(*) AS Total, QUARTER(created_at) As Month')
            ->where('account.created_at', '>', $startDate)
            ->where('account.created_at', '<=', $endDate)
            ->groupByRaw('QUARTER(created_at)')->get();
    }

    private function getNewCustomerRevenueMonthly(string $year = 'current'): Collection|array
    {
        if ($year === 'current') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
        } else {
            $startDate = now()->subYear()->startOfYear();
            $endDate = now()->subYear()->endOfYear();
        }

        return Account::query()
            ->selectRaw('SUM(invoice.grand_total) AS TotalRevenue, MONTH(account.created_at) As Month')
            ->leftJoin('invoice', 'invoice.account_id', 'account.id')
            ->where('account.created_at', '>', $startDate)
            ->where('account.created_at', '<=', $endDate)
            ->groupByRaw('MONTH(account.created_at)')->get();
    }

    private function getNewCustomerRevenueQuarterly(string $year = 'current'): Collection|array
    {
        if ($year === 'current') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
        } else {
            $startDate = now()->subYear()->startOfYear();
            $endDate = now()->subYear()->endOfYear();
        }

        return Account::query()
            ->selectRaw('SUM(invoice.grand_total) AS TotalRevenue, QUARTER(account.created_at) As Month')
            ->leftJoin('invoice', 'invoice.account_id', 'account.id')
            ->where('account.created_at', '>', $startDate)
            ->where('account.created_at', '<=', $endDate)
            ->groupByRaw('QUARTER(account.created_at)')->get();
    }

    private function getAllLeads(string $interval, string $year = 'current')
    {
        return Cache::remember('Dashboard#allLeads#' . $interval . $year, 3, function () use ($interval, $year) {
            list($startDate, $endDate) = $this->getDateRange($interval, $year);

            return Lead::query()->where('created_at', '>', $startDate)
                ->where('created_at', '<', $endDate)->count();
        });
    }

    private function getMQLLeads(string $interval, string $year = 'current')
    {
        return Cache::remember('Dashboard#MQLLeads#' . $interval . $year, 3600, function () use ($interval, $year) {
            list($startDate, $endDate) = $this->getDateRange($interval, $year);

            return Lead::query()->whereHas('customFields', function ($query) {
                $msqlLeadStatuses = LeadStatus::query()->where('name', 'MQL')->first();
                $query->where('integer_value', $msqlLeadStatuses->getKey());
            })->where('created_at', '>', $startDate)->where('created_at', '<', $endDate)->count();
        });
    }

    private function getSQL(string $interval, string $year = 'current'): ?int
    {
        return Cache::remember('Dashboard#SQLLeads#' . $interval . $year, 3600, function () use ($interval, $year) {
            list($startDate, $endDate) = $this->getDateRange($interval, $year);

            return Opportunity::query()->where('project_type', Opportunity::NEW_BUSINESS)
                ->whereHas('account', function ($query) use ($startDate, $endDate) {
                    $query->whereHas('lead', function ($query) use ($startDate, $endDate) {
                        $query->where('created_at', '>', $startDate)->where('created_at', '<=', $endDate);
                    });
                })
                ->count();
        });
    }

    private function getNewOrder(string $interval, string $year = 'current'): ?int
    {
        return Cache::remember('Dashboard#NewOrders#' . $interval . $year, 3600, function () use ($interval, $year) {
            list($startDate, $endDate) = $this->getDateRange($interval, $year);

            return Invoice::query()->whereHas('opportunity', function ($query) {
                $query->where('project_type', Opportunity::NEW_BUSINESS);
            })->whereHas('account', function ($query) use ($startDate, $endDate) {
                $query->whereHas('lead', function ($query) use ($startDate, $endDate) {
                    $query->where('created_at', '>', $startDate)->where('created_at', '<', $endDate);
                });
            })->count();
        });
    }


    public function calculateSalesStats(): void
    {
        $opportunityStages = Opportunity::query()->with('stage')
            ->groupBy('stage_id')->selectRaw('COUNT(*) AS Total, stage_id')->get();
        $opportunityStagesSum = Opportunity::query()->with('stage')
            ->groupBy('stage_id')->selectRaw('SUM(expected_revenue) AS TotalRevenue, stage_id')->get();

        $totalRevenueBySalespersonThisMonth =
            Invoice::query()->selectRaw('SUM(grand_total) AS Total,owner_id')
                ->where('created_at', '>', now()->startOfMonth())
                ->groupBy('owner_id')->get()->each(function ($element) {
                    $element->owner = Cache::remember(
                        'ownerId#' . $element->owner_id,
                        3600,
                        function () use ($element) {
                            return User::query()->where('id', $element->owner_id)->select(
                                ['id', 'first_name', 'last_name'],
                            )->first();
                        },
                    );
                    unset($element->owner_id);
                });


        $totalRevenueBySalespersonPreviousMonth =
            Invoice::query()->selectRaw('SUM(grand_total) AS Total,owner_id')
                ->where('created_at', '>', now()->subMonth()->startOfMonth())
                ->where('created_at', '<', now()->subMonth()->endOfMonth())
                ->groupBy('owner_id')->get()->each(function ($element) {
                    $element->owner = Cache::remember(
                        'ownerId#' . $element->owner_id,
                        3600,
                        function () use ($element) {
                            return User::query()->where('id', $element->owner_id)->select(
                                ['id', 'first_name', 'last_name'],
                            )->first();
                        },
                    );
                    unset($element->owner_id);
                });


        $totalRevenueBySalespersonThisQuarter =
            Invoice::query()->selectRaw('SUM(grand_total) AS Total,owner_id')
                ->where('created_at', '>', now()->startOfQuarter())
                ->groupBy('owner_id')->get()->each(function ($element) {
                    $element->owner = Cache::remember(
                        'ownerId#' . $element->owner_id,
                        3600,
                        function () use ($element) {
                            return User::query()->where('id', $element->owner_id)->select(
                                ['id', 'first_name', 'last_name'],
                            )->first();
                        },
                    );
                    unset($element->owner_id);
                });


        $totalRevenueBySalespersonPreviousQuarter =
            Invoice::query()->selectRaw('SUM(grand_total) AS Total,owner_id')
                ->where('created_at', '>', now()->subQuarter()->startOfQuarter())
                ->where('created_at', '<', now()->subQuarter()->endOfQuarter())
                ->groupBy('owner_id')->get()->each(function ($element) {
                    $element->owner = Cache::remember(
                        'ownerId#' . $element->owner_id,
                        3600,
                        function () use ($element) {
                            return User::query()->where('id', $element->owner_id)->select(
                                ['id', 'first_name', 'last_name'],
                            )->first();
                        },
                    );
                    unset($element->owner_id);
                });


        $totalRevenueBySalespersonThisYear =
            Invoice::query()->selectRaw('SUM(grand_total) AS Total,owner_id')
                ->where('created_at', '>', now()->startOfYear())
                ->groupBy('owner_id')->get()->each(function ($element) {
                    $element->owner = Cache::remember(
                        'ownerId#' . $element->owner_id,
                        3600,
                        function () use ($element) {
                            return User::query()->where('id', $element->owner_id)->select(
                                ['id', 'first_name', 'last_name'],
                            )->first();
                        },
                    );
                    unset($element->owner_id);
                });

        $totalRevenueBySalespersonPreviousYear =
            Invoice::query()->selectRaw('SUM(grand_total) AS Total,owner_id')
                ->where('created_at', '>', now()->subYear()->startOfYear())
                ->where('created_at', '<=', now()->subYear()->endOfYear())
                ->groupBy('owner_id')->get()->each(function ($element) {
                    $element->owner = Cache::remember(
                        'ownerId#' . $element->owner_id,
                        3600,
                        function () use ($element) {
                            return User::query()->where('id', $element->owner_id)->select(
                                ['id', 'first_name', 'last_name'],
                            )->first();
                        },
                    );
                    unset($element->owner_id);
                });


        Cache::put(self::SALES_CACHE_KEY, [

            'opportunityStages' => [
                'count' => $opportunityStages,
                'sum' => $opportunityStagesSum,
            ],
            'totalRevenueMonthly' => [
                'lastYear' => [
                    'monthly' => $this->getTotalRevenueMonthly('last'),
                    'quarter' => $this->getTotalRevenueQuarterly('last'),
                ],
                'currentYear' => [
                    'monthly' => $this->getTotalRevenueMonthly(),
                    'quarter' => $this->getTotalRevenueQuarterly(),
                ],
            ],
            'totalRevenueBySalesPerson' => [
                'thisMonth' => $totalRevenueBySalespersonThisMonth,
                'prevMonth' => $totalRevenueBySalespersonPreviousMonth,
                'thisQuarter' => $totalRevenueBySalespersonThisQuarter,
                'prevQuarter' => $totalRevenueBySalespersonPreviousQuarter,
                'thisYear' => $totalRevenueBySalespersonThisYear,
                'prevYear' => $totalRevenueBySalespersonPreviousYear,
            ],
            'calculatedAt' => now(),
        ]);
    }


    private function getTotalRevenueMonthly(string $year = 'current'): Collection|array
    {
        if ($year === 'current') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
        } else {
            $startDate = now()->subYear()->startOfYear();
            $endDate = now()->subYear()->endOfYear();
        }

        return Invoice::query()->groupByRaw('MONTH(created_at)')
            ->selectRaw('SUM(grand_total) AS Total, MONTH(created_at) As Month')->where(
                'created_at',
                '>',
                $startDate,
            )->where('created_at', '<=', $endDate)->get();
    }

    private function getTotalRevenueQuarterly(string $year = 'current'): Collection|array
    {
        if ($year === 'current') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
        } else {
            $startDate = now()->subYear()->startOfYear();
            $endDate = now()->subYear()->endOfYear();
        }


        return Invoice::query()->groupByRaw('QUARTER(created_at)')
            ->selectRaw('SUM(grand_total) AS Total, QUARTER(created_at) As Quarter')
            ->where('created_at', '>', $startDate)->where('created_at', '<=', $endDate)->get();
    }


    public function calculateAccountStats(): void
    {
        $opportunityStagesSum = Opportunity::query()
            ->groupByRaw('MONTH(created_at)')->selectRaw(
                'SUM(expected_revenue) AS TotalRevenue, MONTH(created_at) As Month',
            )->get();


        Cache::put(self::ACCOUNT_CACHE_KEY, [
            'expectedRevenue' => $opportunityStagesSum,
            'calculatedAt' => now(),
        ]);
    }

    /**
     * @param string $interval
     * @param string $year
     * @return array
     * @throws CustomErrorException
     */
    private function getDateRange(string $interval, string $year): array
    {
        if ($year === 'current') {
            $endDate = now();
            $startDate = match ($interval) {
                self::INTERVAL_MONTH_TO_DATE => now()->startOfMonth(),
                self::INTERVAL_QUARTER_TO_DATE => now()->startOfQuarter(),
                self::INTERVAL_YEAR_TO_DATE => now()->startOfYear(),
                default => throw new CustomErrorException('Unknown interval'),
            };
        } else {
            switch ($interval) {
                case self::INTERVAL_MONTH_TO_DATE:
                    $startDate = now()->subYear()->startOfMonth();
                    $endDate = now()->subYear()->endOfMonth();
                    break;

                case self::INTERVAL_QUARTER_TO_DATE:
                    $startDate = now()->subYear()->startOfQuarter();
                    $endDate = now()->subYear()->endOfQuarter();
                    break;

                case self::INTERVAL_YEAR_TO_DATE:
                    $startDate = now()->subYear()->startOfYear();
                    $endDate = now()->subYear()->endOfYear();
                    break;

                default:
                    throw new CustomErrorException('Unknown interval');
            }
        }

        return [$startDate, $endDate];
    }
}
