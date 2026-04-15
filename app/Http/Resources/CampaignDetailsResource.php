<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'priority' => $this->priority,

            'start_date' => $this->start_date,
            'end_date' => $this->end_date,

            'location' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'radius' => $this->radius,
            ],


            'volunteers' => [
                'required' => $this->required_volunteers,
                'current' => $this->current_volunteers,
                'remaining' => $this->required_volunteers - $this->current_volunteers,
            ],

            'donations' => [
                'target' => $this->target_amount,
                'current' => $this->current_amount,
            ],

            'image' => $this->image,
            'video' => $this->video,

            'kpis' => $this->whenLoaded('Campaign_kpis', function () {
                return $this->Campaign_kpis->map(function ($kpi) {
                    return [
                        'id' => $kpi->id,
                        'name' => $kpi->name,
                        'type' => $kpi->type ?? null,
                        'target_value' => $kpi->target_value,
                        'current_value' => $kpi->current_value ?? 0,
                        'unit' => $kpi->unit,
                    ];
                });
            }),

            'progress' => [
                'volunteers' => $this->required_volunteers > 0
                    ? round(($this->current_volunteers / $this->required_volunteers) * 100, 1)
                    : 0,

                'donations' => $this->target_amount > 0
                    ? round(($this->current_amount / $this->target_amount) * 100, 1)
                    : 0,
            ],
        ];
    }
}
