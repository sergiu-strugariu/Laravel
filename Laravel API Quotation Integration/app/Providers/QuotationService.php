<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class QuotationService extends ServiceProvider
{
    protected mixed $fixedRate;
    protected mixed $ageGroups;

    public function __construct()
    {
        $this->fixedRate = config('quotation.fixed_rate');
        $this->ageGroups = config('quotation.age_groups');
    }

    public function calculateQuotation(array $ages, string $currencyId, string $startDate, string $endDate)
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $startDate);
        $endDate = Carbon::createFromFormat('Y-m-d', $endDate);
        $tripLength = $startDate->diffInDays($endDate) + 1;

        $total = 0;

        foreach ($ages as $age) {
            $ageLoad = $this->getAgeLoad((int)$age);

            if ($ageLoad === null) {
                throw new \InvalidArgumentException('One or more ages are out of range');
            }

            $total += $this->fixedRate * $ageLoad * $tripLength;
        }

        return [
            'total' => round($total),
            'currency_id' => $currencyId,
            'quotation_id' => rand(1, 1000),
        ];
    }

    private function getAgeLoad(int $age)
    {
        foreach ($this->ageGroups as $group) {
            if ($age >= $group['min'] && $age <= $group['max']) {
                return $group['load'];
            }
        }

        return null;
    }
}
