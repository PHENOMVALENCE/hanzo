<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Rfq;
use App\Models\RfqAttachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class RfqService
{
    public function create(User $buyer, array $data, array $files = []): Rfq
    {
        $rfq = Rfq::create([
            'code' => 'RFQ-' . strtoupper(Str::random(8)),
            'buyer_id' => $buyer->id,
            'category_id' => $data['category_id'],
            'description' => $data['description'] ?? null,
            'specs' => $data['specs'] ?? null,
            'quantity' => $data['quantity'],
            'timeline_weeks' => $data['timeline_weeks'] ?? null,
            'delivery_country' => $data['delivery_country'] ?? null,
            'delivery_city' => $data['delivery_city'] ?? null,
            'status' => 'new',
        ]);

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $path = $file->store('rfq-attachments/' . $rfq->id, 'private');
                $rfq->attachments()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                ]);
            }
        }

        return $rfq;
    }

    public function assignFactory(Rfq $rfq, int $factoryId, User $admin): void
    {
        $rfq->update([
            'assigned_factory_id' => $factoryId,
            'assigned_admin_id' => $admin->id,
            'status' => 'assigned',
        ]);
    }
}
