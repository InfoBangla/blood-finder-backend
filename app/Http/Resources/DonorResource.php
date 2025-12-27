<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class DonorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dateFrom = Carbon::parse($this->last_donation_date);
        $dateTo = Carbon::now();
        $dayDifference = $dateFrom->diff($dateTo)->days;
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'area' => config("areas.bn.{$this->area_id}"),
            'bloodGroup' => $this->blood_group,
            'lastDonationDate' => $this->last_donation_date,
            'daysRemaining' => $dayDifference >= 90 ? 0 : 90 - $dayDifference,
            'hash' => Crypt::encryptString($this->id)
        ];
    }
}
